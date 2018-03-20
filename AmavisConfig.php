<?php
/**
* AmavisConfig - class to load and store Amavis settings in DB
*/

/*
This file is part of the amacube Roundcube plugin
Copyright (C) 2013, Alexander Köb <nerdkram@koeb.me>

Licensed under the GNU General Public License version 3.
See the COPYING file for a full license statement.

*/
include_once('AmacubeAbstract.php');
class AmavisConfig extends AmacubeAbstract
{
	// DB config
    private 	$db_config; 						// Store DB config
    protected 	$db_conn;   						// DB connection
    // User config
    public 		$initialized			= false;	// User record exists in database
    public 		$user_pk; 							// Primary key for the user record
    private 	$priority 				= 7;		// We do not change the amavis default for that
    public 		$fullname; 							// Full Name of the user, for reference, Amavis does not use that

    // Policy config
    public 		$policy_pk; 						// primary key of the policy record
    public 		$policy_name; 						// Name of the policy, for reference, Amavis does not use that
    public 		$policy_setting 		= array(
        'virus_lover' 					=> false,   // bool
        'spam_lover' 					=> false,   // bool
        'unchecked_lover' 				=> false,   // bool
        'banned_files_lover' 			=> false,  	// bool
        'bad_header_lover' 				=> false,   // bool
        'bypass_virus_checks' 			=> false, 	// bool
        'bypass_spam_checks' 			=> false,  	// bool
        'bypass_banned_checks' 			=> false,	// bool
        'bypass_header_checks' 			=> false,	// bool
//        'spam_modifies_subj' 			=> false,  	// bool
        'spam_tag_level' 				=> -999,    // float
        'spam_tag2_level' 				=> 7,       // float
        'spam_tag3_level' 				=> 7,      // float
        'spam_kill_level' 				=> 7,      // float
        'spam_dsn_cutoff_level' 		=> 10,  	// float
        'spam_quarantine_cutoff_level' 	=> 20, 		// float

        'virus_quarantine_to' 			=> true,    // string 'sql:', but treated as boolean
        'spam_quarantine_to' 			=> true,   // string 'sql:', but treated as boolean
        'banned_quarantine_to' 			=> true,	// string 'sql:', but treated as boolean
        'bad_header_quarantine_to' 		=> true,   	// string 'sql:', but treated as boolean

        'unchecked_quarantine_to' 		=> false,   // string 'sql:', but treated as boolean
        'clean_quarantine_to' 			=> false,   // string 'sql:', but treated as boolean
        'archive_quarantine_to' 		=> false,   // string 'sql:', but treated as boolean
    );

    // class variables(static), the same in all instances:
    private static $boolean_settings = array(
        'virus_lover',
        'spam_lover',
        'unchecked_lover',
        'banned_files_lover',
        'bad_header_lover',
        'bypass_virus_checks',
        'bypass_spam_checks',
        'bypass_banned_checks',
        'bypass_header_checks',
//        'spam_modifies_subj',
    );
    private static $tosql_settings = array(
        'virus_quarantine_to',
        'spam_quarantine_to',
        'banned_quarantine_to',
        'bad_header_quarantine_to',
        'unchecked_quarantine_to',
        'clean_quarantine_to',
        'archive_quarantine_to'
	);
    /*
    The following settings are unused, I added the lines for later implementation if needed

    addr_extension_virus; // unused
    addr_extension_spam; // unused
    addr_extension_banned; // unused
    addr_extension_bad_header; // unused
    warnvirusrecip; // unused
    warnbannedrecip; // unused
    warnbadhrecip; // unused
    newvirus_admin; // unused
    virus_admin; // unused
    banned_admin; // unused
    bad_header_admin; // unused
    spam_admin; // unused
    spam_subject_tag; // unused
    spam_subject_tag2; // unused
    spam_subject_tag3; // unused
    message_size_limit; // unused
    banned_rulenames; // unused
    disclaimer_options; // unused
    forward_method; // unused
    sa_userconf; // unused
    sa_username; // unused
    */

    // Constructor
    function __construct($db_config)
    {
        // Call constructor of the super class
        parent::__construct($db_config);
		// Check for account catchall and adjust user_email accordingly
        if (isset($this->rc->amacube->catchall) && $this->rc->amacube->catchall == true) {
        	$this->user_email	= substr(strrchr($this->user_email,"@"),0);
        }
        // Read config from DB
        $this->initialized 	= $this->read_from_db();
		// Verify policy config from database
        if ($this->initialized) {
        	$this->verify_policy_array();
        }
    }

