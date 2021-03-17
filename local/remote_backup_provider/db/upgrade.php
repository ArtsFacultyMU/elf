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
 * Web service definitions for local_remote_backup_provider
 *
 * @package    local_remote_backup_provider
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_remote_backup_provider_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020051400) {
        // Define table local_remotebp_remotes to be created.
        $table = new xmldb_table('local_remotebp_remotes');
      
        // Adding fields to table local_remotebp_remotes.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '19', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('address', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('token', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('active', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('position', XMLDB_TYPE_INTEGER, '19', null, XMLDB_NOTNULL, null, null);
      
        // Adding keys to table local_remotebp_remotes.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
      
        // Conditionally launch create table for local_remotebp_remotes.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Move token and remote url to the new structure.
        if (!empty(get_config('local_remote_backup_provider', 'remotesite'))
                && !empty(get_config('local_remote_backup_provider', 'wstoken'))) {
            $remote = new stdClass();
            $remote->name = rtrim(trim(get_config('local_remote_backup_provider', 'remotesite')), '/\\');
            $remote->address = rtrim(trim(get_config('local_remote_backup_provider', 'remotesite')), '/\\');
            $remote->token = get_config('local_remote_backup_provider', 'wstoken');
            $remote->active = 1;

            // Set position to the highest (meaning last highest + 1).
            $position_array = $DB->get_records('local_remotebp_remotes', null, 'position', 'position');
            $last_position = array_pop($position_array);
            $remote->position = 0;
            if ($last_position !== null) $remote->position = (int)$last_position->position + 1;

            $DB->insert_record('local_remotebp_remotes', $remote);
        }

        upgrade_plugin_savepoint(true, 2020051400, 'local', 'remote_backup_provider');
    }
      
    if ($oldversion < 2020052800) {
        // Define table local_remotebp_transfer to be created.
        $table = new xmldb_table('local_remotebp_transfer');

        // Adding fields to table local_remotebp_transfer.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('remoteid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('remotecourseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('remotecoursename', XMLDB_TYPE_CHAR, '254', null, null, null, null);
        $table->add_field('remotebackupurl', XMLDB_TYPE_CHAR, '1024', null, null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'added');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_remotebp_transfer.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_remotebp_transfer.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_remotebp_transfer_log to be created.
        $table = new xmldb_table('local_remotebp_transfer_log');

        // Adding fields to table local_remotebp_transfer_log.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('transferid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '1024', null, XMLDB_NOTNULL, null, null);
        $table->add_field('notes', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table local_remotebp_transfer_log.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_remotebp_transfer_log.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2020052800, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2020080500) {
        // Define table local_remotebp_categories to be created.
        $table = new xmldb_table('local_remotebp_categories');

        // Adding fields to table local_remotebp_categories.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('remoteid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('remotecategoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_remotebp_categories.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for local_remotebp_categories.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2020080500, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2020090100.01) {

        // Rename field status on table local_remotebp_transfer_log to fullstatus.
        $table = new xmldb_table('local_remotebp_transfer_log');
        $field = new xmldb_field('status', XMLDB_TYPE_CHAR, '1024', null, XMLDB_NOTNULL, null, null, 'status');
        // Launch rename field status.
        $dbman->rename_field($table, $field, 'fullstatus');
        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2020090100.01, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2020090100.02) {
        // Define field status to be added to local_remotebp_transfer_log.
        $table = new xmldb_table('local_remotebp_transfer_log');
        $field = new xmldb_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'added', 'timemodified');

        // Conditionally launch add field status.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2020090100.02, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2020090100.03) {
        $DB->set_field('local_remotebp_transfer_log', 'status', 'added', ['fullstatus' => 'Added.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'error', ['fullstatus' => 'Configuration error: No remote address.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'error', ['fullstatus' => 'Configuration error: No remote token.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Remote backup started.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'error', ['fullstatus' => 'Remote backup: User not found.']);

        $DB->set_field('local_remotebp_transfer_log', 'status', 'error', ['fullstatus' => 'Remote backup wrong HTTP code.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'error', ['fullstatus' => 'Remote backup URL not starting with remote address.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Remote backup ended successfully.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Transfering backup started.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'error', ['fullstatus' => 'Transfering backup failed on missing remote backup URL.']);

        $DB->set_field('local_remotebp_transfer_log', 'status', 'error', ['fullstatus' => 'Transfering backup failed on creating file.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Transfering backup ended successfully.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Restoration started.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'error', ['fullstatus' => 'Restoration file invalid.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'error', ['fullstatus' => 'Restoration prechecks failed.']);

        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Restoration ended successfully.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Enroling teacher to the course.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Teacher enroled successfully.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Categorizing the course.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Getting category id from remote.']);  

        $DB->set_field('local_remotebp_transfer_log', 'status', 'finished', ['fullstatus' => 'Categorization finished successfully.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Looking for corresponding local category.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Category found.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Remote category not found locally, creating.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Looking for parent category.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Creating a new local category.']);
        $DB->set_field('local_remotebp_transfer_log', 'status', 'processing', ['fullstatus' => 'Saving link to newly created category for later use in transfers.']);

        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2020090100.03, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2020091000) {
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'added', ['fullstatus' => 'Added.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'conf_noremote', ['fullstatus' => 'Configuration error: No remote address.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'conf_notoken', ['fullstatus' => 'Configuration error: No remote token.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'backup_started', ['fullstatus' => 'Remote backup started.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'backup_usernotfound', ['fullstatus' => 'Remote backup: User not found.']);

        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'backup_invalidhttpcode', ['fullstatus' => 'Remote backup wrong HTTP code.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'backup_invalidurlstart', ['fullstatus' => 'Remote backup URL not starting with remote address.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'backup_ended', ['fullstatus' => 'Remote backup ended successfully.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'transfer_started', ['fullstatus' => 'Transfering backup started.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'transfer_missingurl', ['fullstatus' => 'Transfering backup failed on missing remote backup URL.']);

        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'transfer_failedfilecreation', ['fullstatus' => 'Transfering backup failed on creating file.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'transfer_ended', ['fullstatus' => 'Transfering backup ended successfully.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'restore_started', ['fullstatus' => 'Restoration started.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'restore_invalidfile', ['fullstatus' => 'Restoration file invalid.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'restore_prechecksfailed', ['fullstatus' => 'Restoration prechecks failed.']);

        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'restore_ended', ['fullstatus' => 'Restoration ended successfully.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'teacherenrol_started', ['fullstatus' => 'Enroling teacher to the course.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'teacherenrol_ended', ['fullstatus' => 'Teacher enroled successfully.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'categorization_started', ['fullstatus' => 'Categorizing the course.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'categorization_gettingremotecatid', ['fullstatus' => 'Getting category id from remote.']);  

        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'categorization_ended', ['fullstatus' => 'Categorization finished successfully.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'categorization_lookingforlocalcat', ['fullstatus' => 'Looking for corresponding local category.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'categorization_catfound', ['fullstatus' => 'Category found.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'categorization_remotenotfoundlocally', ['fullstatus' => 'Remote category not found locally, creating.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'categorization_lookingforparent', ['fullstatus' => 'Looking for parent category.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'categorization_creatingnewcat', ['fullstatus' => 'Creating a new local category.']);
        $DB->set_field('local_remotebp_transfer_log', 'fullstatus', 'categorization_savingforlater', ['fullstatus' => 'Saving link to newly created category for later use in transfers.']);

        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2020091000, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2020091000.01) {
        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2020091000.01, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2020091000.02) {

        // Define field issuer to be added to local_remotebp_transfer.
        $table = new xmldb_table('local_remotebp_transfer');
        $field = new xmldb_field('issuer', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'userid');

        // Conditionally launch add field issuer.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2020091000.02, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2020091000.03) {
        $DB->execute('UPDATE {local_remotebp_transfer} SET `issuer`=`userid`');

        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2020091000.03, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2020092000) {
        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2020092000, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2020100700) {
        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2020100700, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2021012000) {
        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2021012000, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2021030400) {
        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2021030400, 'local', 'remote_backup_provider');
    }

    if ($oldversion < 2021031800) {
        // Remote_backup_provider savepoint reached.
        upgrade_plugin_savepoint(true, 2021031800, 'local', 'remote_backup_provider');
    }
}