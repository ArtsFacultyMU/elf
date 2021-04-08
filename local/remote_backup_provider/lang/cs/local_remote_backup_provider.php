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

$string['import'] = 'Nahrávání ze vzdálené instalace';
$string['pluginname'] = 'Poskytovatel vzdálené zálohy';
$string['privacy:metadata'] = 'Modul Poskytovatel vzdálené zálohy neuchovává žádné osobní údaje.';
$string['remotesite'] = 'Vzdálená instalace';
$string['remotesite_desc'] = 'Úplná adresa vzdálené instalace';
$string['wstoken'] = 'Token pro webové služby';
$string['wstoken_desc'] = 'Doplňte token pro webové služby ze vzdálené instalace.';

$string['remote_backup_provider:access'] = 'Převádění kurzů – základní přístup';
$string['remote_backup_provider:searchall'] = 'Hledání napříč všemi kurzy';
$string['remote_backup_provider:transferasother'] = 'Převádění za jiného uživatele';
$string['remote_backup_provider:multitransfer'] = 'Přeskočení omezení jednoho převodu';
$string['remote_backup_provider:managetransfers'] = 'Správa převodů';

$string['available_courses_search'] = 'Hledat kurzy ve vzdálené instalaci';
$string['available_courses'] = 'Dostupné kurzy ve vzdálené instalaci';
$string['issued_transfers'] = 'Zadané převody';

$string['search_all'] = 'Vybírat ze všech kurzů';
$string['transfer_as'] = 'Převést jako:';
$string['user_not_found'] = 'Uživatel nebyl nalezen.';
$string['issued_by_other_user'] = 'Převod byl zadán jiným uživatelem.';
$string['course_id'] = 'ID kurzu';
$string['issued_by'] = 'převedl';

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
$string['remote_course'] = 'Kurz ve vzdálené instalaci';

$string['back_to_selection'] = 'Zpět na výběr';
$string['continue_to_course'] = 'Přejít do převedeného kurzu';
$string['courses_issued_for_transfer'] = 'Kurzy jsou nachystány k převodu';
$string['courses_issued_for_transfer_error'] = '<p>Následující kurzy nelze zadat k převodu, již byly do této verze ELFu převedeny:</p>
{$a->errors}
<p>Pokud potřebujete novou verzi kurzu v rámci stávající instalace ELFu, můžete vytvořit jeho kopii (viz <a href="{$a->link}">návod</a>).</p>';

$string['remote_set_as_hidden'] = 'Vzdálená instalace byla úspěšně skryta.';
$string['remote_set_as_visible'] = 'Vzdálená instalace byla úspěšně zobrazena.';
$string['remote_already_hidden'] = 'Vzdálená instalace již byla skrytá.';
$string['remote_already_visible'] = 'Vzdálená instalace již byla viditelná.';

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
$string['course_not_found'] = 'Kurz nenalezen.';

$string['admin_general_settings'] = "Obecná nastavení";
$string['admin_remote_list'] = 'Seznam vzdálených instalací';
$string['admin_remote_edit'] = 'Upravit vzdálenou instalaci';
$string['admin_remote_add'] = 'Přidat vzdálenou instalaci';
$string['admin_transfer_log'] = 'Záznamy o převodech';
$string['admin_detailed_log'] = 'Detailní záznamy o převodech';
$string['admin_manual_cancel'] = 'Zrušit převod ručně';
$string['admin_manual_finish'] = 'Dokončit převod ručně';

$string['remote_name'] = 'Název';
$string['remote_url'] = 'URL';
$string['remote_token'] = 'Token';
$string['remote_active'] = 'Aktivní';
$string['remote_position'] = 'Pozice';

$string['task_maximum_transfer_time'] = 'Maximální doba převodu';
$string['task_maximum_transfer_time_description'] = 'Po určené době bude automaticky rušen převod. Nula znamená bez limitu.';

$string['hide'] = 'Skrýt';
$string['show'] = 'Zobrazit';
$string['move_up'] = 'Posunout nahoru';
$string['move_down'] = 'Posunout dolů';

$string['remote_not_found'] = 'Vzdálená instalace nebyla nalezena.';
$string['remote_added'] = 'Vzdálená instalace byla úspěšně přidána.';
$string['remote_updated'] = 'Vzdálená instalace byla úspěšně upravena.';
$string['transfer_not_found'] = 'Přenos nebyl nalezen.';
$string['transfer_already_canceled'] = 'Převod byl již zrušen.';
$string['transfer_already_finished'] = 'Převod byl již dokončen.';
$string['transfer_manualcancel_areyousure'] = 'Vážně chcete zrušit následující kurz?';
$string['transfer_canceled_successfully'] = 'Převod byl úspěšně zrušen.';
$string['transfer_canceled_successfully'] = 'Převod byl úspěšně dokončen.';