    // Method for verifying policy config
    function verify_policy_array($array = null)
    {
    	$errors = array();
        // Check specified or current policy config
        if (!isset($array) || !is_array($array) || count($array) == 0) { $array = $this->policy_setting; }
        // Check bools
        if (is_bool($array['virus_lover']) === false) { array_push($errors, 'virus_lover'); }
        if (is_bool($array['spam_lover']) === false) { array_push($errors, 'spam_lover'); }
        if (is_bool($array['unchecked_lover']) === false) { array_push($errors, 'unchecked_lover'); }
        if (is_bool($array['banned_files_lover']) === false) { array_push($errors, 'banned_files_lover'); }
        if (is_bool($array['bad_header_lover']) === false) { array_push($errors, 'bad_header_lover'); }
        if (is_bool($array['bypass_virus_checks']) === false) { array_push($errors, 'bypass_virus_checks'); }
        if (is_bool($array['bypass_spam_checks']) === false) { array_push($errors, 'bypass_spam_checks'); }
        if (is_bool($array['bypass_banned_checks']) === false) { array_push($errors, 'bypass_banned_checks'); }
        if (is_bool($array['bypass_header_checks']) === false) { array_push($errors, 'bypass_header_checks'); }
//        if (is_bool($array['spam_modifies_subj']) === false) { array_push($errors, 'spam_modifies_subj'); }
        if (is_bool($array['virus_quarantine_to']) === false) { array_push($errors, 'virus_quarantine_to'); }
        if (is_bool($array['spam_quarantine_to']) === false) { array_push($errors, 'spam_quarantine_to'); }
        if (is_bool($array['banned_quarantine_to']) === false) { array_push($errors, 'banned_quarantine_to'); }
		if (is_bool($array['bad_header_quarantine_to']) === false) { array_push($errors, 'bad_header_quarantine_to'); }
		// Check numerics
        if (is_numeric($array['spam_tag_level']) === false) { array_push($errors, 'spam_tag_level:'.$array['spam_tag_level']."___".gettype($array['spam_tag_level'])); }
        if (is_numeric($array['spam_tag2_level']) === false) { array_push($errors, 'spam_tag2_level'); }
        if (is_numeric($array['spam_tag3_level']) === false) { array_push($errors, 'spam_tag3_level'); }
        if (is_numeric($array['spam_kill_level']) === false) { array_push($errors, 'spam_kill_level'); }
        if (is_numeric($array['spam_dsn_cutoff_level']) === false) { array_push($errors, 'spam_dsn_cutoff_level'); }
        if (is_numeric($array['spam_quarantine_cutoff_level']) === false) { array_push($errors, 'spam_quarantine_cutoff_level'); }
        // Check unknown keys
        foreach ($array as $key => $value) {
            if (!array_key_exists($key, $this->policy_setting)) { array_push($errors, 'unknown:'.$key); }
        }
		// Return false on errors
        if (!empty($errors)) {
        	$this->rc->amacube->errors[] = 'db_policy_error';
			rcube::write_log('errors',"AMACUBE: Database policy error: ".implode(',',$errors));
        	return false;
		}
		// Return true
		return true;

    }



    // manually set amavis settings, either from config or from POST request
    function set_policy($array)
    {
        // Verify policy array
        if (!$this->verify_policy_array($array)) {
            return false;
        } else {
	        // Write policy array to instance variable
	        $this->policy_setting = $array;
			return true;
        }
    }

