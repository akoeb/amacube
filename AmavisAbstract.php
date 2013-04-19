<?php
/**
* AmavisAbstract - super class for AmavisSettings and AmavisQuarantine
*/

/*
This file is part of the amacube Roundcube plugin
Copyright (C) 2013, Alexander Köb

Licensed under the GNU General Public License version 3. 
See the COPYING file for a full license statement.          

*/
class AmavisAbstract
{

    // the roundcube login name must be the same as amavis recipient email address
    protected $user_email = '';

    // DATABASE STUFF
    private $db_config; // array with db configuration
    protected $db_conn;   // db connection

    // constructor
    function __construct($db_config)
    {

        // set the DB connection config
        $this->db_config = $db_config;

         // This plugin assumes the the username equals the email address we want to 
        // have amavis checking for
        $rcmail = rcmail::get_instance();
        $this->user_email = $rcmail->user->data['username'];

    }

    // open database connection
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

    // return the last database error message
    function db_error() {
        if (!$this->db_conn) {
            return false;
        }
        elseif(!$this->db_conn->db_error) {
            return false;
        }
        else {
            return $this->db_conn->db_error_msg;
        }
    }

}
?>
