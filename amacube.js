/*
This file is part of the amacube Roundcube plugin
Copyright (C) 2013, Alexander Köb

Licensed under the GNU General Public License version 3. 
See the COPYING file for a full license statement.          

*/
if (window.rcmail) {
    rcmail.addEventListener('init', function(evt) {
        // <span id="settingstabdefault" class="tablink"><roundcube:button command="preferences" type="link" label="preferences" title="editpreferences" /></span>
        var tab = $('<span>').attr('id', 'settingstabpluginamacube').addClass('tablink');
   
        var button = $('<a>').attr('href', rcmail.env.comm_path+'&_action=plugin.amacube').html(rcmail.gettext('Amavis Settings')).appendTo(tab);
        button.bind('click', function(e){ return rcmail.command('plugin.amacube', this) });
   
        // add button and register command
        rcmail.add_element(tab, 'tabs');
        rcmail.register_command('plugin.amacube', function(){ rcmail.goto_url('plugin.amacube') }, true);

        rcmail.register_command('plugin.amacube-save', function() { 
            // client input verification here
            rcmail.gui_objects.amacubeform.submit();
        }, true);

        // need this for quarantine
        rcmail.register_command('plugin.amacube-quarantine', function(){ rcmail.goto_url('mail/plugin.amacube-quarantine') }, true);

        rcmail.register_command('plugin.amacube-quarantine-post', function() { 
            // client input verification here
            rcmail.gui_objects.quarantineform.submit();
        }, true);

   });
}
