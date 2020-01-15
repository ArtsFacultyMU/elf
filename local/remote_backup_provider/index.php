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
 * Landing page for local_remote_backup_provider
 *
 * @package    local_remote_backup_provider
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('classes/transfer_manager.php');
require_once('output/search_form/renderable.php');
require_once('output/search_form/renderer.php');
require_once('output/course_list/renderable.php');
require_once('output/course_list/renderer.php');

$remote = optional_param_array('remote', [], PARAM_INT);
$search = optional_param('search', '', PARAM_NOTAGS);

require_login();
$PAGE->set_url('/local/remote_backup_provider/index.php');
$PAGE->set_pagelayout('report');
$returnurl = new moodle_url('/', array('redirect' => 0,));

// Check the permissions.
$context = context_system::instance();
require_capability('local/remote_backup_provider:access', $context);

// Get config settings and initiate transfer manager.
try {
    $transfer_manager = new local_remote_backup_provider\transfer_manager();
} catch (Exception $e) {
    print_error('pluginnotconfigured', 'local_remote_backup_provider', $returnurl);
}

$PAGE->set_title(get_string('import', 'local_remote_backup_provider'));
$PAGE->set_heading(get_string('import', 'local_remote_backup_provider'));

echo $OUTPUT->header();

// Display the courses.
if (!empty($search)) {
    $courses = $transfer_manager->search($search);
    echo $OUTPUT->heading(get_string('available_courses', 'local_remote_backup_provider'), 2);
    echo html_writer::tag('p', html_writer::link(new moodle_url('/local/remote_backup_provider/index.php'), $OUTPUT->larrow() . ' ' . get_string('back_to_search', 'local_remote_backup_provider')));
    $course_list_renderable = new local_remote_backup_provider\output\course_list\renderable($courses);
    $course_list_renderer = $PAGE->get_renderer('local_remote_backup_provider', 'course_list');
    echo $course_list_renderer->render_course_list($course_list_renderable, $courses, $transfer_manager->get_remote_url());

// Show the search form.
} else {
    echo $OUTPUT->heading(get_string('available_courses_search', 'local_remote_backup_provider'), 2);
    $form_renderable = new local_remote_backup_provider\output\search_form\renderable();
    $form_renderer = $PAGE->get_renderer('local_remote_backup_provider', 'search_form');
    echo $form_renderer->render_form($form_renderable);
}

echo $OUTPUT->footer();
