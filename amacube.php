<?php
/**
*  Amacube
* 
* A Roundcube plugin to let users change their amavis settings (which must be stored
* in a database)
* 
* @version 0.1
* @author Alexander Köb <nerdkram@koeb.me>
* @url https://github.com/akoeb/amacube
*
*/

/*
This file is part of the amacube Roundcube plugin
Copyright (C) 2013, Alexander Köb <nerdkram@koeb.me>

Licensed under the GNU General Public License version 3. 
See the COPYING file for a full license statement.          

*/


// DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 'Off');
ini_set("log_errors", 1);
ini_set("error_log", "/var/www/roundcube/plugins/amacube/roundcube-error.log");
class amacube extends rcube_plugin
{
    // all tasks except login / logout
    public $task = '?(?!login|logout).*';

    private $storage;
    private $quarantine;

    // every plugin needs to overwrite this:
    function init()
    {
        $this->load_config();

        // add taskbar button in all tasks
        $this->add_button(array(
            'command'    => 'plugin.amacube-quarantine',
            'class'      => 'button-quarantine',
            'classsel'   => 'button-quarantine button-selected',
            'innerclass' => 'button-inner',
            'label'      => 'quarantine',
        ), 'taskbar');
        
        $this->register_action('plugin.amacube-quarantine', array($this, 'init_quarantine'));
        $this->register_action('plugin.amacube-quarantine-post', array($this, 'process_quarantines'));
        
        // actions for our settings page
        $this->register_action('plugin.amacube', array($this, 'init_settings'));
        $this->register_action('plugin.amacube-save', array($this, 'save_settings'));

        // and javascript ui modifications:
        $this->include_script('amacube.js');

    }

    // settings page is requested:
    function init_settings()
    {
        // body for the page is the settings form
        $this->register_handler('plugin.body', array($this, 'settings_form'));
        rcmail::get_instance()->output->set_pagetitle($this->gettext('amavissettings'));
        rcmail::get_instance()->output->send('plugin');
    }
    // quarantine list is requested:
    function init_quarantine()
    {
        // body of the page is the list of quarantined emails
        $this->register_handler('plugin.body', array($this, 'show_quarantine_list'));
        rcmail::get_instance()->output->set_pagetitle($this->gettext('show_quarantine'));
        rcmail::get_instance()->output->send('plugin');
    }
  
