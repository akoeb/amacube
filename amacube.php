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
        //FIXME



        // create a table to hold form content:
        $table = new html_table(array('cols' => 3, 'cellpadding' => 3));

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
            $this->storage->policy_settings['spam_tag2_level']
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
            $this->storage->policy_settings['spam_kill_level']
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
        

/*
        $table->add('title', 'ID');
        $table->add('', Q($user->ID));
        
        $table->add('title', Q($this->gettext('username')));
        $table->add('', Q($user->data['username']));
        
        $table->add('title', Q($this->gettext('server')));
        $table->add('', Q($user->data['mail_host']));
        
        $table->add('title', Q($this->gettext('created')));
        $table->add('', Q($user->data['created']));
        
        $table->add('title', Q($this->gettext('lastlogin')));
        $table->add('', Q($user->data['last_login']));
        
        $identity = $user->get_identity();
        $table->add('title', Q($this->gettext('defaultidentity')));
        $table->add('', Q($identity['name'] . ' <' . $identity['email'] . '>'));
        
        //if the user has no database entry yet, the user_pk field is empty:
        if(! $this->storage->user_pk) {
             $table->add('title', Q('NO DATABASE ENTRY YET, USING DEFAULT VALUES!!'));
        }
        else {
            $table->add('title', Q($this->gettext('users_pk')));
            $table->add('', Q($this->storage->user_pk));
            $table->add('title', Q($this->gettext('policy_pk')));
            $table->add('', Q($this->storage->policy_pk));
            $table->add('title', Q($this->gettext('email')));
            $table->add('', Q($this->storage->user_email));
            $table->add('title', Q($this->gettext('settings')));
            $settings = '';
            foreach ($this->storage->policy_settings as $key => $value) {
                $settings .= $key . ' => '.$value ."\n";
            }
            $table->add('', Q($settings));
        }
  */     
        // do we need that?
        //$rcmail->output->add_gui_object('amacubeform', 'amacube-form');
        return $rcmail->output->form_tag(array(
                    'id' => 'amacube-form',
                    'name' => 'amacube-form',
                    'method' => 'post',
                    'action' => './?_task=settings&_action=plugin.amacube-save',
                    ), $table->show());

    }

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
