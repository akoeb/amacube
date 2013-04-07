<?php
class AmavisSettings
{
    // USER SETTINGS
    public $user_pk; // primary key for the user record
    private $priority = 7; // we do not change the amavis default for that
    public $user_email; // email address of the user
    public $fullname; // Full Name of the user, for reference, Amavis does not use that  

    // POLICY SETTINGS
    public $policy_pk; // primary key of the policy record
    public $policy_name; // Name of the policy, for reference, Amavis does not use that
    public $policy_settings = array(
        'virus_lover' => false,         // bool
        'spam_lover' => false,          // bool
        'unchecked_lover' => false,     // bool
        'banned_files_lover' => false,  // bool
        'bad_header_lover' => false,    // bool
        'bypass_virus_checks' => false, // bool
        'bypass_spam_checks' => false,  // bool
        'bypass_banned_checks' => false,// bool
        'bypass_header_checks' => false,// bool
        'spam_modifies_subj' => false,  // bool
        'spam_tag_level' => -999,       // float
        'spam_tag2_level' => 6,         // float
        'spam_tag3_level' => 12,        // float
        'spam_kill_level' => 12,        // float
        'spam_dsn_cutoff_level' => 12,  // float
        'spam_quarantine_cutoff_level' => 50, // float
 
        'virus_quarantine_to' => '',    // string 'sql:', but treated as boolean
        'spam_quarantine_to' => '',     // string 'sql:', but treated as boolean
        'banned_quarantine_to' => '',       // unused
        'unchecked_quarantine_to' => '',    // unused
        'bad_header_quarantine_to' => '',   // unused
        'clean_quarantine_to' => '',        // unused
        'archive_quarantine_to' => '',      // unused
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
            'spam_modifies_subj',
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


    // DATABASE STUFF
    private $db_config; // array with db configuration
    private $db_conn;   // db connection



    // constructor
    function __construct($db_config)
    {
        // set the DB connection config
        $this->db_config = $db_config;

        // This plugin assumes the the username equals the email address we want to 
        // have amavis checking for
        $rcmail = rcmail::get_instance();
        $this->user_email = $rcmail->user->data['username'];

        // read everything from db if we have records there
        $this->read_from_db();
    }
    
    // method to verify the policy settings are correct
    function verify_policy_array($array)
    {
        // store the errors
        $errors = array();

        // check the booleans:
        if(empty($array['virus_lover']) || 
           ! is_bool($array['virus_lover']))
        {
            array_push($errors, 'virus_lover');
        }
        if(empty($array['spam_lover']) ||
           ! is_bool($array['spam_lover']))
        {
            array_push($errors, 'spam_lover');
        }

        if(empty($array['unchecked_lover']) ||
           ! is_bool($array['unchecked_lover']))
        {
            array_push($errors, 'unchecked_lover');
        }

        if(empty($array['banned_files_lover']) ||
           ! is_bool($array['banned_files_lover']))
        {
            array_push($errors, 'banned_files_lover');
        }

        if(empty($array['bad_header_lover']) ||
           ! is_bool($array['bad_header_lover']))
        {
            array_push($errors, 'bad_header_lover');
        }

        if(empty($array['bypass_virus_checks']) ||
           ! is_bool($array['bypass_virus_checks']))
        {
            array_push($errors, 'bypass_virus_checks');
        }

        if(empty($array['bypass_spam_checks']) ||
           ! is_bool($array['bypass_spam_checks']))
        {
            array_push($errors, 'bypass_spam_checks');
        }

        if(empty($array['bypass_banned_checks']) ||
           ! is_bool($array['bypass_banned_checks']))
        {
            array_push($errors, 'bypass_banned_checks');
        }

        if(empty($array['bypass_header_checks']) ||
           ! is_bool($array['bypass_header_checks']))
        {
            array_push($errors, 'bypass_header_checks');
        }

        if(empty($array['spam_modifies_subj']) ||
           ! is_bool($array['spam_modifies_subj']))
        {
            array_push($errors, 'spam_modifies_subj');
        }

        // check the floats:
        if(empty($array['spam_tag_level']) ||
           ! is_numeric($array['spam_tag_level']))
        {
            array_push($errors, 'spam_tag_level');
        }

        if(empty($array['spam_tag2_level']) ||
           ! is_numeric($array['spam_tag2_level']))
        {
            array_push($errors, 'spam_tag2_level');
        }

        if(empty($array['spam_tag3_level']) ||
           ! is_numeric($array['spam_tag3_level']))
        {
            array_push($errors, 'spam_tag3_level');
        }
        
        if(empty($array['spam_kill_level']) ||
           ! is_numeric($array['spam_kill_level']))
        {
            array_push($errors, 'spam_kill_level');
        }
        
        if(empty($array['spam_dsn_cutoff_level']) ||
           ! is_numeric($array['spam_dsn_cutoff_level']))
        {
            array_push($errors, 'spam_dsn_cutoff_level');
        }
        
        if(empty($array['spam_quarantine_cutoff_level']) ||
           ! is_numeric($array['spam_quarantine_cutoff_level']))
        {
            array_push($errors, 'spam_quarantine_cutoff_level');
        }

        if(empty($array['virus_quarantine_to']) ||
           ! is_bool($array['virus_quarantine_to']))
        {
            array_push($errors, 'virus_quarantine_to');
        }

        if(empty($array['spam_quarantine_to']) ||
           ! is_bool($array['spam_quarantine_to']))
        {
            array_push($errors, 'spam_quarantine_to');
        }



        // make sure the array does not contain any other keys
        foreach($array as $key => $value) {
            if (! array_key_exists($key, $this->policy_settings)) {
                // unkonwn key found
                array_push($errors, 'unknown:'.$key);
            }
        }

        // return if error found:
        if(! empty ($error)) {
            return $error;
        }
    }



    // manually set amavis settings, either from config or from POST request
    function set_policy($array)
    {
        // verify the array is correct
        $error = $this->verify_policy_array($array);
        if(! empty ($error)) {
            return $error;
        }
        // and set write to instance variable
        $this->policy_settings = $array;
    }

    // initialize the database connection
    function init_db()
    {
        // initialize a persistent DB connection
        if (!$this->db_conn) {
            // pre 0.9
            if (!class_exists('rcube_db')) {
                $this->db_conn = new rcube_mdb2($this->db_config, '', TRUE);
            } 
            // version 0.9
            else {
                $this->db_conn = rcube_db::factory($this->db_config, '', TRUE);
            }

        }
        $this->db_conn->db_connect('w');

        // check DB connections and exit on failure
        if ($err_str = $this->db_conn->is_error()) {
            raise_error(array(
            'code' => 603,
            'type' => 'db',
            'message' => $err_str), true, true);
        }
    }

    // read amavis settings from database
    function read_from_db() 
    {
        if (! is_resource($this->db_conn)) {
            $this->init_db();
        }

        // check whether we have a db connection
        $query = 'SELECT users.id as user_id, users.priority, users.email, users.fullname, 
                         policy.*
            FROM users, policy
            WHERE users.policy_id = policy.id 
            AND users.email = ? ';

        // prepare statement and execute
        $res = $this->db_conn->query($query, $this->user_email);

        // write the first result line to settings array
        if ($res && ($res_array = $this->db_conn->fetch_assoc($res))) {
            // read all keys of policy_settings array
            foreach ($this->policy_settings as $key => $value) {
                $this->policy_setting[$key] = $this->map_from_db($key, $res_array[$key]);
            }
            $this->user_pk = $res_array['user_id'];
            $this->priority = $res_array['priority'];
            $this->fullname = $res_array['fullname'];
            $this->policy_pk = $res_array['id'];
            $this->policy_name = $res_array['policy_name'];
        }

    }
    // write settings back to database
    // FIXME: this method must return an error string in casesomething fails
    function write_to_db()
    {
        if (! is_resource($this->db_conn)) {
            $this->init_db();
        }
        $query = ''; // store the mysql query
        $query_params = array();

        // if we have a primary key, the row exists already in the database
        if(! empty($this->policy_pk)) {
            $query = 'UPDATE policy SET ';
            $keys = array_keys($this->policy_settings);
            $max = sizeof($keys);
            for ($i = 0; $i < $max; $i++) {
                $query .= $keys[$i] .' = ? ';
                array_push($query_params, $this->map_to_db($key, $key[$i]);
                if($i < $max - 1 ) {
                    $query .= ', ';
                }
            }
            $query .= ' WHERE id = ? ';
            array_push($query_params, $this->policy_pk);
        }
        // no PK, insert
        else {
            // we insert the policy row first, the user row comes later
            $keys = array_keys($this->policy_settings);
            $query = 'INSERT INTO policy (';
            $query .= implode(',', $keys);
            $query .= ') VALUES (';
            for($i = 0; $i < $max; $i++) {
                $query .= '?';
                if($i < $max - 1 ) {
                    $query .= ', ';
                }
            }
            foreach($keys as $k => $v) {
                array_push($query_params, $this->map_to_db($k, $v);
            }
        }
        $res = $this->db_conn->query($query, $query_params);
        // TODO: error check

        // in case this was an insert, read policy_pk and insert user as well if needed 
        if(empty($this->policy_pk)) {
            $this->policy_pk = $this->db_conn->lastInsertID();
            // TODO: error check
            
            // now that we have the policy pk, we check 
            // whether we need to insert or update the user as well
            $res = $this->db_conn->query(
                'SELECT id from users where email = ? ', 
                $this->user_email);

            if ($res && ($res_array = $this->db_conn->fetch_assoc($res))) {
                // we need to update:
                $this->user_pk = $res_array['id'];
                $res2 = $this->db_conn->query(
                    'UPDATE users set policy_id = ? WHERE id = ?',
                    $this->policy_pk, $this->user_pk);
                // TODO: error check
            }
            else {
                // INSERT user as well
                $res2 = $this->db_conn->query(
                    'INSERT INTO users (policy_id, email) VALUES (?,?))'
                    $this->policy_pk, $this->user_email);
                // TODO: error check
            }
        }
    }


    // CONVENIENCE METHODS:
    // set the checkbox checked mark if user is a spam lover
    function is_spam_check_activated_checkbox()
    {
        if ($this->policy_settings['spam_lover']) {
            // true means unchecked activation...
            return false;
        }
        return true;
    }
    // set the checkbox checked mark if user is a virus lover
    function is_virus_check_activated_checkbox()
    {
        if ($this->policy_settings['virus_lover']) {
            // true means unchecked activation...
            return false;
        }
        return true;
    }
    function is_spam_quarantine_activated_checkbox()
    {
        if($this->policy_settings['spam_quarantine_to']) {
            return true;
        }
        return false;
    }

    function is_virus_quarantine_activated_checkbox()
    {
        if($this->policy_settings['virus_quarantine_to']) {
            return true;
        }
        return false;
    }

    function map_to_db($key, $value)
    {                
        // the boolean settings are stored as Y/N or null in the database
        if(in_array($key, self::$boolean_settings)) {
            if ($value) {
                return 'Y';
            }
            else {
                return 'N';
            }
        }
        // special mapping for the two quarantine settings we use:
        elseif($key == 'spam_quarantine_to' || $key == 'virus_quarantine_to') {
            if ($value) {
                return 'sql:';
            }
            else {
                return null;
            }
        }
        // all other settings do not require mapping
        else {
            return $value;
        }

    }

    function map_from_db($key, $value)
    {
        // the boolean settings are stored as Y/N or null in the database
        if(in_array($key, self::$boolean_settings)) {
            if (!empty($value) && $value == 'Y') {
                return true;
            }
            else {
                return false;
            }
        }
        // special mapping for the two quarantine settings we use:
        elseif($key == 'spam_quarantine_to' || $key == 'virus_quarantine_to') {
            if (!empty($value) && $value == 'sql:') {
                return true;
            }
            else {
                return false;
            }
        }
        // all other settings do not require mapping
        else {
            return $value;
        }
    }
}
?>
