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
 * This file keeps track of upgrades to the newmodule module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package    mod
 * @subpackage newmodule
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute newmodule upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_newassignment_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    if ($oldversion < 2012092601) {
    	$table = new xmldb_table('newassign_guide_fillings');
    	
    	$table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, true);	
    	$table->add_field('gradeid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
    	$table->add_field('criterionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
    	$table->add_field('remark', XMLDB_TYPE_TEXT, 'big');
    	$table->add_field('remarkformat', XMLDB_TYPE_INTEGER, '2');
    	$table->add_field('score', XMLDB_TYPE_NUMBER, '10', null, XMLDB_NOTNULL);

    	$table->add_key('primary', XMLDB_KEY_PRIMARY,array('id'));
    	$table->add_key('fk_gradeid', XMLDB_KEY_FOREIGN,array('gradeid'), 'newassign_grades',array('id'));
    	$table->add_key('fk_criterionid', XMLDB_KEY_FOREIGN,array('criterionid'), 'gradingform_guide_criteria',array('id'));
    	$table->add_key('uq_grade_criterion', XMLDB_KEY_UNIQUE,array('gradeid','criterionid'));
    	
    	$dbman->create_table($table);   	
    	
    	$table = new xmldb_table('newassign_rubric_fillings');
    	
    	$table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, true);
    	$table->add_field('gradeid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
    	$table->add_field('criterionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
    	$table->add_field('levelid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
    	$table->add_field('remark', XMLDB_TYPE_TEXT, 'big');    	
    	$table->add_field('remarkformat', XMLDB_TYPE_INTEGER, '2');
    	
    	$table->add_key('primary', XMLDB_KEY_PRIMARY,array('id'));
    	$table->add_key('fk_gradeid', XMLDB_KEY_FOREIGN,array('gradeid'), 'newassign_grades',array('id'));
    	$table->add_key('fk_criterionid', XMLDB_KEY_FOREIGN,array('criterionid'), 'gradingform_rubric_criteria',array('id'));
    	$table->add_key('uq_grade_criterion', XMLDB_KEY_UNIQUE,array('gradeid','criterionid'));
    	
    	$table->add_index('ix_levelid',XMLDB_INDEX_NOTUNIQUE,array('levelid'));
    	
    	$dbman->create_table($table);
    	
    }
    
    if($oldversion < 2012093000) {
    	$table = new xmldb_table('newassignment');
    	$field = new xmldb_field('newassigncompletition', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL,false,0,'publishfeedbacksanonymously');
    	$dbman->add_field($table, $field);
    }

    return true;
}
