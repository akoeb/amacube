# Amacube

Roundcube plugin to let users control their amavis settings as well as manage quarantined emails.

## Configurable Settings

The user can adjust following settings from the settings task:
* activate or deactivate spam check for this user
* activate or deactivate virus check for this user
* activate or deactivate quarantining of virus emails
* activate or deactivate quarantining of spam emails
* activate or deactivate quarantining of banned emails
* spam tag2 level
* spam kill level

All Those configuration options apply only to the user, for all other users the sidewide defaults apply.

## Quarantine

* The user can view, delete or release quarantined emails through the quarantine button in the task bar

## Notes on the software

* This software is in an early development status, see CHANGELOG. 
* The plugin assumes that the login name for roundcube is equal to the email address of the user that amavis sees as recipient.
* Alias email addresses need to be expanded by postfix before amavis sees them, otherwise this plugin only works with the main email addreess, not its aliases
* The plugin only works if amavis uses a sql database for policy and quarantining, see README.sql and README.sql-mysql of the amavis documentation
* It is tested only with mysql for now.
* If a user loads the settings page first time and he has no database record yet, it will be created upon save of the settings.
* If you find bugs, have comments on the software, want to send patches or have implementation wishes, you are welcome to send them to me, but I won't promise anything.
* If you find this software useful and you ever show up in the same geographical location as I am, you are welcome to buy me a beer. ;-)


## Installation

1. Amavis
I document here only some specific settings for this plugin, please refer to Amavis documentation for other configuration
options.
* Create a Database for amavis in Mysql:

```sql
    SQL> create database amavis;
```

* allow access to this database from amavis and roundcube host in Mysql:

```sql
    SQL> grant all privileges on amavis.*  TO '<AMAVIS-USER>'@'<AMAVIS-HOST>' IDENTIFIED BY '<AMAVIS-PASSWORD>';
    SQL> grant all privileges on amavis.*  TO '<AMAVIS-USER>'@'<ROUNDCUBE-HOST>' IDENTIFIED BY '<AMAVIS-PASSWORD>';
```

* set amavis database connection in the amavis configuration:

```perl
    @lookup_sql_dsn = ( ['DBI:mysql:database=amavis;host=<MYSQL-HOST>;port=3306', '<AMAVIS-USER>', '<AMAVIS-PASSWORD>]);
    @storage_sql_dsn = @lookup_sql_dsn;
```

* for release of quarantined emails

```perl
    # tell amavis to listen on all interfaces:
    $inet_socket_bind = undef;
    # The ports amavis needs to listen (10024 for postfix, 9998 for us)
    $inet_socket_port = [10024, 9998];
    # new interface policy for posrt 9998
    $interface_policy{'9998'} = 'AM.PDP-INET';
    $policy_bank{'AM.PDP-INET'} = {
        protocol => 'AM.PDP',  # select Amavis policy delegation protocol
        inet_acl => [qw( 127.0.0.1 [::1] )],  # restrict access to these IP addresses
        auth_required_release => 1,  # require secret_id for amavisd-release
    };
```

2. Amacube Plugin
* create a directory in your roundcube plugin directory called amacube
* drop all the files of the plugin into this directory
* create a amacube/config.inc.php file with mysql and amavis settings filled out correctly. A template with the extension -dist is supplied.
* add amacube to the $rcmail_config['plugins'] array in roundcubes config/main.inc.php

## TODO

This is a uncomplete list of things that need to be done with this plugin
* pagination in the quarantine list
* proper error handling (see all those FIXME statements in the code)
* eye candy
* lots of help and information texts

## Version

0.1.1 - initial release, functionally working, but ugly and lacking informational texts


## License

GPLv3 - see COPYING file for full license statement

## Author

Alexander Köb <nerdkram@koeb.me>
