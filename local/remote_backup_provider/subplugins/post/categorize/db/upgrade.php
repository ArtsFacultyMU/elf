<?php


defined('MOODLE_INTERNAL') || die();

function xmldb_remotebppost_categorize_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();
    if ($oldversion < 2022062200) {

        // Define table remotebppost_categorize to be created.
        $table = new xmldb_table('remotebppost_categorize');

        // Adding fields to table remotebppost_categorize.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('remoteid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('remotecategoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table remotebppost_categorize.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for remotebppost_categorize.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Categorize savepoint reached.
        upgrade_plugin_savepoint(true, 2022062200, 'remotebppost', 'categorize');
    }
}