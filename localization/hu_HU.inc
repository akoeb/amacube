<?php
/*
Language File for amacube

This file is part of the amacube Roundcube plugin
Copyright (C) 2013, Alexander Köb <nerdkram@koeb.me>

Licensed under the GNU General Public License version 3. 
See the COPYING file for a full license statement.          

*/

$labels = array();

$labels['policy_default_message'] = 'Initialized default policy.';
$labels['spam_check'] = 'SPAM ellenőrzés bekapcsolása';
$labels['spam_check_active'] = 'SPAM ellenőrzés bekapcsolása';
$labels['spam_check_active_info'] = 'Check here if you want amavis check your incoming mails for their spaminess';
$labels['virus_check'] = 'Vírus ellenőrzés bekapcsolása';
$labels['virus_check_active'] = 'vírus ellenőrzés bekapcsolása';
$labels['virus_check_active_info'] = 'Check here if you want amavis to check your incoming mails for viruses';
$labels['spam_quarantine'] = 'Quarantine spam mails';
$labels['spam_quarantine_active'] = 'quarantine spam';
$labels['spam_quarantine_active_info'] = 'Check here if you want amavis to quarantine mails that are recognized as spam (over kill level) for your review';
$labels['virus_quarantine'] = 'Vírusos levelek karanténba zárása';
$labels['virus_quarantine_active'] = 'vírusos levelek karanténba zárása';
$labels['virus_quarantine_active_info'] = 'Check here if you want amavis to quarantine virus mails for your review';
$labels['banned_quarantine'] = 'Tiltott tartalmú levelek karanténba zárása';
$labels['banned_quarantine_active'] = 'tiltott tartalmú levelek karanténba zárása';
$labels['banned_quarantine_active_info'] = 'Check here if you want amavis to quarantine mails that contain banned content (eg. executable attachments) for your review';
$labels['spam_tag2_level'] = 'Spam jelölés szintje (csak jelöl)';
$labels['spam_tag2_level_info'] = 'Mails with a spam level over this are assumed to be spam, yet still delivered to you. A "X-Spam-Flag" header with the value "Yes" is added.';
$labels['spam_tag2_level_error'] = 'Assumed spam must be an integer number between -20 and 20';
$labels['spam_kill_level'] = 'Spam kiszűrés szintje (karanténba helyez)';
$labels['spam_kill_level_info'] = 'Mails with a spam level over this are considered spam and NOT delivered to you. However, they can be reviewed if spam quarantine is activated';
$labels['spam_kill_level_error'] = 'Spam level must be an integer number between -20 and 20';
$labels['verification_error'] = 'Verification Error in the amavis policy, not saved!';
$labels['write_error'] = 'An error occurred writing your policy to the database';
$labels['config_saved'] = 'Az új beállításokat sikeresen elmentettük.';
$labels['successfully saved'] = 'Your new settings have been stored successfully.';
$labels['quarantine_no_result'] = 'Nincsenek üzenetek a karanténban';
$labels['quarantine_pagetitle'] = 'Karanténba helyezett levelek';
$labels['intersection_error'] = 'You cannot delete AND release quarantined emails for delivery.';
$labels['content_decode_V'] = 'Virus';
$labels['content_decode_B'] = 'Tiltott tartalom';
$labels['content_decode_U'] = 'Nem ellenőrzött';
$labels['content_decode_S'] = 'Kiszűrt SPAM';
$labels['content_decode_Y'] = 'SPAM';
$labels['content_decode_M'] = 'Hibás MIME';
$labels['content_decode_H'] = 'Hibás fejlécek';
$labels['content_decode_O'] = 'Túlméretes';
$labels['content_decode_T'] = 'MTA hiba';
$labels['content_decode_C'] = 'Tiszta';

$labels['filter_settings_pagetitle'] = 'SPAM/Vírus szűrés';
$labels['section_checks'] = 'Ellenőrzések';
$labels['section_quarantine'] = 'Karantén';
$labels['section_delivery'] = 'Kézbesítés';
$labels['section_levels'] = 'Határértékek';
$labels['spam_quarantine_cutoff_level'] = 'SPAM megsemmisítés szintje (megsemmisít)';
$labels['process'] = 'Feldolgozás';
$labels['quarantine'] = 'Karantén';
$labels['spam_delivery'] = 'SPAM levelek';
$labels['virus_delivery'] = 'Vírusos levelek';
$labels['banned_delivery'] = 'Tiltott fájlt tartalmazó levelek';
$labels['bad_header_delivery'] = 'Levelek fejléc-hibákkal';
$labels['deliver'] = 'Kézbesít';
$labels['discard'] = 'Megsemmisít';
$labels['process'] = 'Feldolgoz';
$labels['email_filter_disabled'] = 'E-mail szűrés jelenleg kikapcsolva';
$labels['email_filter_enabled'] = 'E-mail szűrés bekapcsolva';
$labels['process_quarantine'] = 'Karantén tartalmának feldolgozása a megjelölt levelek elengedésével, vagy törlésével';
$labels['release'] = 'Elenged';
$labels['delete'] = 'Töröl';
$labels['subject'] = 'Tárgy';
$labels['received'] = 'Érkezett';
$labels['mailtype'] = 'Levél típus';
$labels['spamlevel'] = 'SPAM pontszám';
$labels['messages'] = 'Üzenetek';
$labels['levels'] = 'Határértékek';
$labels['status'] = 'Állapot';
$labels['checks'] = 'Ellenőrzések';
$labels['to'] = 'to';
$labels['of'] = 'of';
$labels['success_release'] = 'Selected messages have been released.';
$labels['success_delete'] = 'Selected messages have been deleted.';
$labels['recipient'] = 'Címzett';

$labels['db_conect_error'] = 'Database connection error.';
$labels['db_query_error'] = 'Database query error.';
$labels['db_policy_error'] = 'Database policy error.';
$labels['spam_quarantine_cutoff_level_error'] = 'Spam cutoff level must be an integer number between 20 and 1001';
?>
