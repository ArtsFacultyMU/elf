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
 * Prints a particular instance of newmodule
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage newassignment
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** config.php */
require_once('../../config.php');
/** Include locallib.php */
require_once($CFG->dirroot . '/mod/newassignment/locallib.php');

require_once($CFG->dirroot.'/mod/newassignment/renderable.php');

$id = required_param('id', PARAM_INT);  // Course Module ID
$userid = required_param('user', PARAM_INT); 
$url = new moodle_url('/mod/newassignment/submissions.php', array('id' => $id)); // Base URL

$user = $DB->get_record('user', array('id' =>$userid));
// get the request parameters
$cm = get_coursemodule_from_id('newassignment', $id, 0, false, MUST_EXIST);

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

// Auth
require_login($course, true, $cm);
$PAGE->set_url($url);

$context = context_module::instance($cm->id);

require_capability('mod/newassignment:view', $context);

$assignment = new NewAssignment($context,$cm,$course);

$PAGE->set_pagelayout('popup');
$PAGE->set_title(get_string("modulename",'newassignment'));
$PAGE->set_heading($assignment->get_instance()->name.' - '.$user->firstname.' '.$user->lastname);

$output = $PAGE->get_renderer('mod_newassignment');

$versions = array();

$versions = $assignment->get_all_user_submissions_with_feedbacks($user->id);

echo $output->header();

echo $OUTPUT->heading(get_string('studentsallversions','newassignment'), 3);



foreach($versions as $version) {
	$t = new html_table();
	$t->width = '100%';
	$row = new html_table_row();
	$cell1 = new html_table_cell(get_string('submissionversion','newassignment').': '.$version->version); 
	$row->cells = array($cell1);
	$t->data[] = $row;
	
	$row = new html_table_row();
	$cell1 = new html_table_cell(get_string('submitted','newassignment')); 
	$cell2 = new html_table_cell($assignment->get_submission_plugin()->view($version->submissionid));
	$cell3 = new html_table_cell(userdate($version->submissionmodified));
	$cell1->attributes['width'] = 150;
	$cell3->attributes['width'] = 180;
	$row->cells = array($cell1, $cell2, $cell3);
	$t->data[] = $row;
	
	if(isset($version->grade)) {
		$grader = $DB->get_record('user', array('id' => $version->grader));
		if($grader) {
			$row = new html_table_row();
			$cell1 = new html_table_cell(get_string('graded','newassignment'));
		
			$cell2 = new html_table_cell($grader->firstname.' '.$grader->lastname); //TODO - add link to user profile
		
			$cell3 = new html_table_cell(userdate($version->feedbackmodified));
			$row->cells = array($cell1, $cell2, $cell3);
			$t->data[] = $row;
		}
		
		$actualgrade = '-';
		$gradingmanager = get_grading_manager($assignment->get_context(), 'mod_newassignment', 'submissions');
		if ($controller = $gradingmanager->get_active_controller()) {
			if($version->grade) {
				$grade = new stdClass;
				$grade->id = $version->gradeid;
				$grade->grade = $version->grade;
				$actualgrade = $assignment->display_advanced_grade($grade);
			}
		} else {
			if($version->grade)
				$actualgrade = $assignment->display_grade($version->grade,false);
		}
		
		$row = new html_table_row();
		$cell1 = new html_table_cell(get_string('grade'));
		$cell2 = new html_table_cell($actualgrade);
		$row->cells = array($cell1, $cell2,'');
		$t->data[] = $row;
	}
	
	
	require_once($CFG->dirroot.'/mod/newassignment/feedbacks/comment.php');
	$fcomment = new mod_newassignment_feedback_comment($assignment);
	if($comment = $fcomment->view($version->feedbackid)) {
		$row = new html_table_row();
		$cell1 = new html_table_cell(get_string('feedbackcomment','newassignment'));
		$cell2 = new html_table_cell($comment);
		$cell2->colspan = 2;
		$row->cells = array($cell1, $cell2);
		$t->data[] = $row;
	}
	
	require_once($CFG->dirroot.'/mod/newassignment/feedbacks/file.php');
	$ffile = new mod_newassignment_feedback_file($assignment);
	if($comment = $ffile->view($version->feedbackid)) {
		$row = new html_table_row();
		$cell1 = new html_table_cell(get_string('feedbackfile','newassignment'));
		$cell2 = new html_table_cell($comment);
		$cell2->colspan = 2;
		$row->cells = array($cell1, $cell2);
		$t->data[] = $row;
	}
	echo html_writer::table($t);
}



echo $output->footer();