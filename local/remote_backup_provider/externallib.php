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
 * Web service library functions
 *
 * @package    local_remote_backup_provider
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/externallib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

/**
 * Web service API definition.
 *
 * @package local_remote_backup_provider
 * @copyright 2015 Lafayette College ITS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_remote_backup_provider_external extends external_api {
    /**
     * Parameter description for find_courses().
     *
     * @return external_function_parameters
     */
    public static function find_courses_parameters() {
        return new external_function_parameters(
            array(
                'search' => new external_value(PARAM_TEXT, 'search'),
            )
        );
    }

    /**
     * Find courses by text search.
     *
     * This function searches the course short name, full name, and idnumber.
     *
     * @param string $search The text to search on
     * @return array All courses found
     */
    public static function find_courses($search) {
        global $DB;

        // Validate parameters passed from web service.
        $params = self::validate_parameters(self::find_courses_parameters(), array('search' => $search));

        // Capability check.
        if (!has_capability('moodle/course:viewhiddencourses', context_system::instance())) {
            return false;
        }

        // Build query.
        $searchsql    = '';
        $searchparams = array();
        $searchlikes = array();
        $searchfields = array('c.shortname', 'c.fullname', 'c.idnumber');
        for ($i = 0; $i < count($searchfields); $i++) {
            $searchlikes[$i] = $DB->sql_like($searchfields[$i], ":s{$i}", false, false);
            $searchparams["s{$i}"] = '%' . $search . '%';
        }
        // We exclude the front page.
        $searchsql = '(' . implode(' OR ', $searchlikes) . ') AND c.id != 1';

        // Run query.
        $fields = 'c.id,c.idnumber,c.shortname,c.fullname';
        $sql = "SELECT $fields FROM {course} c WHERE $searchsql ORDER BY c.shortname ASC";
        $courses = $DB->get_records_sql($sql, $searchparams, 0);
        return $courses;
    }

    /**
     * Parameter description for find_courses().
     *
     * @return external_description
     */
    public static function find_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'        => new external_value(PARAM_INT, 'id of course'),
                    'idnumber'  => new external_value(PARAM_RAW, 'idnumber of course'),
                    'shortname' => new external_value(PARAM_RAW, 'short name of course'),
                    'fullname'  => new external_value(PARAM_RAW, 'long name of course'),
                )
            )
        );
    }

    /**
     * Parameter description for find_teacher_courses().
     *
     * @return external_function_parameters
     */
    public static function find_teacher_courses_parameters() {
        return new external_function_parameters(
            array(
                'search' => new external_value(PARAM_TEXT, 'Value from the search field'),
                'username' => new external_value(PARAM_TEXT, 'Username'),
                'auth' => new external_value(PARAM_AUTH, 'Authentication method')
            )
        );
    }

    /**
     * Find courses by text search filtered by the specific teacher.
     *
     * This function searches the course short name, full name, and idnumber.
     *
     * @param string $search The text to search on
     * @return array All courses found
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
        if (!has_capability('moodle/course:viewhiddencourses', context_system::instance())) {
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

            $context = context_course::instance($course->id);
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
                $course_data = new stdClass();
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
     * Parameter description for find_teacher_courses().
     *
     * @return external_description
     */
    public static function find_teacher_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'        => new external_value(PARAM_INT, 'id of course'),
                    'idnumber'  => new external_value(PARAM_RAW, 'idnumber of course'),
                    'shortname' => new external_value(PARAM_RAW, 'short name of course'),
                    'fullname'  => new external_value(PARAM_RAW, 'long name of course'),
                )
            )
        );
    }

    /**
     * Parameter description for get_course_backup_by_id().
     *
     * @return external_function_parameters
     */
    public static function get_course_backup_by_id_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'id'),
                'username' => new external_value(PARAM_USERNAME, 'username'),
            )
        );
    }

    /**
     * Create and retrieve a course backup by course id.
     *
     * The user is looked up by username as it is not a given that user ids match
     * across platforms.
     *
     * @param int $id the course id
     * @param string $username The username
     * @return array|bool An array containing the url or false on failure
     */
    public static function get_course_backup_by_id($id, $username) {
        global $CFG, $DB;

        // Validate parameters passed from web service.
        $params = self::validate_parameters(
            self::get_course_backup_by_id_parameters(), array('id' => $id, 'username' => $username)
        );

        //ini_set('max_execution_time', 1);
        //sleep(10);

        // Extract the userid from the username.
        $userid = $DB->get_field('user', 'id', array('username' => $username));

        // Instantiate controller.
        $bc = new backup_controller(
            \backup::TYPE_1COURSE, $id, backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_GENERAL, $userid);

        // Alter the initial backup settings.
        $backupsettings = array (
            'users' => 0,               // Include enrolled users (default = 1)
            'anonymize' => 0,           // Anonymize user information (default = 0)
            'role_assignments' => 1,    // Include user role assignments (default = 1)
            'activities' => 1,          // Include activities (default = 1)
            'blocks' => 1,              // Include blocks (default = 1)
            'filters' => 1,             // Include filters (default = 1)
            'comments' => 1,            // Include comments (default = 1)
            'userscompletion' => 0,     // Include user completion details (default = 1)
            'logs' => 0,                // Include course logs (default = 0)
            'grade_histories' => 0      // Include grade history (default = 0)
        );

        foreach ($bc->get_plan()->get_tasks() as $taskindex => $task) {
            $settings = $task->get_settings();
            foreach ($settings as $settingindex => $setting) {
                $setting->set_status(backup_setting::NOT_LOCKED);

                // Modify the values of the intial backup settings
                if ($taskindex == 0) {
                    foreach ($backupsettings as $key => $value) {
                        if ($setting->get_name() == $key) {
                            $setting->set_value($value);
                        }
                    }
                }
            }
        }        

        // Run the backup.
        $bc->set_status(backup::STATUS_AWAITING);
        $bc->execute_plan();
        $result = $bc->get_results();

        if (isset($result['backup_destination']) && $result['backup_destination']) {
            $file = $result['backup_destination'];
            $context = context_course::instance($id);
            $fs = get_file_storage();
            $timestamp = time();

            $filerecord = array(
                'contextid' => $context->id,
                'component' => 'local_remote_backup_provider',
                'filearea' => 'backup',
                'itemid' => $timestamp,
                'filepath' => '/',
                'filename' => 'foo',
                'timecreated' => $timestamp,
                'timemodified' => $timestamp
            );
            $storedfile = $fs->create_file_from_storedfile($filerecord, $file);
            $file->delete();

            // Make the link.
            $filepath = $storedfile->get_filepath() . $storedfile->get_filename();
            $fileurl = moodle_url::make_webservice_pluginfile_url(
                $storedfile->get_contextid(),
                $storedfile->get_component(),
                $storedfile->get_filearea(),
                $storedfile->get_itemid(),
                $storedfile->get_filepath(),
                $storedfile->get_filename()
            );
            return array('url' => $fileurl->out(true));
        } else {
            return false;
        }
    }

    /**
     * Parameter description for get_course_backup_by_id().
     *
     * @return external_description
     */
    public static function get_course_backup_by_id_returns() {
        return new external_single_structure(
            array(
                'url' => new external_value(PARAM_RAW, 'url of the backup file'),
            )
        );
    }

    /**
     * Parameter description for get_course_name_by_id().
     *
     * @return external_function_parameters
     */
    public static function get_course_name_by_id_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'id'),
            )
        );
    }

    /**
     * Get name of the course.
     *
     * @param int $id the course id
     * @return array|bool An array containing the url or false on failure
     */
    public static function get_course_name_by_id($id) {
        global $DB;
        // Validate parameters passed from web service.
        $params = self::validate_parameters(
            self::get_course_name_by_id_parameters(), array('id' => $id)
        );

        // Capability check.
        if (!has_capability('moodle/course:viewhiddencourses', context_system::instance())) {
            return false;
        }

        $course = $DB->get_record('course', array('id' => $id));

        if ($course === false) return false;
        return ['name' => $course->fullname];
    }

    /**
     * Parameter description for get_course_name_by_id().
     *
     * @return external_description
     */
    public static function get_course_name_by_id_returns() {
        return new external_single_structure(
            array(
                'name' => new external_value(PARAM_TEXT, 'name of the course'),
            )
        );
    }

    /**
     * Parameter description for get_course_category_by_id().
     *
     * @return external_function_parameters
     */
    public static function get_course_category_by_id_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'id'),
            )
        );
    }

    /**
     * Get name of the course.
     *
     * @param int $id the course id
     * @return array|bool An array containing the url or false on failure
     */
    public static function get_course_category_by_id($id) {
        global $DB;
        // Validate parameters passed from web service.
        $params = self::validate_parameters(
            self::get_course_category_by_id_parameters(), array('id' => $id)
        );

        // Capability check.
        if (!has_capability('moodle/course:viewhiddencourses', context_system::instance())) {
            return false;
        }

        $course = $DB->get_record('course', array('id' => $id));

        if ($course === false) return false;
        return ['category' => $course->category];
    }

    /**
     * Parameter description for get_course_name_by_id().
     *
     * @return external_description
     */
    public static function get_course_category_by_id_returns() {
        return new external_single_structure(
            array(
                'category' => new external_value(PARAM_INT, 'id of the category'),
            )
        );
    }

    /**
     * Parameter description for get_course_category_by_id().
     *
     * @return external_function_parameters
     */
    public static function get_category_info_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_TEXT, 'id'),
            )
        );
    }

    /**
     * Get name of the course.
     *
     * @param int $id the course id
     * @return array|bool An array containing the url or false on failure
     */
    public static function get_category_info($id) {
        global $DB;
        // Validate parameters passed from web service.
        $params = self::validate_parameters(
            self::get_course_name_by_id_parameters(), array('id' => $id)
        );

        // Capability check.
        if (!has_capability('moodle/course:viewhiddencourses', context_system::instance())) {
            return false;
        }

        $category = $DB->get_record('course_categories', array('id' => $id));

        if ($category === false) return false;
        return [
            'id' => $category->id,
            'name' => $category->name,
            'idnumber' => $category->idnumber,
            'path' => $category->path,
            'visible' => (int)$category->visible,
        ];
    }

    /**
     * Parameter description for get_course_name_by_id().
     *
     * @return external_description
     */
    public static function get_category_info_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'id of the category'),
                'name' => new external_value(PARAM_TEXT, 'name of the category'),
                'idnumber' => new external_value(PARAM_TEXT, 'idnumber of the category'),
                'path' => new external_value(PARAM_TEXT, 'path to the category'),
                'visible' => new external_value(PARAM_INT, 'is category visible?'),
            )
        );
    }
}
