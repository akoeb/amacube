amacube
=======

Roundcube plugin to let users control their amavis settings as well as manage quarantined emails.

## Configurable Settings
* final_*_destiny
* tag level
* tag2 level
* kill level
* *_quaranti__to

## Quarantine
* if use_quarantine is on, the quarantined mails can be viewed, released or deleted

## Installation
1. amavis
* create database amavis;
* grant all privileges on amavis.*  TO '<AMAVIS-USER>'@'<AMAVIS-HOST>' IDENTIFIED BY '<AMAVIS-PASSWORD>';
* create users:
**  INSERT INTO users VALUES ( 1, 7, 1, '<EMAIL>','<NAME>');
INSERT INTO policy (id, policy_name,
    virus_lover, spam_lover, banned_files_lover, bad_header_lover,
    bypass_virus_checks, bypass_spam_checks, bypass_banned_checks, bypass_header_checks, 
    spam_modifies_subj, spam_tag_level, spam_tag2_level, spam_kill_level, spam_dsn_cutoff_level
    ) 
    VALUES
    (1, 'default',
    'N','N','N','N', -- what we love
    'N','N','N','N', -- what we bypass
    'N',-999, 6, 12,12
    );

-- jeder user eine policy...

-- später: whitelist/blacklist und quarantine

* set amavis database connection:
** @lookup_sql_dsn = ( ['DBI:mysql:database=amavis;host=<MYSQL-HOST>;port=3306', '<AMAVIS-USER>', '<AMAVIS-PASSWORD>]);
   @storage_sql_dsn = @lookup_sql_dsn;



## Necessary Amavis settings
* user settings in sql
* quarantine in sql

## To Do
Pretty much everything needs still to be done...
* create a dummy plugin that shows an empty settings page - DONE
* add amavis settings to the dummy page DONE
* add quarantine mode to amavis sql DONE
* add save handler DONE
* make the dummy page check the settings on save DONE
* make the dummy page save the settings to the amavis database on save DONE
* make the page insert the user (instead of update) if he has no db record in users yet DONE
* now that the functionality is working, add localization and eye candy
* create a dummy manage-quarantine page on the main menu
* make the quarantine page read the quarantines from sql
* add functions to delete or release mails from quarantine
* add functions to whitelist/blacklist emails on the settings page

## notes
* create configure object on page load, required param for the constructor is username
* the object loads default values from config file
* the object can overwrite the settings from the database or they are set manually
* if the page is loaded with POST params: write all the POST params to the object, then call a save method that writes back to DB
* if the page is without POST params: try from database, if it fails load default and set boolean that the user has no record in DB yet
* if the user had no DB record, display a message that the user will be created 

## Version

0.0