    // This displays the settings form
    function settings_form()
    {
        $rcmail = rcmail::get_instance();
        $user = $rcmail->user;

        // collect and store the output in this var:
        $out = '';
        
        // load user settings from the database
        // if the user has not yet settings in the database, the form will be 
        //filled with default values in the property declaration of that class:
        include_once('AmavisSettings.php');
        $this->storage = new AmavisSettings($rcmail->config->get('amacube_db_dsn'));

        // path to info graphic
        $info_png = $this->urlbase.'media/info.png';


        // create a message box stating that the users values were filled from default
        // if no database record was found:
        if(! $this->storage->policy_pk) {
            $rcmail->output->command('display_message', $this->gettext('policy_default_message'), 'warning');
        }

        // add some labels to client
        $rcmail->output->add_label(
                'amacube.activate_spam_check',
                'amacube.activate_virus_check',
                'amacube.activate_spam_quarantine',
                'amacube.activate_virus_quarantine',
                'amacube.spam_tag2_level',
                'amacube.spam_kill_level'
        );

        // create a table to hold form content:
        $table = new html_table(array('cols' => 3, 'cellpadding' => 3));

        //DEBUG
        $table->add(array('colspan' => '3'), html::tag(
            'h4', null, Q("DEBUG: user: ".$this->storage->user_pk.", policy: ".$this->storage->policy_pk)));
        
        
        // heading for first form section:
        $table->add(array('colspan' => '3'), html::tag(
            'h4', null, Q($this->gettext('activate')))
        );

        # checkboxes to activate spam check:
        $table->add('',$this->_show_checkbox(
            'activate_spam_check',
            $this->storage->is_check_activated_checkbox('spam')
        ));
        $table->add('title', html::label(
            'activate_spam_check', 
            Q($this->gettext('spamcheck'))
        ));
        # info image link:
        $table->add('',html::img(array(
            'src' => $info_png,
            'title' => Q($this->gettext('spam_check_active_info')),
            'alt' => Q($this->gettext('spamcheck'))
        )));


        # checkboxes to activate virus check:
        $table->add('',$this->_show_checkbox( 
            'activate_virus_check',
            $this->storage->is_check_activated_checkbox('virus')
        ));
        $table->add('title', html::label(
            'activate_virus_check', 
            Q($this->gettext('viruscheck'))
        ));
        # info image link:
        $table->add('',html::img(array(
            'src' => $info_png,
            'title' => Q($this->gettext('virus_check_active_info')),
            'alt' => Q($this->gettext('virus_check_active'))
        )));


        # next section: quarantine
        $table->add(array('colspan' => '3'), html::tag(
            'h4', null, Q($this->gettext('quarantine')))
        );

        # checkbox to activate spam quarantine:
        $table->add('',$this->_show_checkbox( 
            'activate_spam_quarantine',
            $this->storage->is_quarantine_activated_checkbox('spam')
        ));

        $table->add('title', html::label(
            'activate_spam_quarantine', 
            Q($this->gettext('spamquarantine'))
        ));
        # info image link:
        $table->add('',html::img(array(
            'src' => $info_png,
            'title' => Q($this->gettext('spam_quarantine_active_info')),
            'alt' => Q($this->gettext('spam_quarantine_active'))
        )));

        # checkbox to activate virus quarantine:
        $table->add('',$this->_show_checkbox( 
            'activate_virus_quarantine',
            $this->storage->is_quarantine_activated_checkbox('virus')
        ));
        $table->add('title', html::label(
            'info', 
            Q($this->gettext('virusquarantine'))
        ));
        # info image link:
        $table->add('',html::img(array(
            'src' => $info_png,
            'title' => Q($this->gettext('virus_quarantine_active_info')),
            'alt' => Q($this->gettext('virus_quarantine_active'))
        )));

        # checkbox to activate virus quarantine:
        $table->add('',$this->_show_checkbox( 
            'activate_banned_quarantine',
            $this->storage->is_quarantine_activated_checkbox('banned')
        ));
        $table->add('title', html::label(
            'info', 
            Q($this->gettext('bannedquarantine'))
        ));
        # info image link:
        $table->add('',html::img(array(
            'src' => $info_png,
            'title' => Q($this->gettext('banned_quarantine_active_info')),
            'alt' => Q($this->gettext('banned_quarantine_active'))
        )));



        # next section: input boxes for spam level settings
        $table->add(array('colspan' => '3'), html::tag(
            'h4', null, Q($this->gettext('adjust levels')))
        );

        # input box for sa_tag2_level:
        $table->add('',$this->_show_inputfield( 
            'spam_tag2_level',
            $this->storage->policy_setting['spam_tag2_level']
        ));
        $table->add('title', html::label(
            'spam_tag2_level', Q($this->gettext('spam_tag2_level'))
        ));
        # info image link:
        $table->add('',html::img(array(
            'src' => $info_png,
            'title' => Q($this->gettext('spam_tag2_level_info')),
            'alt' => Q($this->gettext('spam_tag2_level'))
        )));

        # input box for sa_kill_level:
        $table->add('',$this->_show_inputfield( 
            'spam_kill_level',
            $this->storage->policy_setting['spam_kill_level']
        ));
        $table->add('title', html::label(
            'spam_kill_level', Q($this->gettext('spam_kill_level'))
        ));
        # info image link:
        $table->add('',html::img(array(
            'src' => $info_png,
            'title' => Q($this->gettext('spam_kill_level_info')),
            'alt' => Q($this->gettext('spam_kill_level'))
        )));


        # last section: submit button, empty row as separator
        $table->add_row('');
        $table->add('','');
        $table->add(array('colspan' => '2'), 
            $rcmail->output->button(array(
                'command' => 'plugin.amacube-save',
                'type' => 'input',
                'class' => 'button mainaction',
                'label' => 'save'
        )));
        
    
        // register the form to the client:
        $rcmail->output->add_gui_object('amacubeform', 'amacubeform');
        // and return the page including form tags:
        return $rcmail->output->form_tag(array(
                    'id' => 'amacubeform',
                    'name' => 'amacubeform',
                    'method' => 'post',
                    'action' => './?_task=settings&_action=plugin.amacube-save',
                    ), $table->show());

    }

