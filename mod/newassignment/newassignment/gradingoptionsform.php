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

/**
 * Assignment grading options form
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_newassignment_grading_options_form extends moodleform {
    /**
     * Define this form - called from the parent constructor
     */
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;

        // hidden params
        $mform->addElement('hidden', 'contextid', $instance['contextid']);
        $mform->setType('contextid', PARAM_INT);
        $mform->addElement('hidden', 'id', $instance['cm']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'userid', $instance['userid']);
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'action', 'saveoptions');
        $mform->setType('action', PARAM_ALPHA);
        
        $mform->addElement('header', 'general', get_string('gradingoptions', 'newassignment'));
        // visible elements
        $options = array(-1=>get_string('all'),10=>'10', 20=>'20', 50=>'50', 100=>'100');
        $mform->addElement('select', 'perpage', get_string('assignmentsperpage', 'newassignment'), $options);
        $options = array('' => get_string('filternone', 'newassignment'),
                         NEWASSIGN_FILTER_SUBMITTED => get_string('filtersubmitted', 'newassignment'),
                         NEWASSIGN_FILTER_REQUIRE_GRADING => get_string('filterrequiregrading', 'newassignment'));
        $mform->addElement('select', 'filter', get_string('filter', 'newassignment'), $options);

        // quickgrading
        if ($instance['showquickgrading']) {
            $mform->addElement('checkbox', 'quickgrading', get_string('quickgrading', 'newassignment'), '');
            $mform->addHelpButton('quickgrading', 'quickgrading', 'newassignment');
            $mform->setDefault('quickgrading', $instance['quickgrading']);
        }

        // buttons
        $mform->addElement('submit', 'submitbutton', get_string('updatetable', 'newassignment'));
        
    }
}

