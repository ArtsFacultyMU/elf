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

// get the request parameters
$id = required_param('id', PARAM_INT);
list ($course, $cm) = get_course_and_cm_from_cmid($id, 'newassignment');

// Auth
$url = new moodle_url('/mod/newassignment/view.php', array('id' => $id)); // Base URL
$PAGE->set_url($url);
require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/newassignment:view', $context);

$assignment = new NewAssignment($context,$cm,$course);

// Mark as viewed
$completion=new completion_info($course);
$completion->set_module_viewed($cm);

// Get the assign to render the page
echo $assignment->view(optional_param('action', '', PARAM_TEXT));

