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

namespace local_remote_backup_provider\output\renderables;

use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/outputcomponents.php');


/**
 * List of found courses.
 *
 * @package   local_remote_backup_provider
 * @copyright 2020 Masaryk University
 * @author    Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class front_course_list_renderable implements \renderable {
    const FORM_ID = 'remote_form';

    /**
     * List of found courses.
     * 
     * @var array
     */
    protected $courses = [];

    /**
     * Used remote.
     * 
     * @var stdClass
     */
    protected $remote = null;

    public function setRemote(stdClass $remote) {
        $this->remote = $remote;
    }

    public function getRemote() {
        return $this->remote;
    }

    public function setCourses(array $courses) {
        $this->courses = $courses;
    }

    public function hasCourses() {
        return (bool)$this->courses;
    }

    /**
     * Render the list of courses.
     */
    public function render() {
        
        if ($this->courses) {
            # Course links
            $table = new \html_table();
            $table->head = ['', get_string('short_course_name', 'local_remote_backup_provider'), get_string('full_course_name', 'local_remote_backup_provider')];
            
            array_map(function($course) use ($table) {
                $table->data[] = [
                    \html_writer::checkbox('remote_id[]', $course->id, false, null, ['form' => self::FORM_ID, 'class'=>'remote_course_checkbox']),
                    $course->shortname,
                    \html_writer::link($this->remote->url . '/course/view.php?id=' . $course->id, $course->fullname, ['target' => '_blank']),
                ];
            }, $this->courses);
            return \html_writer::div(\html_writer::table($table), "", ['style' => 'margin: 20px 0']);
        } 
        return \html_writer::div(\html_writer::tag('i', get_string('no_courses_found', 'local_remote_backup_provider')) . '.', "", ['style' => 'margin-bottom: 20px']);
    }
}