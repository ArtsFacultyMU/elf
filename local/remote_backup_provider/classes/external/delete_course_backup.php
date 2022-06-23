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
 * Deletes course backup.
 *
 * @package    local_remote_backup_provider
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_course_backup extends \external_api {
    
    /**
     * Parameter description for delete_course_backup().
     *
     * @return \external_function_parameters
     */
    public static function delete_course_backup_parameters() {
        return new \external_function_parameters(
            array(
                'id' => new \external_value(\PARAM_INT, 'ID of the course'),
                'timestamp' => new \external_value(\PARAM_TEXT, 'timestamp'),
            )
        );
    }

    /**
     * Deletes course backup.
     *
     * @param int $timestamp Timestamp.
     * @return array|bool An array containing the url or false on failure.
     */
    public static function delete_course_backup($id, $timestamp) {
        global $DB;

        

        // Validate parameters passed from web service.
        $params = self::validate_parameters(
            self::delete_course_backup_parameters(), array(
                'id' => $id,
                'timestamp' => $timestamp,
            )
        );

        try {
            $context = \context_course::instance($id);

            $fileinfo = array(
                'contextid' => $context->id,
                'component' => 'local_remote_backup_provider',
                'filearea' => 'backup',
                'itemid' => $timestamp,
                'filepath' => '/',
                'filename' => 'foo',
            );

            $fs = get_file_storage();
            $file = $fs->get_file(
                $fileinfo['contextid'],
                $fileinfo['component'],
                $fileinfo['filearea'], 
                $fileinfo['itemid'],
                $fileinfo['filepath'],
                $fileinfo['filename']
            );

            if ($file) {
                $file->delete();
            }

            return True;
        } catch (\Exception $ex) {
            return False;
        }
    }

    /**
     * Return value description for delete_course_backup().
     *
     * @return \external_description
     */
    public static function delete_course_backup_returns() {
        return new \external_value(\PARAM_BOOL, 'TRUE if everything is OK');
    }
}