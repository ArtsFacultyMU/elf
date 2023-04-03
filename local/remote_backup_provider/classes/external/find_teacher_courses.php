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
 * Looks for courses in the instance of MOODLE limited for specific teacher.
 *
 * @package    local_remote_backup_provider
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class find_teacher_courses extends \external_api {
    /**
     * Parameter description for find_teacher_courses().
     *
     * @return \external_function_parameters
     */
    public static function find_teacher_courses_parameters() {
        return new \external_function_parameters(
            array(
                'search' => new \external_value(PARAM_TEXT, 'Value from the search field'),
                'username' => new \external_value(PARAM_TEXT, 'Username'),
                'auth' => new \external_value(PARAM_AUTH, 'Authentication method')
            )
        );
    }

    /**
     * Find courses by text search filtered by the specific teacher.
     *
     * This function searches the course short name, full name, and idnumber.
     *
     * @param string $search The text to search.
     * @param string $username Teacher's username.
     * @param string $auth Authentication method.
     * 
     * @return array All courses found.
     */
    public static function find_teacher_courses($search, $username, $auth) {
        global $DB;

        // Validate parameters passed from web service.
        $params = self::validate_parameters(
                self::find_teacher_courses_parameters(),
                array(
                    'search' => $search,
                    'username' => $username,
                    'auth' => $auth,
                )
        );

        // Capability check.
        if (!has_capability('moodle/course:viewhiddencourses', \context_system::instance())) {
            return [];
        }


        // Get user's ID by auth (method) & username.
        $user_lookup = $DB->get_record('user', array('username' => $username, 'auth' => $auth), 'id');
        if (!$user_lookup) {
            return [];
        }


        // Fetch courses by user.
        $courses = enrol_get_all_users_courses($user_lookup->id);
        $filtered_courses = [];
        foreach($courses as $course) {
            // Check the name of the course.
            if (mb_stripos($course->fullname, $search, 0, 'UTF-8')===FALSE 
                    && mb_stripos($course->shortname, $search, 0, 'UTF-8')===FALSE
                    && mb_stripos($course->idnumber, $search, 0, 'UTF-8')===FALSE) {
                continue;
            }

            $context = \context_course::instance($course->id);
            $user_roles = get_user_roles($context, $user_lookup->id, false);

            $is_teacher = false;
            foreach ($user_roles as $role) {
                if ($role->shortname === 'editingteacher') {
                    $is_teacher = true;
                    break;
                }
            }

            // Add course to the list of filtered courses, if user is teacher.
            if ($is_teacher) {
                $course_data = new \stdClass();
                $course_data->id = $course->id;
                $course_data->idnumber = $course->idnumber;
                $course_data->shortname = $course->shortname;
                $course_data->fullname = $course->fullname;

                $filtered_courses[] = $course_data;
            }
        }

        return $filtered_courses;
    }

    /**
     * Return value description for find_teacher_courses().
     *
     * @return external_description
     */
    public static function find_teacher_courses_returns() {
        return new \external_multiple_structure(
            new \external_single_structure(
                array(
                    'id'        => new \external_value(PARAM_INT, 'id of course'),
                    'idnumber'  => new \external_value(PARAM_RAW, 'idnumber of course'),
                    'shortname' => new \external_value(PARAM_RAW, 'short name of course'),
                    'fullname'  => new \external_value(PARAM_RAW, 'long name of course'),
                )
            )
        );
    }
}