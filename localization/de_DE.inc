<?php
/*
Language File for amacube

This file is part of the amacube Roundcube plugin
Copyright (C) 2013, Alexander Köb <nerdkram@koeb.me>

Licensed under the GNU General Public License version 3. 
See the COPYING file for a full license statement.          

*/

$labels = array();

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
$labels['section adjust levels'] = 'Festlegen der Spamwehrstufen';
$labels['spam_tag2_level'] = 'Spamwert für Spamverdacht';
$labels['spam_tag2_level_info'] = 'Emails mit einem Spamwert in dieser Höhe oder mehr könnte spam sein, wird aber noch zugestellt. Ein X-Spam-Header mit dem Wert Yes wird hinzugefügt.';
$labels['spam_tag2_level_error'] = 'Der Wert für Spamverdacht muss eine ganze Zahl zwischen -20 und 20 sein.';
$labels['spam_kill_level'] = 'Spamwert für abgewiesene Spam';
$labels['spam_kill_level_info'] = 'Emails mit einem Spamwert in dieser Höhe oder mehr werden nicht mehr akzeptiert und zurückgewiesen. Wennn Spam-Quarantäne aktiviert ist können diese Emails dennoch eingesehen werden';
$labels['spam_kill_level_error'] = 'Der Wert für Spammails muss eine ganze Zahl zwischen -20 und 20 sein.';
$labels['verification_error'] = 'Die Amaviseinstellungen enthielten einen Fehler und konnten nicht gespeichert werden.';
$labels['write_error'] = 'Es kam zu einem Fehler beim Abspeichern der Amaviseinstellungen';
$labels['config_saved'] = 'Die Amaviseinstellungen für diesen Benutzer wurden erfolgreich abgespeichert.';
$labels['successfully saved'] = 'Die Amaviseinstellungen für diesen Benutzer wurden erfolgreich abgespeichert.';
$labels['quarantine heading'] = 'Quarantäne E-Mails';
$labels['quarantine_no_result'] = 'Es konnten keine E-Mails in der Quarantäne gefunden werden';
$labels['quarantine_pagetitle'] = 'Quarantäne E-Mails';
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

$labels['filter_settings_pagetitle'] = 'Spam-Filter';
$labels['section_checks'] = 'Prüfungen';
$labels['section_quarantine'] = 'Quarantäne';
$labels['section_delivery'] = 'Zustellung';
$labels['section_levels'] = 'Levels';
$labels['spam_quarantine_cutoff_level'] = 'Spamwert für zulöschende Spam';
$labels['process'] = 'Verarbeiten';
$labels['quarantine'] = 'Quarantäne';
$labels['spam_delivery'] = 'E-Mails mit Spam';
$labels['virus_delivery'] = 'E-Mails mit Viren';
$labels['banned_delivery'] = 'E-Mails mit verbotenen Dateien';
$labels['bad_header_delivery'] = 'E-Mails mit ungültigen Kopfzeilen';
$labels['deliver'] = 'Zustellen';
$labels['discard'] = 'Löschen';
$labels['process'] = 'Verarbeiten';
$labels['email_filter_disabled'] = 'E-Mail Filter abgeschaltet';
$labels['email_filter_enabled'] = 'E-Mail Filter eingeschaltet';
$labels['process_quarantine'] = 'Ausgewählte Nachrichten werden freigegeben oder gelöscht';
$labels['release'] = 'Freigeben';
$labels['delete'] = 'Löschen';
$labels['subject'] = 'Betreff';
$labels['received'] = 'Erhalten';
$labels['mailtype'] = 'Mail typ';
$labels['spamlevel'] = 'Spamwert';
$labels['messages'] = 'Nachrichten';
$labels['levels'] = 'Levels';
$labels['status'] = 'Status';
$labels['checks'] = 'Prüfungen';
$labels['to'] = 'an';
$labels['of'] = 'von';
$labels['success_release'] = 'Ausgewählte Nachrichten wurden freigegeben.';
$labels['success_delete'] = 'Ausgewählte Nachrichten wurden gelöscht.';
$labels['recipient'] = 'Empfänger';

$labels['db_conect_error'] = 'Datenbankverbindungsfehler.';
$labels['db_query_error'] = 'Datenbankabfragefehler.';
$labels['db_policy_error'] = 'Datenbankrichtlinienfehler.';
$labels['spam_quarantine_cutoff_level_error'] = 'Der Wert für zulöschende Spammails muss eine ganze Zahl zwischen 20 und 1001 sein.';
?>
