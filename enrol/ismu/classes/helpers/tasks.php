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

namespace enrol_ismu\helpers;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class to simplify queue of specific adhoc tasks.
 *
 * @package    enrol_ismu
 * @copyright  2016-2021 Masaryk University
 * @author     Filip Benčo & Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 */
class tasks {
    public static function sync_students_from_ismu($courseid) {
        $task = new \enrol_ismu\tasks\adhoc\sync_users_from_ismu;
        $task->set_custom_data(['courseid' => $courseid]);
        \core\task\manager::queue_adhoc_task($task);
    }
    
    public static function sync_students_from_archive($courseid, $enrolid, $period) {
        $task = new \enrol_ismu\tasks\adhoc\sync_users_from_archive;
        $task->set_custom_data(['courseid' => $courseid, 'enrolid' => $enrolid, 'period' => $period]);
        \core\task\manager::queue_adhoc_task($task);
    }
    
    public static function archive_users($courseid, $enrolid, $period) {
        $task = new \enrol_ismu\tasks\adhoc\archive_users;
        $task->set_custom_data(['courseid' => $courseid, 'enrolid' => $enrolid, 'period' => $period]);
        \core\task\manager::queue_adhoc_task($task);
    }
}