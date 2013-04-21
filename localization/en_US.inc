<?php
/*
Language File for amacube

This file is part of the amacube Roundcube plugin
Copyright (C) 2013, Alexander Köb <nerdkram@koeb.me>

Licensed under the GNU General Public License version 3. 
See the COPYING file for a full license statement.          

*/

$labels = array();
$labels['amavissettings_pagetitle'] = 'Amavis Settings';
$labels['policy_default_message'] = 'No policy found for this user, initializing default policy!';
$labels['section activate'] = 'Activate';
$labels['spam_check'] = 'Activate spam check';
$labels['spam_check_active'] = 'activate spam check';
$labels['spam_check_active_info'] = 'Check here if you want amavis check your incoming mails for their spaminess';
$labels['virus_check'] = 'Activate virus check';
$labels['virus_check_active'] = 'activate virus check';
$labels['virus_check_active_info'] = 'Check here if you want amavis to check your incoming mails for viruses';
$labels['section quarantine'] = 'Quarantine';
$labels['spam_quarantine'] = 'Quarantine spam mails';
$labels['spam_quarantine_active'] = 'quarantine spam';
$labels['spam_quarantine_active_info'] = 'Check here if you want amavis to quarantine mails that are recognized as spam (over kill level) for your review';
$labels['virus_quarantine'] = 'Quarantine virus mails';
$labels['virus_quarantine_active'] = 'quarantine virus mails';
$labels['virus_quarantine_active_info'] = 'Check here if you want amavis to quarantine virus mails for your review';
$labels['banned_quarantine'] = 'Quarantine banned-content-mails';
$labels['banned_quarantine_active'] = 'quarantine banned-content-mails';
$labels['banned_quarantine_active_info'] = 'Check here if you want amavis to quarantine mails that contain banned content (eg. executable attachments) for your review';
$labels['section adjust levels'] = 'Adjust spam levels';
$labels['spam_tag2_level'] = 'Assumed spam level';
$labels['spam_tag2_level_info'] = 'Mails with a spam level over this are assumed to be spam, yet still delivered to you. A "X-Spam-Flag" header with the value "Yes" is added.';
$labels['spam_tag2_level_error'] = 'Assumed spam must be an integer number between -20 and 20';
$labels['spam_kill_level'] = 'Spam level';
$labels['spam_kill_level_info'] = 'Mails with a spam level over this are considered spam and NOT delivered to you. However, they can be reviewed if spam quarantine is activated';
$labels['spam_kill_level_error'] = 'Spam level must be an integer number between -20 and 20';
$labels['verification_error'] = 'Verification Error in the amavis policy, not saved!';
$labels['write_error'] = 'An error occurred writing your policy to the database';
$labels['successfully saved'] = 'Your new settings have been stored successfully.';
$labels['quarantine heading'] = 'Quarantined E-Mails';
$labels['quarantine_no_result'] = 'No quarantined emails have been found.';
$labels['quarantine_pagetitle'] = 'Quarantined emails';
$labels['intersection_error'] = 'You cannot delete AND release quarantined emails for delivery.';
$labels['content_decode_V'] = 'Virus';
$labels['content_decode_B'] = 'Banned Content';
$labels['content_decode_U'] = 'Unchecked';
$labels['content_decode_S'] = 'Killed Spam';
$labels['content_decode_Y'] = 'Spam';
$labels['content_decode_M'] = 'Bad Mime';
$labels['content_decode_H'] = 'Bad Header';
$labels['content_decode_O'] = 'Oversized';
$labels['content_decode_T'] = 'MTA error';
$labels['content_decode_C'] = 'Clean';

?>
