<?php
/**
* AccountConfig - class to load and store account settings in DB
*/

/*
This file is part of the amacube Roundcube plugin
Copyright (C) 2013, Alexander Köb <nerdkram@koeb.me>

Licensed under the GNU General Public License version 3.
See the COPYING file for a full license statement.

*/
include_once('AmacubeAbstract.php');
class AccountConfig extends AmacubeAbstract
{
    // User config
    public 	$initialized							= false;	// User record exists in database
    public	$catchall								= null;
	public	$filter									= null;

    // Constructor
    function __construct($db_config) {
        // Call constructor of the super class to initialize db connection:
        parent::__construct($db_config);
		// Check config
		$this->initialized = $this->get_account();

    }

	function get_account() {

		$table_account 							= $this->rc->config->get('amacube_accounts_db_account_table');
		$field_account 							= $this->rc->config->get('amacube_accounts_db_account_field');
		$field_account_filter 					= $this->rc->config->get('amacube_accounts_db_account_filter_field');
		$field_account_catchall					= $this->rc->config->get('amacube_accounts_db_account_catchall_field');
		// Account table required
		if (isset($table_account) && is_string($table_account)) {
			// Account field required
    		if (isset($field_account) && is_string($field_account)) {
    			// Account filter or catchall required
    			if ((isset($field_account_filter) && is_string($field_account_filter)) || (isset($field_account_catchall) && is_string($field_account_catchall))) {
    				// Connect to db
					if (!is_resource($this->db_conn)) {
						if (!$this->init_db()) { return false; }
					}
					$query = "SELECT $table_account.$field_account";
					if ($field_account_filter) {
						$query .= ", $table_account.$field_account_filter";
					}
					if ($field_account_catchall) {
						$query .= ", $table_account.$field_account_catchall";
					}
					$query .= " FROM $table_account WHERE $table_account.$field_account = ? ";
			        $res = $this->db_conn->query($query, $this->user_email);
			        if ($error = $this->db_conn->is_error()) {
						$this->rc->amacube->errors[] = 'db_query_error';
						rcube::write_log('errors','AMACUBE: Database query error: '.$error);
						return false;
					}
			        if ($res && ($res_array = $this->db_conn->fetch_assoc($res))) {
						// Check filter
						if (isset($res_array[$field_account_filter])) {
							if ($res_array[$field_account_filter] == 1 || $res_array[$field_account_filter] == 'Y' || $res_array[$field_account_filter]) {
								$this->filter 	= true;
							} else {
								$this->filter 	= false;
							}
						}
						// Check catchall
						if (isset($res_array[$field_account_catchall])) {
							if ($res_array[$field_account_catchall] == 1 || $res_array[$field_account_catchall] == 'Y' || $res_array[$field_account_catchall]) {
								$this->catchall = true;
							} else {
								$this->catchall = false;
							}
						}
						return true;
			        }
    			}
    		}
		}
    	return false;
	}

}
?>
