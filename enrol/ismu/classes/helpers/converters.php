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
 * Helper class to transfer data from one format to another.
 *
 * @package    enrol_ismu
 * @copyright  2016-2021 Masaryk University
 * @author     Filip Benčo & Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 */
class converters {
    public static function ismu_to_moodle_settings($data, $settingsobject = null) {
        if($settingsobject != null) {
            $settingsobject->customint1 = $data->enrol_ismu_enrol_status;
            $settingsobject->customint2 = $data->enrol_ismu_create_seminars;
            $settingsobject->customchar1 = $data->enrol_ismu_course_codes;
            $settingsobject->customchar2 = $data->enrol_ismu_period;
            return $settingsobject;
        } else {
            return [
               'customint1' => $data->enrol_ismu_enrol_status ?? 0,
               'customint2' => $data->enrol_ismu_create_seminars ?? 0,
               'customchar1' => $data->enrol_ismu_course_codes ?? '',
               'customchar2' => $data->enrol_ismu_period ?? semester::get_current_semester()->full(),
            ];
        }
    }
    
    public static function moodle_to_ismu_settings($data) {
        return (object) [
           'enrol_ismu_enrol_status' => $data->customint1 ?? 0,
           'enrol_ismu_create_seminars' => $data->customint2 ?? 0,
           'enrol_ismu_course_codes' => $data->customchar1 ?? '',
           'enrol_ismu_period' => $data->customchar2 ?? semester::get_current_semester()->full(),
        ];
    }
}