$string['transfer_as_self'] = 'Převést za sebe';
$string['transfer_as_other'] = 'Převést jménem jiného uživatele';

$string['transfer_status_added'] = 'Přidáno';
$string['transfer_status_error'] = 'Chyba';
$string['transfer_status_processing'] = 'Probíhá';
$string['transfer_status_finished'] = 'Dokončeno';
$string['transfer_status_canceled'] = 'Zrušeno';

$string['transfer_fullstatus_added'] = 'Přidáno.';
$string['transfer_fullstatus_conf_noremote'] = 'Chyba konfigurace: Chybí adresa vzdálené instalace.';
$string['transfer_fullstatus_conf_notoken'] = 'Chyba konfigurace: Chybí token ke vzdálené instalaci.';
$string['transfer_fullstatus_backup_started'] = 'Začala vzdálená záloha.';
$string['transfer_fullstatus_backup_usernotfound'] = 'Nebyl nalezen uživatel ve vzdálené instalaci.';
$string['transfer_fullstatus_backup_invalidhttpcode'] = 'Při vzdálené záloze byl vrácen špatný HTTP kód.';
$string['transfer_fullstatus_backup_invalidurlstart'] = 'Url vzdálené zálohy nezačíná adresou vzdálené instalace.';
$string['transfer_fullstatus_backup_ended'] = 'Vzdálená záloha skončila úspěchem.';
$string['transfer_fullstatus_transfer_started'] = 'Začal převod zálohy.';
$string['transfer_fullstatus_transfer_missingurl'] = 'Převod zálohy selhal: chybí adresa souboru se zálohou.';
$string['transfer_fullstatus_transfer_failedfilecreation'] = 'Převod zálohy selhal: Nepodařilo se vytvořit lokální soubor.';
$string['transfer_fullstatus_transfer_ended'] = 'Převod zálohy skončil úspěchem..';
$string['transfer_fullstatus_restore_started'] = 'Začala obnova.';
$string['transfer_fullstatus_restore_invalidfile'] = 'Soubor obnovy není platný.';
$string['transfer_fullstatus_restore_prechecksfailed'] = 'Selhala předběžná kontrola souboru k obnovení.';
$string['transfer_fullstatus_restore_existingcourse'] = 'Při obnově byl nalezen existující lokální kurz. Data budou nahrazena.';
$string['transfer_fullstatus_restore_newcourse'] = 'Při obnově nebyl nalezen existující lokální kurz. Bude vytvořen nový.';
$string['transfer_fullstatus_restore_newcoursefinished'] = 'Byl vytvořen nový kurz pro obnovu kurzu.';
$string['transfer_fullstatus_restore_itself'] = 'Začala samotná obnova.';
$string['transfer_fullstatus_restore_ended'] = 'Obnova skončila úspěchem.';
$string['transfer_fullstatus_teacherenrol_started'] = 'Začalo zapisování učitele do kurzu.';
$string['transfer_fullstatus_teacherenrol_ended'] = 'Zapisování učitele do kurzu skončilo úspěchem.';
$string['transfer_fullstatus_categorization_started'] = 'Začalo řazení kurzu do kategorie.';
$string['transfer_fullstatus_categorization_gettingremotecatid'] = 'Získávání ID kategorie.';
$string['transfer_fullstatus_categorization_ended'] = 'Kategorizace skončila úspěchem.';
$string['transfer_fullstatus_categorization_lookingforlocalcat'] = 'Hledání odpovídající lokální kategorie.';
$string['transfer_fullstatus_categorization_catfound'] = 'Kategorie nalezena.';
$string['transfer_fullstatus_categorization_remotenotfoundlocally'] = 'Nebyla nalezena lokální kategorie odpovídající vzdálené, vytváří se nová.';
$string['transfer_fullstatus_categorization_lookingforparent'] = 'Hledání nadřazené kategorie.';
$string['transfer_fullstatus_categorization_creatingnewcat'] = 'Vytváření nové lokální kategorie.';
$string['transfer_fullstatus_categorization_savingforlater'] = 'Ukládání vazby k nově vytvořené kategorii pro budoucí užití v přenosech.';
$string['transfer_fullstatus_cancelled_timeout'] = 'Přenos zrušen kvůli vypršení limitu.';
$string['transfer_fullstatus_cancelled_manually'] = 'Přenos zrušen ručně.';
$string['transfer_fullstatus_finished_manually'] = 'Přenos dokončen ručně.';