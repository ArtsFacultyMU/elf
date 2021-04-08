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
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

/**
 * Creates and retrieves a course backup by course id.
 *
 * @package    local_remote_backup_provider
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_course_backup_by_id extends \external_api {
    /**
     * Parameter description for get_course_backup_by_id().
     *
     * @return \external_function_parameters
     */
    public static function get_course_backup_by_id_parameters() {
        return new \external_function_parameters(
            array(
                'id' => new \external_value(PARAM_INT, 'id'),
                'username' => new \external_value(PARAM_USERNAME, 'username'),
            )
        );
    }

    /**
     * Creates and retrieves a course backup by course id.
     *
     * The user is looked up by username as it is not a given that user ids match
     * across platforms.
     *
     * @param int $id The course id.
     * @param string $username The username
     * @return array|bool An array containing the url or false on failure
     */
    public static function get_course_backup_by_id($id, $username) {
        global $CFG, $DB;

        // Validate parameters passed from web service.
        $params = self::validate_parameters(
            self::get_course_backup_by_id_parameters(), array('id' => $id, 'username' => $username)
        );

        // Extract the userid from the username.
        $userid = $DB->get_field('user', 'id', array('username' => $username));

        // Instantiate controller.
        $bc = new \backup_controller(
            \backup::TYPE_1COURSE, $id, \backup::FORMAT_MOODLE, \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, $userid);

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
                $setting->set_status(\backup_setting::NOT_LOCKED);

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
        $bc->set_status(\backup::STATUS_AWAITING);
        $bc->execute_plan();
        $result = $bc->get_results();

        if (isset($result['backup_destination']) && $result['backup_destination']) {
            $file = $result['backup_destination'];
            $context = \context_course::instance($id);
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
            $fileurl = \moodle_url::make_webservice_pluginfile_url(
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
     * Return value description for get_course_backup_by_id().
     *
     * @return \external_description
     */
    public static function get_course_backup_by_id_returns() {
        return new \external_single_structure(
            array(
                'url' => new \external_value(PARAM_RAW, 'url of the backup file'),
            )
        );
    }
}