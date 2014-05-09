 <?php
/**
* AmavisQuarantine - class to load, delete or release Amavis quarantined emails in/from DB
*/

/*
This file is part of the amacube Roundcube plugin
Copyright (C) 2013, Alexander Köb <nerdkram@koeb.me>

Licensed under the GNU General Public License version 3.
See the COPYING file for a full license statement.          

*/
include_once('AmacubeAbstract.php');
class AmavisQuarantine extends AmacubeAbstract
{
    // Amavis 
    private $amavis_host = '';
    private $amavis_port = '';
	
    // Constructor
    function __construct($db_config, $amavis_host, $amavis_port)
    {
        // Call constructor of the super class
        parent::__construct($db_config);
		// Apply amavis database settings
        $this->amavis_host = $amavis_host;
        $this->amavis_port = $amavis_port;

    }

    // Returns a list of (all) quarantined emails
    function list_quarantines($start_index = 0, $rows_displayed = 0) {
        
        if (!is_resource($this->db_conn)) {
        	if (!$this->init_db()) { return false; }
		}
        $query = "
            SELECT
              UNIX_TIMESTAMP()-msgs.time_num AS age, 
              msgs.time_num AS received, 
              SUBSTRING(policy,1,2) as pb,
              msgs.content AS content, 
              dsn_sent AS dsn, 
              ds AS delivery_status, 
              bspam_level AS level, 
              size,
              SUBSTRING(sender.email,1,40) AS sender,
              SUBSTRING(recip.email,1,40)  AS recipient,
              SUBSTRING(msgs.subject,1,40) AS subject,
              msgs.mail_id AS id
              FROM msgs LEFT JOIN msgrcpt              ON msgs.mail_id=msgrcpt.mail_id
                        LEFT JOIN maddr      AS sender ON msgs.sid=sender.id
                        LEFT JOIN maddr      AS recip  ON msgrcpt.rid=recip.id
                        LEFT JOIN quarantine AS quar   ON quar.mail_id = msgs.mail_id
              WHERE msgs.content IS NOT NULL 
              AND msgs.quar_type = 'Q'";
        if ($this->catchall) {
        	$id		= '@'.$this->user_domain;
        	$query .= "AND recip.email LIKE ?";
        } else {
        	$id		= $this->user_email;
        	$query .= "AND recip.email = ?";
        }
		$query .= " ORDER BY msgs.time_num DESC";
        // prepare statement and execute
        if ($start_index == 0 && $rows_displayed == 0) {
        	// Get all quarantines
        	$res = $this->db_conn->query($query, $id);
        } else {
        	// Get specified quarantines
        	$res = $this->db_conn->limitquery($query, $start_index, $rows_displayed, $id);
        }
		// Error check
        if ($error = $this->db_conn->is_error()) {
			$this->errors[] = 'db_query_error';
			write_log('errors','AMACUBE: Database query error: '.$error);
			return false;
		}
        // Write the first result line to settings array
        $ret_array 	= array();
        $index 		= 0;
        while ($res && ($res_array = $this->db_conn->fetch_assoc($res))) {
            $ret_array[$index] = $res_array;
            $index ++;
        }
        return $ret_array;

    }

    /**
    * bool delete(array)
    * deletes an array of mail_ids from quarantine including the
    * relevant entries of following tables:
    * - quarantine
    * - msgs
    * - msgrcpt
    * The entries of the table maddr is left for the cleanup job
    */
    function delete($mails) {

        if (!is_resource($this->db_conn)) {
            if (!$this->init_db()) { return false; }
        }
        //write_log('errors','AMACUBE: Delete: '.implode(',',$mails));
		
        if (is_array($mails)) {
 	        if (count($mails) < 1) { return true; }
	        $query_start = 'DELETE FROM ';
	        $query_end = ' WHERE mail_id in ('.implode(',',array_fill(0, count($mails), '?')).')';
	        foreach(array('quarantine', 'msgrcpt', 'msgs') as $table) {
	            $res = $this->db_conn->query($query_start.$table.$query_end, $mails); 
				// Error check
		        if ($error = $this->db_conn->is_error()) {
					$this->errors[] = 'db_delete_error';
					write_log('errors','AMACUBE: Delete: Database error: '.$error);
				}
	        }
		}
        if ($error) { return false; }
        return count($mails);
    }

