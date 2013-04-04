if (window.rcmail) {
    rcmail.addEventListener('init', function(evt) {
        // <span id="settingstabdefault" class="tablink"><roundcube:button command="preferences" type="link" label="preferences" title="editpreferences" /></span>
        var tab = $('<span>').attr('id', 'settingstabpluginamacube').addClass('tablink');
   
        var button = $('<a>').attr('href', rcmail.env.comm_path+'&_action=plugin.amacube').html(rcmail.gettext('Amavis Settings')).appendTo(tab);
        button.bind('click', function(e){ return rcmail.command('plugin.amacube', this) });
   
        // add button and register command
        rcmail.add_element(tab, 'tabs');
        rcmail.register_command('plugin.amacube', function(){ rcmail.goto_url('plugin.amacube') }, true);
   });
}
