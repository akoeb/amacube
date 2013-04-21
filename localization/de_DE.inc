<?php
/*
Language File for amacube

This file is part of the amacube Roundcube plugin
Copyright (C) 2013, Alexander Köb <nerdkram@koeb.me>

Licensed under the GNU General Public License version 3. 
See the COPYING file for a full license statement.          

*/

$labels = array();
$labels['amavissettings_pagetitle'] = 'Amavis Einstellungen';
$labels['policy_default_message'] = 'Bisher wurden keine Benutzerspezifischen Einstellungen gefunden, verwende Voreinstellungen.';
$labels['section activate'] = 'Aktivierung';
$labels['spam_check'] = 'Aktiviere Spamprüfung';
$labels['spam_check_active'] = 'aktiviere Spamprüfung';
$labels['spam_check_active_info'] = 'Klicke hier um die Prüfung eingehender Mails auf Spam zu aktivieren.';
$labels['virus_check'] = 'Aktiviere Virusprüfung';
$labels['virus_check_active'] = 'aktiviere Virusprüfung';
$labels['virus_check_active_info'] = 'Klicke hier um die Prüfung eingehender Mails auf Viren zu aktivieren.';
$labels['section quarantine'] = 'Quarantäne';
$labels['spam_quarantine'] = 'Quarantäne von Spam Mails';
$labels['spam_quarantine_active'] = 'Quarantäne von Spam Mails';
$labels['spam_quarantine_active_info'] = 'Klicke hier um als Spam erkannte Mails in die Quarantäne zu nehmen.';
$labels['virus_quarantine'] = 'Quarantäne von Virus Mails';
$labels['virus_quarantine_active'] = 'Quarantäne von Virus Mails';
$labels['virus_quarantine_active_info'] = 'Klicke hier um Virus Emails in die Quarantäne zu nehmen.';
$labels['banned_quarantine'] = 'Quarantäne von gefährlichen Inhalten';
$labels['banned_quarantine_active'] = 'Quarantäne von gefährlichen Inhalten';
$labels['banned_quarantine_active_info'] = 'Klick hier um Emails mit gefährlichen Inhalten in die Quarantäne zu nehmen';
$labels['section adjust levels'] = 'Festlegen der Spamwerstufen';
$labels['spam_tag2_level'] = 'Spamwert für Spamverdacht';
$labels['spam_tag2_level_info'] = 'Emails mit einem Spamwert in dieser Höhe oder mehr könnte spam sein, wird aber noch zugestellt. Ein X-Spam-Header mit dem Wert Yes wird hinzugefügt.';
$labels['spam_tag2_level_error'] = 'Der Wert für Spamverdacht muss eine ganze Zahl zwischen -20 und 20 sein.';
$labels['spam_kill_level'] = 'Spamwert für abgewiesene Spam';
$labels['spam_kill_level_info'] = 'Emails mit einem Spamwert in dieser Höhe oder mehr werden nicht mehr akzeptiert und zurückgewiesen. Wennn Spam-Quarantäne aktiviert ist können diese Emails dennoch eingesehen werden';
$labels['spam_kill_level_error'] = 'Der Wert für Spammails muss eine ganze Zahl zwischen -20 und 20 sein.';
$labels['verification_error'] = 'Die Amaviseinstellungen enthielten einen Fehler und konnten nicht gespeichert werden.';
$labels['write_error'] = 'Es kam zu einem Fehler beim Abspeichern der Amaviseinstellungen';
$labels['successfully saved'] = 'Die Amaviseinstellungen für diesen Benutzer wurden erfolgreich abgespeichert.';
$labels['quarantine heading'] = 'Quarantäne-Emails';
$labels['quarantine_no_result'] = 'Es konnten keine Emails in der Quarantäne gefunden werden';
$labels['quarantine_pagetitle'] = 'Quarantäne-Emails';
$labels['intersection_error'] = 'Fehler: Es kann eine E-Mail nicht gleichzeitig gelöscht und aus der Quarantäne freigegeben werden!';
$labels['content_decode_V'] = 'Virus';
$labels['content_decode_B'] = 'Verbotener Inhalt';
$labels['content_decode_U'] = 'Ungeprüft';
$labels['content_decode_S'] = 'Spam';
$labels['content_decode_Y'] = 'Spamverdacht';
$labels['content_decode_M'] = 'Mime Fehler';
$labels['content_decode_H'] = 'Header Fehler';
$labels['content_decode_O'] = 'Größenbeschränkung';
$labels['content_decode_T'] = 'MTA Fehler';
$labels['content_decode_C'] = 'Sauber';


?>
