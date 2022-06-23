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
class front_course_list_renderable implements \renderable, \templatable {
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

    public function export_for_template(\renderer_base $output) {
        $output = new \stdClass();

        $output->found_courses = (bool)$this->courses;
        $output->courses = $this->courses;
        $output->form_id = self::FORM_ID;
        $output->remote_address = $this->remote->address;
        $output->remote_id = $this->remote->id;

        $context = \context_system::instance();
        $output->transfer_as_other = has_capability('local/remote_backup_provider:transferasother', $context);

        return $output;
    }
}