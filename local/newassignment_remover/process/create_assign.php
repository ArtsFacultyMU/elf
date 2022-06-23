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
 * Creation of the Assignment instances based on the New Assignment ones.
 *
 * @package    local_newassignment_remover
 * @copyright  2022 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/modlib.php');

$courseid = required_param('course', PARAM_INT);

$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
require_login($course);

// Check permissions.
$context = context_course::instance($course->id);
require_capability('local/newassignment_remover:use', $context);

// Get all instances of mod_newassignment to be looped over.
$newassignments = get_all_instances_in_course('newassignment', $course);

foreach ($newassignments as $newassignment) {
    \local_newassignment_remover\converter::convert($course, $newassignment);
}
purge_caches();

redirect(new \moodle_url('/local/newassignment_remover/index.php', ['course' => $courseid]), 
        get_string('assign_creation_finished', 'local_newassignment_remover'), null, 
        \core\output\notification::NOTIFY_SUCCESS);
