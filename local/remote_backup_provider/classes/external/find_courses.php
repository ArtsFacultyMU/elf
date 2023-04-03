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
 * Looks for courses in the instance of MOODLE.
 *
 * @package    local_remote_backup_provider
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class find_courses extends \external_api {

    /**
     * Parameter description for find_courses().
     *
     * @return \external_function_parameters
     */
    public static function find_courses_parameters() {
        return new \external_function_parameters(
            array(
                'search' => new \external_value(PARAM_TEXT, 'search'),
            )
        );
    }

    /**
     * Find courses by text search.
     *
     * This function searches the course short name, full name, and idnumber.
     *
     * @param string $search The text to search.
     * @return array All courses found.
     */
    public static function find_courses($search) {
        global $DB;

        // Validate parameters passed from web service.
        $params = self::validate_parameters(self::find_courses_parameters(), array('search' => $search));

        // Capability check.
        if (!has_capability('moodle/course:viewhiddencourses', \context_system::instance())) {
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
     * Return value description for find_courses().
     *
     * @return external_description
     */
    public static function find_courses_returns() {
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