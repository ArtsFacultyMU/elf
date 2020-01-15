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

namespace local_remote_backup_provider\output\course_list;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/outputcomponents.php');


/**
 * List of found courses.
 *
 * @package   local_remote_backup_provider
 * @copyright 2015 Lafayette College ITS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderable implements \renderable {
    /**
     * Renders the list of courses.
     * 
     * @param string $remote_url URL of the remote
     * @param array $courses List of courses
     */
    public function render($remote_url, $courses) {
        
        if ($courses) {
            # Course links
            $table = new \html_table();
            $table->head = ['', get_string('short_course_name', 'local_remote_backup_provider'), get_string('full_course_name', 'local_remote_backup_provider')];
            
            array_map(function($course) use ($table, $remote_url) {
                $table->data[] = [
                    \html_writer::checkbox('remote[]', $course->id, false, null, ['form' => 'remote_form', 'class'=>'remote_course_checkbox']),
                    $course->shortname,
                    \html_writer::link($remote_url . '/course/view.php?id=' . $course->id, $course->fullname, ['target' => '_blank']),
                ];
            }, $courses);
            return \html_writer::div(\html_writer::table($table), "", ['style' => 'margin-bottom: 20px']);
        } 
        return null;
    }
}