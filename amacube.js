/*
This file is part of the amacube Roundcube plugin
Copyright (C) 2013, Alexander Köb

Licensed under the GNU General Public License version 3. 
See the COPYING file for a full license statement.          

*/

// Extend rcmail with amacube methods for ajax pagination
rcube_webmail.prototype.amacube = {
	// Function for pagination
	// Send ajax requests for specified page || Enable/disable specified page button
	page : function(page,status) {
		var obj = {};
		switch (page) {
			case 'first':
				if (status == 'enabled' || status == 'disabled') {
					if (rcmail.commands['plugin.firstpage']) { rcmail.enable_command('plugin.firstpage', ((status == 'enabled') ? true : false)); }
					else { rcmail.register_command('plugin.firstpage', function() { rcmail.amacube.page('first'); }, ((status == 'enabled') ? true : false)); }
					return;
				}
				var obj = { page : 1, msgcount: rcmail.env.msgcount };
				break;
			case 'previous':
				if (status == 'enabled' || status == 'disabled') {
					if (rcmail.commands['plugin.previouspage']) { rcmail.enable_command('plugin.previouspage', ((status == 'enabled') ? true : false)); }
					else { rcmail.register_command('plugin.previouspage', function() { rcmail.amacube.page('previous'); }, ((status == 'enabled') ? true : false)); }
					return;
				}
				var obj = { page : (rcmail.env.page - 1), msgcount: rcmail.env.msgcount };
				break;
			case 'next':
				if (status == 'enabled' || status == 'disabled') {
					if (rcmail.commands['plugin.nextpage']) { rcmail.enable_command('plugin.nextpage', ((status == 'enabled') ? true : false)); }
					else { rcmail.register_command('plugin.nextpage', function() { rcmail.amacube.page('next'); }, ((status == 'enabled') ? true : false)); }
					return;
				}
				var obj = { page : (rcmail.env.page + 1), msgcount: rcmail.env.msgcount };
				break;
			case 'last':
				if (status == 'enabled' || status == 'disabled') {
					if (rcmail.commands['plugin.lastpage']) { rcmail.enable_command('plugin.lastpage', ((status == 'enabled') ? true : false)); }
					else { rcmail.register_command('plugin.lastpage', function() { rcmail.amacube.page('last'); }, ((status == 'enabled') ? true : false)); }
					return;
				}
				var obj = { page : rcmail.env.pagecount, msgcount: rcmail.env.msgcount };
				break;
		}
		rcmail.http_post('quarantine/amacube-quarantine', obj);
	},
	// Function for updating the list of quarantined messages
	messagelist : function(data) {
		if (data && data.messages) {
			var messages = $('table#messagelist.quarantine-messagelist').children('tbody');
			messages.empty();
			$.each(data.messages, function(index, value) {
				messages.append(value);
			});			
		}
	},
	// Function for updating message count
	messagecount : function(string) {
		var message = $('span.quarantine-countdisplay');
		message.text(string);
	}
};
// Init rcmail
if (window.rcmail) {
	// Catch clicks to quarantine task button and apply action to url
    rcmail.addEventListener('beforeswitch-task', function(prop) {
        if (prop == 'quarantine') {
            rcmail.redirect(rcmail.url('quarantine/amacube-quarantine'), false);
            return false;
		}
	});
	// Init buttons & commands
    rcmail.addEventListener('init', function(evt) {
        if (evt.task == 'settings') {
	        // Settings post command
	        rcmail.register_command('plugin.amacube-settings-post', function() { rcmail.gui_objects.amacubeform.submit(); }, true);
        }
		if (evt.task == 'quarantine') {
			// Quarantine post command
	        rcmail.register_command('amacube-quarantine-post', function() { rcmail.gui_objects.quarantineform.submit(); }, true);
			// Pagination commands
			if (rcmail.env.page > 1) {
				// Enable first & previous
				rcmail.amacube.page('first','enabled');
				rcmail.amacube.page('previous','enabled');
				// Disable first & previous by default
			}
			else if (rcmail.env.pagecount > 1) {
				if (rcmail.env.page < rcmail.env.pagecount) {
					// Enable next & last
					rcmail.amacube.page('next','enabled');
					rcmail.amacube.page('last','enabled');
				} else {
					// Disable next & last
					rcmail.amacube.page('next','disabled');
					rcmail.amacube.page('last','disabled');
				}
			}
		}
   });
}