    // read amavis settings from database
    function read_from_db()
    {
        if (!is_resource($this->db_conn)) {
        	if (!$this->init_db()) { return false; }
		}
        // Get query for user and policy config
        $query = "SELECT users.id as user_id, users.priority, users.email, users.fullname, policy.*
            FROM users, policy
            WHERE users.policy_id = policy.id
            AND users.email = ? ";
        $res = $this->db_conn->query($query, $this->user_email);
		// Error check
        if ($error = $this->db_conn->is_error()) {
			$this->rc->amacube->errors[] = 'db_query_error';
			rcube::write_log('errors','AMACUBE: Database query error: '.$error);
		}
		// Get record for user and map policy config
        if ($res && ($res_array = $this->db_conn->fetch_assoc($res))) {
            foreach ($this->policy_setting as $key => $value) {
                $this->policy_setting[$key] = $this->map_from_db($key, $res_array[$key]);
            }
            $this->user_pk = $res_array['user_id'];
            $this->priority = $res_array['priority'];
            $this->fullname = $res_array['fullname'];
            $this->policy_pk = $res_array['id'];
            $this->policy_name = $res_array['policy_name'];
			return true;
        }
		return false;
    }
    // write settings back to database
    function write_to_db()
    {
        if (!is_resource($this->db_conn)) {
        	if (!$this->init_db()) { return false; }
		}
        $query 			= ''; 			// store the mysql query
        $query_params 	= array();

        // Check for PK for update or insert
        if (!empty($this->policy_pk)) {
        	// Update policy
            $query = 'UPDATE policy SET ';
            $keys = array_keys($this->policy_setting);
            $max = sizeof($keys);
            for ($i = 0; $i < $max; $i++) {
                $query .= $keys[$i] .' = ? ';
                array_push($query_params, $this->map_to_db($keys[$i], $this->policy_setting[$keys[$i]]));
                if ($i < $max - 1 ) {
                    $query .= ', ';
                }
            }
            $query .= ' WHERE id = ? ';
            array_push($query_params, $this->policy_pk);
        } else {
            // Insert policy
            $keys = array_keys($this->policy_setting);
            array_push($keys, 'policy_name');
            $max = sizeof($keys);
            $query = 'INSERT INTO policy (';
            $query .= implode(',', $keys);
            $query .= ') VALUES (';
            for ($i = 0; $i < $max; $i++) {
                $query .= '?';
                if ($i < $max - 1 ) {
                    $query .= ', ';
                }
            }
            $query .= ')';
            foreach ($keys as $k) {
                if ($k == 'policy_name') {
                    array_push($query_params,'policy_user_'.$this->user_email);
                }
                else {
                    array_push($query_params, $this->map_to_db($k,$this->policy_setting[$k]));
                }
            }
        }
        $res = $this->db_conn->query($query, $query_params);
		// Error check
        if ($error = $this->db_conn->is_error()) {
			$this->rc->amacube->errors[] = 'db_query_error';
			rcube::write_log('errors','AMACUBE: Database query error: '.$error);
			return false;
		}
        // Check for user on insert policy
        if (empty($this->policy_pk)) {
            $this->policy_pk = $this->db_conn->insert_id();
            // Error check
            if (empty($this->policy_pk)) {
            	$this->rc->amacube->errors[] = 'db_query_error';
				rcube::write_log('errors','AMACUBE: Database query error: '.$this->db_conn->is_error());
				return false;
            }
			// Check for user
            $res = $this->db_conn->query('SELECT id from users where email = ? ', $this->user_email);
			// Error check
	        if ($error = $this->db_conn->is_error()) {
				$this->rc->amacube->errors[] = 'db_query_error';
				rcube::write_log('errors','AMACUBE: Database query error: '.$error);
				return false;
			}
            if ($res && ($res_array = $this->db_conn->fetch_assoc($res))) {
                // Update user
                $this->user_pk = $res_array['id'];
                $res2 = $this->db_conn->query("UPDATE users set policy_id = ? WHERE id = ?", $this->policy_pk, $this->user_pk);
            }
            else {
                // Insert user
                $res2 = $this->db_conn->query("INSERT INTO users (policy_id, email) VALUES (?,?)", $this->policy_pk, $this->user_email);
            }
			// Error check
	        if ($error = $this->db_conn->is_error()) {
				$this->rc->amacube->errors[] = 'db_query_error';
				rcube::write_log('errors','AMACUBE: Database query error: '.$error);
				return false;
			}
        }
        return true;
    }
    // Convenience methods
    function is_delivery($type,$method) {

		if ($type == 'banned') { $lover = $type.'_files_lover'; }
		else { $lover = $type.'_lover'; }

		if ($method == 'deliver' && $this->policy_setting[$lover]) { return true; }
		if ($method == 'quarantine' && !$this->policy_setting[$lover] && $this->policy_setting[$type.'_quarantine_to']) { return true; }
		if ($method == 'discard' && !$this->policy_setting[$lover] && !$this->policy_setting[$type.'_quarantine_to']) { return true; }
		return false;

    }

	function is_active($type) {

		if ($type == 'virus' || $type == 'spam') {
			return !$this->policy_setting['bypass_'.$type.'_checks'];
		}
		return false;
	}

    // Mapping function for internal representation -> database content
    function map_to_db($key, $value)
    {
        $retval = null;
        // Map boolean settings to Y/N
        if (in_array($key, self::$boolean_settings)) {
            if ($value) { $retval = 'Y'; }
            else { $retval = 'N'; }
        }
        // Map tosql settings to sql:/null
		elseif (in_array($key, self::$tosql_settings)) {
            if ($value) { $retval = 'sql:'; }
            else { $retval = null; }
        } else {
        	// No mapping needed for other settings
            $retval = $value;
        }
        return $retval;
    }

    // Mapping function for internal representation <- database content
    function map_from_db($key, $value)
    {
        $retval = null;
        // Map boolean settings from Y/N
        if (in_array($key, self::$boolean_settings)) {
            if (!empty($value) && $value == 'Y') { $retval = true; }
            else { $retval = false; }
        }
        // Map tosql settings from sql:/null
        elseif (in_array($key, self::$tosql_settings)) {
            if (!empty($value) && $value == 'sql:') { $retval = true; }
            else { $retval = false; }
        } else {
        	// No mapping needed for other settings
            $retval = $value;
        }
        return $retval;
    }
}
?>
