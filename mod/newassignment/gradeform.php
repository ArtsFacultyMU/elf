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
 * This file contains the forms to create and edit an instance of this module
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');


/** Include formslib.php */
require_once ($CFG->libdir.'/formslib.php');
/** Include locallib.php */
require_once($CFG->dirroot . '/mod/newassignment/locallib.php');
/** Required for advanced grading */
require_once('HTML/QuickForm/input.php');

/**
 * Assignment grade form
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_newassignment_grade_form extends moodleform {
    /** @var assignment $assignment */
    private $assignment;

    /**
     * Define the form - called by parent constructor
     */
    function definition() {
        $mform = $this->_form;

        list($assignment, $data, $params) = $this->_customdata;
        // visible elements
        $this->assignment = $assignment;
        $assignment->add_grade_form_elements($mform, $data, $params);

        if ($data) {
            $this->set_data($data);
        }
    }

    /**
     * Perform minimal validation on the grade form
     * @param array $data
     * @param array $files
     */
    function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        // advanced grading
        if (!array_key_exists('grade', $data)) {
            return $errors;
        }

        if ($this->assignment->get_instance()->grade > 0) {
            if (unformat_float($data['grade']) === null && (!empty($data['grade']))) {
                $errors['grade'] = get_string('invalidfloatforgrade', 'newassignment', $data['grade']);
            } else if (unformat_float($data['grade']) > $this->assignment->get_instance()->grade) {
                $errors['grade'] = get_string('gradeabovemaximum', 'newassignment', $this->assignment->get_instance()->grade);
            } else if (unformat_float($data['grade']) < 0) {
                $errors['grade'] = get_string('gradebelowzero', 'newassignment');
            }
        } else {
            // this is a scale
            if ($scale = $DB->get_record('scale', array('id'=>-($this->assignment->get_instance()->grade)))) {
                $scaleoptions = make_menu_from_list($scale->scale);
                if (!array_key_exists((int)$data['grade'], $scaleoptions)) {
                    $errors['grade'] = get_string('invalidgradeforscale', 'newassignment');
                }
            }
        }
        return $errors;
    }

}
