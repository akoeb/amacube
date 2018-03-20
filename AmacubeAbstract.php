<?php
/**
* AmacubeAbstract - super class for AmavisConfig, AmavisQuarantine and AccountConfig
*/

/*
This file is part of the amacube Roundcube plugin
Copyright (C) 2013, Alexander Köb <nerdkram@koeb.me>

Licensed under the GNU General Public License version 3.
See the COPYING file for a full license statement.

*/
class AmacubeAbstract {
    // The roundcube login name must be the same as amavis recipient email address
    protected 	$user_email 	= '';			// User login

    private 	$db_config; 					// Store DB config
    protected 	$db_conn;   					// DB connection

    public 		$errors			= array();		// Store errors

    // Constructor
    function __construct($db_config)
    {
        // Set the DB connection config
        $this->db_config 		= $db_config;
		// RCMail
        $this->rc 				= rcmail::get_instance();
        // This plugin assumes the the username equals the email address we want to have amavis checking for
		$this->user_email		= $this->rc->user->data['username'];

    }

    // Connect to DB
    function init_db()
    {
        // Initialize a persistent DB connection
        if (!$this->db_conn) {
            if (!class_exists('rcube_db')) {
            	// Version: < 0.9
                $this->db_conn = new rcube_mdb2($this->db_config, '', TRUE);
            } else {
            	// Version: > 0.9
                $this->db_conn = rcube_db::factory($this->db_config, '', TRUE);
            }
        }
        $this->db_conn->db_connect('w');
		// Error check
        if ($error = $this->db_conn->is_error()) {
			$this->rc->amacube->errors[] = 'db_connect_error';
			rcube::write_log('errors','AMACUBE: Database connect error: '.$error);
			return false;
		}
		return true;
    }

    // Return the last database error
    function db_error()
    {
        if (!$this->db_conn) {
            return false;
        }
        elseif (!$this->db_conn->is_error()) {
            return false;
        }
        else {
            return $this->db_conn->is_error();
        }
    }

}
?>
