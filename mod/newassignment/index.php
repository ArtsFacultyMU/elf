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
 * This is a one-line short description of the file
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage newmodule
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// Replace newmodule with the name of your module and remove this line

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // course

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course);

add_to_log($course->id, 'newassignment', 'view all', 'index.php?id='.$course->id, '');

$coursecontext = context_course::instance($course->id);

$PAGE->set_url('/mod/newassignment/index.php', array('id' => $id));
$PAGE->set_pagelayout('incourse');

// Print the header
$strplural = get_string("modulenameplural", "newassignment");
$PAGE->navbar->add($strplural);
$PAGE->set_title($strplural);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

if (! $newassignments = get_all_instances_in_course('newassignment', $course)) {
    notice(get_string('thereareno', 'moodle', $strplural), new moodle_url('/course/view.php', array('id' => $course->id)));
    die;
}

// Check if we need the closing date header
$table = new html_table();
$table->head  = array ($strplural, get_string('duedate', 'newassignment'), get_string('submissions', 'newassignment'));
$table->align = array ('left', 'left', 'center');
$table->data = array();
foreach ($newassignments as $assignment) {
    $cm = get_coursemodule_from_instance('newassignment', $assignment->id, 0, false, MUST_EXIST);

    $link = html_writer::link(new moodle_url('/mod/newassignment/view.php', array('id' => $cm->id)), $assignment->name);
	$date = '-';
    if (!empty($assignment->duedate)) {
        $date = userdate($assignment->duedate);
    }
    $submissions = $DB->count_records_sql('SELECT COUNT(*) FROM (SELECT ss.id, ss.userid, ss.version, ss.assignment FROM (SELECT userid, MAX(version) AS maxversion FROM {newassign_submissions} WHERE assignment = :assignmentid1 GROUP BY userid) x INNER JOIN {newassign_submissions} ss ON x.userid = ss.userid AND ss.version = x.maxversion  WHERE assignment = :assignmentid2) s', array('assignmentid1'=>$cm->instance,'assignmentid2'=>$cm->instance));
    $row = array($link, $date, $submissions);
    $table->data[] = $row;

}
echo html_writer::table($table);
echo $OUTPUT->footer();
