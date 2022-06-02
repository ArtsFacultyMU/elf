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

namespace local_remote_backup_provider\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/externallib.php");

/**
 * Returns category ID by course ID.
 *
 * @package    local_remote_backup_provider
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_course_category_by_id extends \external_api {
    /**
     * Parameter description for get_course_category_by_id().
     *
     * @return \external_function_parameters
     */
    public static function get_course_category_by_id_parameters() {
        return new \external_function_parameters(
            array(
                'id' => new \external_value(PARAM_INT, 'id'),
            )
        );
    }

    /**
     * Returns category ID by course ID.
     *
     * @param int $id the course id
     * @return array|bool An array containing the ID or false on failure
     */
    public static function get_course_category_by_id($id) {
        global $DB;
        // Validate parameters passed from web service.
        $params = self::validate_parameters(
            self::get_course_category_by_id_parameters(), array('id' => $id)
        );

        // Capability check.
        if (!has_capability('moodle/course:viewhiddencourses', \context_system::instance())) {
            return false;
        }

        $course = $DB->get_record('course', array('id' => $id));

        if ($course === false) return false;
        return ['category' => $course->category];
    }

    /**
     * Return value description for get_course_category_by_id().
     *
     * @return external_description
     */
    public static function get_course_category_by_id_returns() {
        return new \external_single_structure(
            array(
                'category' => new \external_value(PARAM_INT, 'id of the category'),
            )
        );
    }
}