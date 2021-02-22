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
 * Provides important info about selected category. 
 *
 * @package    local_remote_backup_provider
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_category_info extends \external_api {
    /**
     * Parameter description for get_category_info().
     *
     * @return \external_function_parameters
     */
    public static function get_category_info_parameters() {
        return new \external_function_parameters(
            array(
                'id' => new \external_value(PARAM_TEXT, 'id'),
            )
        );
    }

    /**
     * Get category information.
     *
     * @param int $id the category ID.
     * @return array|bool An array with information or false on failure.
     */
    public static function get_category_info($id) {
        global $DB;
        // Validate parameters passed from web service.
        $params = self::validate_parameters(
            self::get_category_info_parameters(), array('id' => $id)
        );

        // Capability check.
        if (!has_capability('moodle/course:viewhiddencourses', \context_system::instance())) {
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
     * Return value description for get_category_info().
     *
     * @return external_description
     */
    public static function get_category_info_returns() {
        return new \external_single_structure(
            array(
                'id' => new \external_value(PARAM_INT, 'id of the category'),
                'name' => new \external_value(PARAM_TEXT, 'name of the category'),
                'idnumber' => new \external_value(PARAM_TEXT, 'idnumber of the category'),
                'path' => new \external_value(PARAM_TEXT, 'path to the category'),
                'visible' => new \external_value(PARAM_INT, 'is category visible?'),
            )
        );
    }
}