    // this saves the setting after the settings form was submitted
    function save_settings()
    {
        $this->register_handler('plugin.body', array($this, 'settings_form'));
        $rcmail = rcmail::get_instance();
        $rcmail->output->set_pagetitle($this->gettext('amavissettings'));
        
        // et the post vars
        $activate_spam_check = get_input_value('_activate_spam_check', RCUBE_INPUT_POST, false);
        $activate_virus_check = get_input_value('_activate_virus_check', RCUBE_INPUT_POST, false);
        $activate_spam_quarantine = get_input_value('_activate_spam_quarantine', RCUBE_INPUT_POST, false);
        $activate_virus_quarantine = get_input_value('_activate_virus_quarantine', RCUBE_INPUT_POST, false);
        $activate_banned_quarantine = get_input_value('_activate_banned_quarantine', RCUBE_INPUT_POST, false);

        $spam_tag2_level = get_input_value('_spam_tag2_level', RCUBE_INPUT_POST, false);
        $spam_kill_level = get_input_value('_spam_kill_level', RCUBE_INPUT_POST, false);

        // verify the integer post params:
        $error = false;
        if (! is_numeric($spam_tag2_level) || $spam_tag2_level < -20 || $spam_tag2_level > 20) {
            $rcmail->output->command('display_message', $this->gettext('spam_tag2_level'), 'error');
            $error = true;
        }
        if(! is_numeric($spam_kill_level) || $spam_kill_level < -20 || $spam_kill_level > 20) {
            $rcmail->output->command('display_message', $this->gettext('spam_kill_level'), 'error');
            $error = true;
        }
                  
        if(! $error) {
            $write_error = '';
            
            // new storage object
            include_once('AmavisSettings.php');
            $this->storage = new AmavisSettings($rcmail->config->get('amacube_db_dsn'));
    
            // now overwrite the new settings:
            if(!empty($activate_spam_check)) {
                $this->storage->policy_setting['spam_lover'] = false;
            }
            else {
                $this->storage->policy_setting['spam_lover'] = true;
            }
            if(!empty($activate_virus_check)) {
                $this->storage->policy_setting['virus_lover'] = false;
            }
            else {
                $this->storage->policy_setting['virus_lover'] = true;
            }
            if(! empty($activate_spam_quarantine)) {
                $this->storage->policy_setting['spam_quarantine_to'] = true;
            }
            else {
                $this->storage->policy_setting['spam_quarantine_to'] = false;
            }
            if(! empty($activate_virus_quarantine)) {
                $this->storage->policy_setting['virus_quarantine_to'] = true;
            }
            else {
                $this->storage->policy_setting['virus_quarantine_to'] = false;
            }
            if(! empty($activate_banned_quarantine)) {
                $this->storage->policy_setting['banned_quarantine_to'] = true;
            }
            else {
                $this->storage->policy_setting['banned_quarantine_to'] = false;
            }
            if($spam_tag2_level) {
                $this->storage->policy_setting['spam_tag2_level'] = $spam_tag2_level;
            }
            if($spam_kill_level) {
                $this->storage->policy_setting['spam_kill_level'] = $spam_kill_level;
            }
   
            // check whether resulting property is syntactically correct:
            $verify = $this->storage->verify_policy_array();
            if(isset($verify) && is_array($verify)) {
                //  error check: 
                $rcmail->output->command('display_message', $this->gettext('verification_error'), 'error');
            }
            else {
                // and store the settings:
                $write_error = $this->storage->write_to_db();
            }
            // error check
            if($write_error) {
                $rcmail->output->command('display_message', $this->gettext('write_error'), 'error');
                $rcmail->output->command('display_message', $write_error, 'error');
            }
            else {
                // success message and done
                $rcmail->output->command('display_message', $this->gettext('successfullysaved'), 'confirmation');
            }
        }
        // and send:
        $rcmail->output->send('plugin');

    }


