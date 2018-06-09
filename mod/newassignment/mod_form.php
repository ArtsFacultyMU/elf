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
 * The main newmodule configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod
 * @subpackage newassignment
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once('locallib.php');
/**
 * Module instance settings form
 */
class mod_newassignment_mod_form extends moodleform_mod {
	
    /**
     * Defines forms elements
     */
    public function definition() {

		global $CFG, $DB, $COURSE;
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('assignmentname', 'newassignment'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements(get_string('description', 'newassignment'));

        $ctx = null;
        if ($this->current && $this->current->coursemodule) {
            $cm = get_coursemodule_from_instance('newassignment', $this->current->id, 0, false, MUST_EXIST);
            $ctx = context_module::instance($cm->id, MUST_EXIST);
        }
		if ($this->current && $this->current->course) {
            if (!$ctx) {
                $ctx = context_course::instance($this->current->course);
            }
            $course = $DB->get_record('course', array('id'=>$this->current->course), '*', MUST_EXIST);
        }

        $mform->addElement('header', 'general', get_string('settings', 'newassignment'));
        $mform->addElement('date_time_selector', 'allowsubmissionsfromdate', get_string('allowsubmissionsfromdate', 'newassignment'), array('optional'=>true));
        $mform->addHelpButton('allowsubmissionsfromdate', 'allowsubmissionsfromdate', 'newassignment');
        $mform->setDefault('allowsubmissionsfromdate', false);
        $mform->addElement('date_time_selector', 'duedate', get_string('duedate', 'newassignment'), array('optional'=>true));
        $mform->addHelpButton('duedate', 'duedate', 'newassignment');
        $mform->setDefault('duedate', false);
        $mform->addElement('selectyesno', 'alwaysshowdescription', get_string('alwaysshowdescription', 'newassignment'));
        $mform->addHelpButton('alwaysshowdescription', 'alwaysshowdescription', 'newassignment');
        $mform->setDefault('alwaysshowdescription', 1);
        $mform->disabledIf('alwaysshowdescription', 'allowsubmissionsfromdate[enabled]','notchecked');
        $mform->addElement('selectyesno', 'preventlatesubmissions', get_string('preventlatesubmissions', 'newassignment'));
        $mform->addHelpButton('preventlatesubmissions', 'preventlatesubmissions', 'newassignment');
        $mform->setDefault('preventlatesubmissions', 0);
        $mform->disabledIf('preventlatesubmissions', 'duedate[enabled]','notchecked');
        $mform->addElement('selectyesno', 'sendnotifications', get_string('sendnotifications', 'newassignment'));
        $mform->addHelpButton('sendnotifications', 'sendnotifications', 'newassignment');
        $mform->setDefault('sendnotifications', 1);
        $mform->addElement('selectyesno', 'sendlatenotifications', get_string('sendlatenotifications', 'newassignment'));
        $mform->addHelpButton('sendlatenotifications', 'sendlatenotifications', 'newassignment');
        $mform->setDefault('sendlatenotifications', 1);
        $mform->disabledIf('sendlatenotifications', 'sendnotifications', 'eq', 1);
        $mform->disabledIf('sendlatenotifications', 'preventlatesubmissions', 'neq', 0);

        // plagiarism enabling form
        if (!empty($CFG->enableplagiarism)) {
            /** Include plagiarismlib.php */
            require_once($CFG->libdir . '/plagiarismlib.php');
            plagiarism_get_form_elements_module($mform, $ctx, 'mod_newassignment');
        }

        //submission settings
        //preparation
        $choices = get_max_upload_sizes($CFG->maxbytes, $COURSE->maxbytes);
        
        //element creation
        $mform->addElement('header', 'general', get_string('submissionsettings', 'newassignment'));
        $mform->addElement('select', 'submissiontype', get_string('submissiontype','newassignment'), array(NEWASSIGN_SUBMISSION_FILE => get_string('filesubmission','newassignment'), NEWASSIGN_SUBMISSION_ONLINE_TEXT => get_string('onlinetextsubmission','newassignment')));
        $mform->addHelpButton('submissiontype', 'submissiontype', 'newassignment');
        $mform->addElement('select', 'submissionmaxfilesize', get_string('submissionmaxfilesize', 'newassignment'), $choices);
        $mform->disabledIf('submissionmaxfilesize', 'submissiontype', 'neq', NEWASSIGN_SUBMISSION_FILE);
        $mform->addHelpButton('submissionmaxfilesize', 'submissionmaxfilesize', 'newassignment');
        $mform->setDefault('submissiontype', NEWASSIGN_SUBMISSION_FILE);
        
        $options = array();
        for($i = 1; $i <= 20; $i++) {
        	$options[$i] = $i;
        }
        
        $mform->addElement('select', 'submissionmaxfilecount', get_string('maxfilessubmission', 'newassignment'), $options);
        $mform->addHelpButton('submissionmaxfilecount', 'maxfilessubmission', 'newassignment');
        $mform->setDefault('submissionmaxfilecount', 1);
        $mform->disabledIf('submissionmaxfilecount', 'submissiontype', 'neq', NEWASSIGN_SUBMISSION_FILE);
        $mform->addElement('advcheckbox', 'submissioncomments', get_string('submissioncomments', 'newassignment'), '');
        $mform->addHelpButton('submissioncomments', 'submissioncomments', 'newassignment');
        
        //Publishing settings
        $mform->addElement('header', 'general', get_string('publish', 'newassignment'));
        $mform->addElement('select', 'publishsubmissions', get_string('publishsubmissions','newassignment'), array(NEWASSIGN_PUBLISH_SUBMISSIONS_NO => get_string('no'), NEWASSIGN_PUBLISH_SUBMISSIONS_AFTER_SUBMISSION => get_string('aftersubmission','newassignment'),NEWASSIGN_PUBLISH_SUBMISSIONS_AFTER_ACHIEVEMENT=>get_string('afterachievement','newassignment')));
        $mform->addHelpButton('publishsubmissions', 'publishsubmissions', 'newassignment');
        $mform->addElement('advcheckbox', 'publishsubmissionsanonymously', get_string('publishsubmissionsanonymously', 'newassignment'), '');
        $mform->addHelpButton('publishsubmissionsanonymously', 'publishsubmissionsanonymously', 'newassignment');
        $mform->disabledIf('publishsubmissionsanonymously', 'publishsubmissions', 'eq', NEWASSIGN_PUBLISH_SUBMISSIONS_NO);
        $mform->addElement('select', 'publishfeedbacks', get_string('publishfeedbacks','newassignment'), array(NEWASSIGN_PUBLISH_FEEDBACKS_NO => get_string('no'),NEWASSIGN_PUBLISH_FEEDBACKS_FILES => get_string('onlyfiles','newassignment'),NEWASSIGN_PUBLISH_FEEDBACKS_FILES_COMMENTS=>get_string('filesandcomments','newassignment')));
        $mform->addHelpButton('publishfeedbacks', 'publishfeedbacks', 'newassignment');
        $mform->addElement('advcheckbox', 'publishfeedbacksanonymously', get_string('publishfeedbacksanonymously', 'newassignment'), '');
        $mform->addHelpButton('publishfeedbacksanonymously', 'publishfeedbacksanonymously', 'newassignment');
        $mform->disabledIf('publishfeedbacksanonymously', 'publishfeedbacks', 'eq', NEWASSIGN_PUBLISH_FEEDBACKS_NO);
        $mform->addElement('select', 'publishtime', get_string('publishtime','newassignment'), array(NEWASSIGN_PUBLISH_NOW => get_string('publishnow','newassignment'), NEWASSIGN_PUBLISH_AFTER_SUBMISSION => get_string('publishaftersubmission','newassignment'),NEWASSIGN_PUBLISH_AFTER_DUEDATE=>get_string('publishafterduedate','newassignment')));
        $mform->addHelpButton('publishtime', 'publishtime', 'newassignment');
        
        
        $this->standard_grading_coursemodule_elements();
        $options = array(
	        NEWASSIGN_GRADEHIGHEST => get_string('grade'.NEWASSIGN_GRADEHIGHEST, 'newassignment'),
	        NEWASSIGN_GRADEAVERAGE => get_string('grade'.NEWASSIGN_GRADEAVERAGE, 'newassignment'),
	        NEWASSIGN_ATTEMPTFIRST => get_string('grade'.NEWASSIGN_ATTEMPTFIRST, 'newassignment'),
	        NEWASSIGN_ATTEMPTLAST  => get_string('grade'.NEWASSIGN_ATTEMPTLAST, 'newassignment')
    	);
        $mform->addElement('select','grademethod',get_string('grademethod', 'newassignment'),$options);
        $mform->addHelpButton('grademethod','grademethod','newassignment');
        $mform->setDefault('grademethod', NEWASSIGN_ATTEMPTLAST);
        
        $this->standard_coursemodule_elements();

        $mform->disabledIf('completionusegrade','grade','eq',0);
        
        $this->add_action_buttons();
    }

    public function add_completion_rules() {
	    $mform =& $this->_form;
	
	    
	    $mform->addElement('checkbox', 'newassigncompletition',get_string('completition','newassignment'), get_string('completition_desc','newassignment'));
	    $mform->addHelpButton('newassigncompletition', 'completition', 'newassignment');
	
	    return array('newassigncompletition');
	}
	
	function completion_rule_enabled($data) {
		return (!empty($data['newassigncompletition']));
	}
	
	function data_preprocessing(&$default_values) {
		parent::data_preprocessing($default_values);
	
		// Set up the completion checkboxes which aren't part of standard data.
		// We also make the default value (if you turn on the checkbox) for those
		// numbers to be 1, this will not apply unless checkbox is ticked.
		$default_values['newassigncompletition']=
		!empty($default_values['newassigncompletition']) ? 1 : 0;
		if (empty($default_values['newassigncompletition'])) {
			$default_values['newassigncompletition']=0;
		}
	}
	
	function get_data() {
		$data = parent::get_data();
		if (!$data) {
			return false;
		}
		// Turn off completion settings if the checkboxes aren't ticked
		$autocompletion = !empty($data->completion) && $data->completion==COMPLETION_TRACKING_AUTOMATIC;
		if (empty($data->newassigncompletition) || !$autocompletion) {
			$data->newassigncompletition = 0;
		}
		return $data;
	}
}
