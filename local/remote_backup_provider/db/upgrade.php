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
}