    // list all the quarantined mails for this user:
    function show_quarantine_list () {

        $rcmail = rcmail::get_instance();
        include_once('AmavisQuarantine.php');
        $this->quarantine = new AmavisQuarantine($rcmail->config->get('amacube_db_dsn'),
                                                 $rcmail->config->get('amacube_amavis_host'), 
                                                 $rcmail->config->get('amacube_amavis_port'));
        
        $output = html::tag('h4', null, Q($this->gettext('quarantine')));
        

        // read post vars for pagination or set to defaults
        $start_index = get_input_value('_start_index', RCUBE_INPUT_POST, false);
        $rows_displayed = get_input_value('_rows_displayed', RCUBE_INPUT_POST, false);
        if(!$start_index) $start_index = 0;
        if(!$rows_displayed) $rows_displayed = 20;


        // get the list of quarantined emails:
        $quarantines = $this->quarantine->list_quarantines($start_index, $rows_displayed);

        if(! is_array ($quarantines)) {
            // FIXME error:
            $output .= Q($this->gettext('db_error'));
            $output .= $quarantines;
            $rcmail->output->command('display_message', $quarantines, 'error');
            return $output;
        }
        elseif(count ($quarantines) == 0) {
            // no result:
            $output .= Q($this->gettext('no_result'));
            return $output;
       }

        // create a table to hold form content:
        $table = new html_table(array('cols' => 8, 'cellpadding' => 3));

        // header
        $table->add_header('','delete');
        $table->add_header('','release');
        $table->add_header('','Age');
        $table->add_header('','Subject');
        $table->add_header('','Sender');
        $table->add_header('','Mail Type');
        $table->add_header('','Delivery Status');
        $table->add_header('','Spam Level');

        foreach ($quarantines as $key => $value) {
            $table->add('',$this->_show_checkbox('del_'.$quarantines[$key]['id']));
            $table->add('',$this->_show_checkbox('rel_'.$quarantines[$key]['id']));
            $table->add('',$quarantines[$key]['age']);
            $table->add('',$quarantines[$key]['subject']);
            $table->add('',$quarantines[$key]['sender']);
            $table->add('',$this->gettext('content_decode_'.$quarantines[$key]['content']));
            $table->add('',$this->gettext('dsn_decode_'.$quarantines[$key]['dsn']));
            $table->add('',$quarantines[$key]['level']);

        }

        $form_content = $table->show();
        $form_content .= html::div('',$rcmail->output->button(array(
            'command' => 'plugin.amacube-quarantine-post',
            'type' => 'input',
            'class' => 'button mainaction',
            'label' => 'process'
            )));


        # compose form, add table and action buttons:
        $output .= $rcmail->output->form_tag(array(
                    'id' => 'quarantineform',
                    'name' => 'quarantineform',
                    'method' => 'post',
                    'action' => './?_task=mail&_action=plugin.amacube-quarantine-post',
                    ), $form_content);
        
    
        // register the form to the client:
        $rcmail->output->add_gui_object('quarantineform', 'quarantineform');


        return $output;
    }

    /**
    * process_quarantines - action handler that either deletes quarantined mails or marks them for release
    */
    function process_quarantines() {
        $this->register_handler('plugin.body', array($this, 'show_quarantine_list'));
        $rcmail = rcmail::get_instance();
        $rcmail->output->set_pagetitle($this->gettext('quarantine'));
        

        // get a list of quarantines to delete or release:
        $delete = array(); // stores mail_ids to be deleted
        $release = array(); // stores mail_ids to be released (and thus sent to the recipient)
        foreach ($_POST as $key => $value) {
            if($value === 'on' && preg_match('/_([dr]el)_([\w\-]+)/', $key, $matches)) {
                if($matches[1] == 'del') {
                    array_push($delete, $matches[2]);
                }
                elseif($matches[1] == 'rel') {
                    array_push($release, $matches[2]);
                }
            }
        }

        // if we have intersections, thats an error, error output and stop:
        $intersect = array_intersect($delete, $release);
        if (is_array($intersect) && count($intersect) > 0) {
            $rcmail->output->command('display_message', $this->gettext('intersection_error'), 'error');
            $rcmail->output->send('plugin');
            return;
        }
        
        
        include_once('AmavisQuarantine.php');
        $this->quarantine = new AmavisQuarantine($rcmail->config->get('amacube_db_dsn'), 
                                                 $rcmail->config->get('amacube_amavis_host'), 
                                                 $rcmail->config->get('amacube_amavis_port'));

        $this->quarantine->delete($delete);
        $this->quarantine->release($release);


        // and send:
        $rcmail->output->send('plugin');
    }

    // CONVENIENCE METHODS
    // This bloody html_checkbox class will always return checkboxes that are "checked"
    // I did not figure out how to prevent that $$*@@!!
    // so I used html::tag instead...
    function _show_checkbox($id, $checked = false)
    {
        $attr_array = array('name' => '_'.$id,'id' => $id);
        if($checked) {
            $attr_array['checked'] = 'checked';
        }
        //$box = new html_checkbox($attr_array);
        $attr_array['type'] = 'checkbox';
        $box = html::tag('input',$attr_array);
        return $box;
    }
    function _show_inputfield($id, $value)
    {
        $input = new html_inputfield(array(
                'name' => '_'.$id, 
                'id' => $id,
                'value' => $value,
                'size'  =>  10
        ));
        return $input->show();
    }
}
?>