    /**
    * bool release(array)
    * connects to the amavi sdaemon and requests release of a list of emails
    * calls delete on success
    */
    function release($mails) {
    	
        if (!is_resource($this->db_conn)) {
        	if (!$this->init_db()) { return false; }
		}
        //write_log('errors','AMACUBE: Release: '.implode(',',$mails));
        if (is_array($mails)) {
			if (count($mails) <= 0) { return true; }
	        // Check mail_ids and secret_ids from database
	        $query 			= 'select mail_id,secret_id,quar_type from msgs where mail_id in ('.implode(',',array_fill(0, count($mails), '?')).')';
	        $res 			= $this->db_conn->query($query, $mails);
			$error			= false;
			// Error check
	        if ($error = $this->db_conn->is_error()) {
				$this->errors[] = 'Database query error.';
				write_log('errors','AMACUBE: Release: Database error: '.$error);
				return false;
			}
	        // Create array of commands to submit to amavis release
	        $commands 		= array();
	        while ($res && ($res_array = $this->db_conn->fetch_assoc($res))) {
	            $command 	= '';
	            $command 	.= "request=release\r\n";
	            $command 	.= "mail_id=".$res_array['mail_id']."\r\n";
	            $command 	.= "secret_id=".$res_array['secret_id']."\r\n";
	            $command 	.= "quar_type=".$res_array['quar_type']."\r\n";
	            $command 	.= "requested_by=".$this->user_email ."%20via%20amacube\r\n";
	            $command 	.= "\r\n";
	            $commands[$res_array['mail_id']] = $command;
	        }
        	//write_log('errors','AMACUBE: Release: Command array: '.implode(',',str_replace("\r\n","_CR_NL_",$commands)));
	        $success_ids 	= array();
	        $error_ids 		= array();

	        // open socket to amavis process and print that command:
	        $fp = @fsockopen($this->amavis_host, $this->amavis_port, $errno, $errstr, 5);
	        if ($fp) {
	            stream_set_timeout($fp,5);
	            foreach($commands as $mail_id => $command) {
	                if (fwrite($fp, $command)) {
	                    $answer = 'New answer after '.$command;
	                    while (!feof($fp)) {
	                        $result = fgets($fp);
	                        $answer .= $result;
	                        if(substr($result,0,12) === 'setreply=250') {
	                            // save success result in commands array
	                            array_push($success_ids,$mail_id);
	                        }
	                        elseif($result == "\r\n") {
	                            // server answered, and waits for more commands
	                            break;
	                        }
	                        else {
	                            // error response
	                            $error_ids[$mail_id] = $result;
	                        }
	                    }
	                }
	                else {
						// Error check
						$this->errors[] = $error = 'release_error';
						write_log('errors','AMACUBE: Release: Socket write error');
	                }
	                //write_log('errors','AMACUBE: Amavis said: '.str_replace("\r\n","_CR_NL_",$answer));
	            }
	            fclose ($fp);
	        } else {
				// Error check
				$this->errors[] = $error = 'release_error';
	            write_log('errors',"AMACUBE: Release: Socket open error: $errstr ($errno)\n");
	        }
	        $this->delete($success_ids);
	        if (count($error_ids) > 0) {
				// Error check
				$this->errors[] = $error = 'release_error';
	            write_log('errors','AMACUBE: Release: Error responses: '. http_build_query($error_ids));
	        }
			if ($error) { return false; }
	        return count($mails);
		}
		return false;
    }
}

?>
