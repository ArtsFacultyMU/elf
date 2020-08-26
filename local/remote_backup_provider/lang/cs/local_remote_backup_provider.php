<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language file for local_remote_backup_provider
 *
 * @package    local_remote_backup_provider
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['remove_old_task'] = 'Odstranit staré soubory zálohy';
$string['create_backup_task'] = 'Vytvořit vzdálenou zálohu';
$string['transfer_backup_task'] = 'Převést vzdálenou zálohu na lokální';
$string['restore_backup_task'] = 'Obnovit kurz ze zálohy';
$string['enrol_teacher_task'] = 'Zapsat učitele do kurzu';
$string['categorize_task'] = 'Zařadit kurz do kategorie';

$string['import'] = 'Nahrávání ze vzdálené instalace';
$string['pluginname'] = 'Poskytovatel vzdálené zálohy';
$string['privacy:metadata'] = 'Modul Poskytovatel vzdálené zálohy neuchovává žádné osobní údaje.';
$string['remotesite'] = 'Vzdálená instalace';
$string['remotesite_desc'] = 'Úplná adresa vzdálené instalace';
$string['wstoken'] = 'Token pro webové služby';
$string['wstoken_desc'] = 'Doplňte token pro webové služby ze vzdálené instalace.';

$string['available_courses_search'] = 'Hledat kurzy ve vzdálené instalaci';
$string['available_courses'] = 'Dostupné kurzy ve vzdálené instalaci';
$string['issued_transfers'] = 'Zadané převody';

$string['short_course_name'] = 'Zkrácený název';
$string['full_course_name'] = 'Celý název';
$string['time_created'] = 'Čas přidání';
$string['status'] = 'Stav';
$string['issuer'] = 'Zadal(a)';
$string['actions'] = 'Akce';
$string['no_courses_found'] = 'Nebyly nalezeny žádné kurzy';
$string['button_import'] = 'Nahrát';
$string['timestamp'] = 'Časová značka';
$string['notes'] = 'Poznámky';


$string['back_to_selection'] = 'Zpět na výběr';
$string['continue_to_course'] = 'Přejít do převedeného kurzu';
$string['courses_issued_for_transfer'] = 'Kurzy jsou nachystány k převodu';

$string['restore_error_invalid_extension'] = 'Obnova nebyla úspěšná: Neplatná přípona souboru.';
$string['exception_tm_restore_error_invalid_backup_file'] = 'Obnova nebyla úspěšná: Neplatný soubor zálohy.';
$string['exception_tm_restore_error_precheck_failed'] = 'Obnova nebyla úspěšná: Selhala předběžná kontrola.';
$string['exception_tm_record_does_not_exist'] = 'Převod nebyl úspěšný: Neexistující záznam v databázi.';

$string['import_success'] = 'Kurz s ID %s byl úspěšně nahrán do nového kurzu <i><a href="%s" target="_blank">%s</a></i>.';
$string['import_failure'] = 'Při nahrávání kurzu s ID %s nastal následující problém:';

$string['invalid_section'] = 'Neplatná sekce';

$string['no_remote'] = 'Nebyla nalezena žádná vzdálená instalace.';
$string['no_token'] = 'Vybraná vzdálená instalace nemá zadán přístupový token.';
$string['no_address'] = 'Vybraná vzdálená instalace nemá zadanou adresu.';
$string['remote_not_found'] = 'Vzdálená instalace nebyla nalezena.';

$string['admin_remote_list'] = 'Seznam vzdálených instalací';
$string['admin_remote_edit'] = 'Upravit vzdálenou instalaci';
$string['admin_remote_add'] = 'Přidat vzdálenou instalaci';
$string['admin_transfer_log'] = 'Záznamy o převodech';
$string['admin_detailed_log'] = 'Detailní záznamy o převodech';

$string['remote_name'] = 'Název';
$string['remote_url'] = 'URL';
$string['remote_token'] = 'Token';
$string['remote_active'] = 'Aktivní';
$string['remote_position'] = 'Pozice';

$string['hide'] = 'Skrýt';
$string['show'] = 'Zobrazit';
$string['move_up'] = 'Posunout nahoru';
$string['move_down'] = 'Posunout dolů';

$string['remote_not_found'] = 'Vzdálená instalace nebyla nalezena.';
$string['remote_added'] = 'Vzdálená instalace byla úspěšně přidána.';
$string['remote_updated'] = 'Vzdálená instalace byla úspěšně upravena.';
$string['transfer_not_found'] = 'Přenos nebyl nalezen.';

$string['transfer_status_added'] = 'Kurz přidán';
$string['transfer_status_error'] = 'Chyba převodu';
$string['transfer_status_processing'] = 'Převod probíhá';
$string['transfer_status_finished'] = 'Převod dokončen';