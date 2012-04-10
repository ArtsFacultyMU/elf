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
 * mod_data data generator
 *
 * @package    mod_data
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Page module PHPUnit data generator class
 *
 * @package    mod_data
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_data_generator extends phpunit_module_generator {

    /**
     * Create new data module instance
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass activity record with extra cmid field
     */
    public function create_instance($record = null, array $options = null) {
        global $DB;

        $this->instancecount++;
        $i = $this->instancecount;

        $record = (object)(array)$record;
        $options = (array)$options;

        if (!isset($record->name)) {
            $record->name = get_string('pluginname', 'data').' '.$i;
        }
        if (!isset($record->intro)) {
            $record->intro = 'Test database '.$i;
        }
        if (!isset($record->introformat)) {
            $record->introformat = FORMAT_MOODLE;
        }
        $record->timemodified = time();

        $id = $DB->insert_record('data', $record);
        $instance = $DB->get_record('data', array('id'=>$id), '*', MUST_EXIST);

        $cm = $this->create_course_module($instance, $options);

        $instance->cmid = $cm->id;

        return $instance;
    }
}
