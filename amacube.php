<?php
/**
*  Amacube
* 
* A Roundcube plugin to let users change their amavis settings (which must be stored
* in a database)
*
* @version 0.0
* @author Alexander Köb
* @url https://github.com/akoeb/amacube
*
*/


// DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 'Off');
ini_set("log_errors", 1);
ini_set("error_log", "/var/www/roundcube/plugins/amacube/roundcube-error.log");
class amacube extends rcube_plugin
{
    // only run in the settings task:
    public $task = 'settings';

    private $storage;

    function init()
    {
        $this->load_config();

        // register an action to initialize our settings page
        $this->register_action('plugin.amacube', array($this, 'init_settings'));
        $this->register_action('plugin.amacube-save', array($this, 'save_settings'));
        $this->include_script('amacube.js');

    }
    function init_settings()
    {
        $this->register_handler('plugin.body', array($this, 'settings_form'));
        rcmail::get_instance()->output->set_pagetitle($this->gettext('amavissettings'));
        rcmail::get_instance()->output->send('plugin');
    }
  
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
            $this->storage->is_spam_check_activated_checkbox()
        ));
        $table->add('title', html::label(
            'activate_spam_check', 
            Q($this->gettext('spamcheck'))
        ));
        # FIXME: info image link:
        $table->add('',html::img(array(
            'src' => '',
            'title' => 'spam_check_active',
            'alt' => 'spam_check_active'
        )));

        # checkboxes to activate virus check:
        $table->add('',$this->_show_checkbox( 
            'activate_virus_check',
            $this->storage->is_virus_check_activated_checkbox()
        ));
        $table->add('title', html::label(
            'activate_virus_check', 
            Q($this->gettext('viruscheck'))
        ));
        # FIXME: info image link:
        $table->add('',html::img(array(
            'src' => '',
            'title' => 'virus_check_active',
            'alt' => 'virus_check_active'
        )));


        # next section: quarantine
        $table->add(array('colspan' => '3'), html::tag(
            'h4', null, Q($this->gettext('quarantine')))
        );

        # checkbox to activate spam quarantine:
        $table->add('',$this->_show_checkbox( 
            'activate_spam_quarantine',
            $this->storage->is_spam_quarantine_activated_checkbox()
        ));

        $table->add('title', html::label(
            'activate_spam_quarantine', 
            Q($this->gettext('spamquarantine'))
        ));
        # FIXME: info image link:
        $table->add('',html::img(array(
            'src' => '',
            'title' => 'spam_quarantine_active',
            'alt' => 'spam_quarantine_active'
        )));

        # checkbox to activate virus quarantine:
        $table->add('',$this->_show_checkbox( 
            'activate_virus_quarantine',
            $this->storage->is_virus_quarantine_activated_checkbox()
        ));
        $table->add('title', html::label(
            'info', 
            Q($this->gettext('virusquarantine'))
        ));
        # FIXME: info image link:
        $table->add('',html::img(array(
            'src' => '',
            'title' => 'virus_quarantine_active',
            'alt' => 'virus_quarantine_active'
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
        # FIXME: info image link:
        $table->add('',html::img(array(
            'src' => '',
            'title' => 'spam_tag2_level',
            'alt' => 'spam_tag2_level'
        )));

        # input box for sa_kill_level:
        $table->add('',$this->_show_inputfield( 
            'spam_kill_level',
            $this->storage->policy_setting['spam_kill_level']
        ));
        $table->add('title', html::label(
            'spam_kill_level', Q($this->gettext('spam_kill_level'))
        ));
        # FIXME: info image link:
        $table->add('',html::img(array(
            'src' => '',
            'title' => 'spam_kill_level',
            'alt' => 'spam_kill_level'
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


    function save_settings()
    {
        $this->register_handler('plugin.body', array($this, 'settings_form'));
        $rcmail = rcmail::get_instance();
        $rcmail->output->set_pagetitle($this->gettext('amavissettings'));
        
        // et the post vars
        $activate_spam_check = get_input_value('_activate_spam_check', RCUBE_INPUT_POST, true);
        $activate_virus_check = get_input_value('_activate_virus_check', RCUBE_INPUT_POST, true);
        $activate_spam_quarantine = get_input_value('_activate_spam_quarantine', RCUBE_INPUT_POST, true);
        $activate_virus_quarantine = get_input_value('_activate_virus_quarantine', RCUBE_INPUT_POST, true);

        $spam_tag2_level = get_input_value('_spam_tag2_level', RCUBE_INPUT_POST, true);
        $spam_kill_level = get_input_value('_spam_kill_level', RCUBE_INPUT_POST, true);

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
