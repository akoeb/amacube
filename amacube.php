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


class amacube extends rcube_plugin
{
    // only run in the settings task:
    public $task = 'settings';

    function init()
    {
        error_log('amacube loaded',0);
        // register an action to initialize our settings page
        $this->register_action('plugin.amacube', array($this, 'init_settings'));
        $this->include_script('amacube.js');

    }
    function init_settings()
    {
        $this->register_handler('plugin.body', array($this, 'settings_page'));
        rcmail::get_instance()->output->send('plugin');
    }
  
    function settings_page()
    {
         $rcmail = rcmail::get_instance();
         $user = $rcmail->user;
         
         $table = new html_table(array('cols' => 2, 'cellpadding' => 3));
         
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
         
         return html::tag('h4', null, Q('Infos for ' . $user->get_username())) . $table->show();
    }

}
?>
