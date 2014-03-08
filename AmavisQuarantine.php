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
include_once('AmavisAbstract.php');
class AmavisQuarantine extends AmavisAbstract
{

    protected $user_email = '';

    // DATABASE STUFF
    private $db_config; // array with db configuration
    protected $db_conn;   // db connection

    // AMAVIS 
    private $amavis_host = '';
    private $amavis_port = '';

    // constructor
    function __construct($db_config, $amavis_host, $amavis_port)
    {
        // call constructor of the super class:
        parent::__construct($db_config);

        $this->amavis_host = $amavis_host;
        $this->amavis_port = $amavis_port;

    }

    // show a list of quarantined emails
    function list_quarantines($start_index = 0, $rows_displayed = 20) {
        
        if (! is_resource($this->db_conn)) {
            $this->init_db();
        }


        $query = "
            SELECT
              UNIX_TIMESTAMP()-msgs.time_num AS age, SUBSTRING(policy,1,2) as pb,
              msgs.content AS content, dsn_sent AS dsn, 
              ds AS delivery_status, bspam_level AS level, size,
              SUBSTRING(sender.email,1,40) AS sender,
              SUBSTRING(recip.email,1,40)  AS recipient,
              SUBSTRING(msgs.subject,1,40) AS subject,
              msgs.mail_id AS id
              FROM msgs LEFT JOIN msgrcpt              ON msgs.mail_id=msgrcpt.mail_id
                        LEFT JOIN maddr      AS sender ON msgs.sid=sender.id
                        LEFT JOIN maddr      AS recip  ON msgrcpt.rid=recip.id
                        LEFT JOIN quarantine AS quar   ON quar.mail_id = msgs.mail_id
              WHERE msgs.content IS NOT NULL 
              AND msgs.quar_type = 'Q'
              AND recip.email = ?
              ORDER BY msgs.time_num DESC";


        // prepare statement and execute
        $res = $this->db_conn->limitquery($query, $start_index, $rows_displayed, $this->user_email);
        if($this->db_error) {
            return "Error in selecting quarantined E-Mails: ".$this->db_error();
        }
        // write the first result line to settings array
        $ret_array = array();
        $index = 0;
        while ($res && ($res_array = $this->db_conn->fetch_assoc($res))) {
            $ret_array[ $index ] = $res_array;
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
        error_log('AMACUBE: delete mails: '.implode(',',$mails));
        if (! is_array($mails)) {
            // FIXME: throw error
            return false;
        }
        elseif (count($mails) < 1) {
            // empty array, all is good
            return true;
        }

        if (! is_resource($this->db_conn)) {
            $this->init_db();
        }
        
        // the dynamic parts of the quaery are table name (does not work with prepared statements)
        // and the number of question marks.
        $query_start = 'DELETE FROM ';
        $query_end = ' WHERE mail_id in ('.implode(',',array_fill(0, count($mails), '?')).')';
        $error = '';
        foreach(array('quarantine', 'msgrcpt', 'msgs') as $table) {
            $res = $this->db_conn->query($query_start.$table.$query_end, $mails); 
            if($this->db_error) {
                $error .= "Error in deleting from $table ".$this->db_error();
            }
        }
        if($error) {
            //FIXME: throw error
            error_log('AMACUBE delete error: '.$error);
            return $error;
        }
        return false;
    }

    /**
    * bool release(array)
    * connects to the amavi sdaemon and requests release of a list of emails
    * calls delete on success
    */
    function release($mails) {
        if (! is_array($mails)) {
            // FIXME throw error
            return false;
        }
        elseif(count($mails) <= 0) {
            return true;
        }

        if (! is_resource($this->db_conn)) {
            $this->init_db();
        }

        // check mail_ids and secret_ids from database:
        $query = 'select mail_id,secret_id,quar_type from msgs where mail_id in ('.implode(',',array_fill(0, count($mails), '?')).')';

        // prepare statement and execute
        $res = $this->db_conn->query($query, $mails);
        if($this->db_error) {
            return "Error in selecting quarantined E-Mails for release: ".$this->db_error();
        }
        // create an array of commands to submit to amavis release:
        $commands = array();
        while ($res && ($res_array = $this->db_conn->fetch_assoc($res))) {
            $command  = '';
            $command .= "request=release\r\n";
            $command .= "mail_id=".$res_array['mail_id']."\r\n";
            $command .= "secret_id=".$res_array['secret_id']."\r\n";
            $command .= "quar_type=".$res_array['quar_type']."\r\n";
            $command .= "requested_by=".$this->user_email ."%20via%20amacube\r\n";
            $command .= "\r\n";
            $commands[$res_array['mail_id']] = $command;
        }
        error_log("AMACUBE: command array: ".implode(',',str_replace("\r\n","_CR_NL_",$commands)));

        // two arrays to store success and error ids
        $success_ids = array();
        $error_ids = array();

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
                    //FIXME throw error
                    error_log("AMACUBE: release: write to socket failed");
                }

                error_log("AMACUBE: amavis said: ".str_replace("\r\n","_CR_NL_",$answer));
            }
            fclose ($fp);
        }
        else {
            //FIXME throw error
            error_log("AMACUBE socket open failed: $errstr ($errno)\n");
        }
            
        // successfully released emails can be deleted
        // errors logged FIXME
        $this->delete($success_ids);
        if(count($error_ids) > 0) {
            //FIXME error
            error_log("AMACUBE release error responses". http_build_query($error_ids));
        }


        if($error) {
            // FIXME throw error
            error_log('AMACUBE release error: '.$error);
            return $error;
        }
        return false;
    }
}
?>
