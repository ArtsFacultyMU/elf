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
 * The main page for the New Assignment Remover plugin.
 *
 * @package    local_newassignment_remover
 * @copyright  2022 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('course', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

require_login($course);
$PAGE->set_url('/local/newassignment_remover/index.php', array('course' => $courseid));
$PAGE->set_pagelayout('incourse');

// Check permissions.
$context = context_course::instance($course->id);
require_capability('local/newassignment_remover:use', $context);
$PAGE->set_title($course->shortname . ': ' . get_string('pluginname', 'local_newassignment_remover'));
$PAGE->set_heading($course->fullname);

// Get all mod_newassignment instances in the course.
$newassignments = get_all_instances_in_course('newassignment', $course);

// Printing page content.
echo $OUTPUT->header();
echo html_writer::tag('h2', get_string('pluginname', 'local_newassignment_remover'));
/// If there are mod_newassignment instances present, print task list.
if ($newassignments) {
    echo html_writer::tag('p', get_string('tasklist', 'local_newassignment_remover'));
    $table_data = [
        [
            '1.',
            get_string('create_assign', 'local_newassignment_remover'),
            $OUTPUT->continue_button(
                    new moodle_url('/local/newassignment_remover/process/create_assign.php', array('course' => $courseid))),
        ], [
            '2.',
            get_string('manual_check', 'local_newassignment_remover'),
            '',
        ], [
            '3.',
            get_string('remove_newassignment', 'local_newassignment_remover'),
            $OUTPUT->continue_button(
                    new moodle_url('/local/newassignment_remover/process/delete_newassignment.php', array('course' => $courseid))),
        ]
    ];
    $table = new html_table($table_data);
    $table->data = $table_data;
    echo html_writer::table($table);
/// If there are no mod_newassignment instances, print only an info message.
} else {
    echo html_writer::tag('p', get_string('no_newassignment', 'local_newassignment_remover'));
}
echo $OUTPUT->footer();