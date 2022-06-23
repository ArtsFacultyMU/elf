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

use local_remote_backup_provider\helper\subtransfer\post_subtransfer_manager;
use moodle_exception;

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
class process_subplugin_post extends \external_api {
    
    /**
     * Parameter description for process_subplugin_post().
     *
     * @return \external_function_parameters
     */
    public static function process_subplugin_post_parameters() {
        return new \external_function_parameters(
            array(
                'subplugin' => new \external_value(\PARAM_TEXT, 'Subplugins name without prefix.'),
                'function' => new \external_value(\PARAM_TEXT, 'Name of the function.'),
                'data' => new \external_value(\PARAM_TEXT, 'Data in a form of JSON.'),
            )
        );
    }

    /**
     * Deletes course backup.
     *
     * @param int $timestamp Timestamp.
     * @return array|bool An array containing the url or false on failure.
     */
    public static function process_subplugin_post($subplugin, $function, $data) {
        $subplugins = post_subtransfer_manager::get_subplugins();
        if (!in_array($subplugin, $subplugins)) {
            throw new \moodle_exception('Invalid subplugin');
        }
        
        $data_unpacked = json_decode($data);

        $class = sprintf('\remotebppost_%s\external\%s', $subplugin, $function);
        return ['data' => json_encode($class::$function($data_unpacked))];  
    }

    /**
     * Return value description for process_subplugin_post().
     *
     * @return \external_description
     */
    public static function process_subplugin_post_returns() {
        return new \external_single_structure(
            array(
                'data' => new \external_value(PARAM_TEXT, 'JSON encoded data.'),
            )
        );
    }
}