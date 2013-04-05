<?php
class AmavisSettings
{
    // USER SETTINGS
    private $user_pk; // primary key for the user record
    private $priority = 7; // we do not change the amavis default for that
    private $user_email; // email address of the user
    private $fullname; // Full Name of the user, for reference, Amavis does not use that  

    // POLICY SETTINGS
    private $policy_pk; // primary key of the policy record
    private $policy_name; // Name of the policy, for reference, Amavis does not use that
    private $policy_settings = array(
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
        'spam_quarantine_cutoff_level' => 20, // float
 
        'virus_quarantine_to' => '',        // later
        'spam_quarantine_to' => '',         // later
        'banned_quarantine_to' => '',       // later
        'unchecked_quarantine_to' => '',    // later
        'bad_header_quarantine_to' => '',   // later
        'clean_quarantine_to' => '',        // later
        'archive_quarantine_to' => '',      // later
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
    function init($email, $db_config)
    {
        // set the obligatory email and DB connection config
        $this->user_email = $email;
        $this->db_config = $db_config;

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

        // FIXME:
        // make sure the array does not contain any other keys

        /* TODO
        'virus_quarantine_to' => '',        // later
        'spam_quarantine_to' => '',         // later       
        'banned_quarantine_to' => '',       // later     
        'unchecked_quarantine_to' => '',    // later  
        'bad_header_quarantine_to' => '',   // later 
        'clean_quarantine_to' => '',        // later      
        'archive_quarantine_to' => '',      // later    
        */


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

    }

    // read amavis settings from database
    function read_from_db() 
    {
        if (! is_resource($db_conn)) {
            $this->init_db();
        }

        // check whether we have a db connection
        $query = ' SELECT *
            FROM users, policy
            WHERE users.policy_id = policy.id 
            AND users.email = ? ';

        // prepare statement

        // execute

        // write to settings array
        $this->policy_settings[''] = $result[''];
        ... 

    }
    // write settings back to database
    function write_to_db()
    {

    }
}
?>
