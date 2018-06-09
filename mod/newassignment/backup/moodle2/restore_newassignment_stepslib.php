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
 * Define all the restore steps that will be used by the restore_assign_activity_task
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete assignment structure for restore, with file and id annotations
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_newassignment_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the structure of the restore workflow
     * @return restore_path_element $structure
     */
    protected function define_structure() {

        $paths = array();
        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $paths[] = new restore_path_element('newassignment', '/activity/newassignment');
        if ($userinfo) {
            $submission = new restore_path_element('newassignment_submission', '/activity/newassignment/submissions/submission');
            $paths[] = $submission;
            
            $grade = new restore_path_element('newassignment_grade', '/activity/newassignment/submissions/submission/grades/grade');
            $paths[] = $grade;
           
            $feedback = new restore_path_element('newassignment_feedback', '/activity/newassignment/submissions/submission/feedbacks/feedback');
            $paths[] = $feedback;
            
            $subonline = new restore_path_element('newassignment_submission_onlinetext', '/activity/newassignment/submissions/submission/onlinetexts/onlinetext');
            $paths[] = $subonline;
            
            $feedcomment = new restore_path_element('newassignment_feedback_comment', '/activity/newassignment/submissions/submission/feedbacks/feedback/feedcomments/feedcomment');
            $paths[] = $feedcomment;
            
            $gradeguidefillings = new restore_path_element('newassignment_grade_guide_fillings', '/activity/newassignment/submissions/submission/grades/grade/gradeguidefillings/gradeguidefilling');
            $paths[] = $gradeguidefillings;
            
            $graderubricfillings = new restore_path_element('newassignment_grade_rubric_fillings', '/activity/newassignment/submissions/submission/grades/grade/graderubricfillings/graderubricfilling');
            $paths[] = $graderubricfillings;            
            
        }
        //$paths[] = new restore_path_element('assign_plugin_config', '/activity/assign/plugin_configs/plugin_config');

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process an assign restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_newassignment($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->allowsubmissionsfromdate = $this->apply_date_offset($data->allowsubmissionsfromdate);
        $data->duedate = $this->apply_date_offset($data->duedate);


        $newitemid = $DB->insert_record('newassignment', $data);

        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process a submission restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_newassignment_submission($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->assignment = $this->get_new_parentid('newassignment');

        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('newassign_submissions', $data);

        // Note - the old contextid is required in order to be able to restore files stored in
        // sub plugin file areas attached to the submissionid
        $this->set_mapping('submission', $oldid, $newitemid, false, null, $this->task->get_old_contextid());
    }

    /**
     * Process a grade restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_newassignment_grade($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->assignment = $this->get_new_parentid('newassignment');
        
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->grader = $this->get_mappingid('user', $data->grader);
        $data->submission = $this->get_mappingid('submission', $data->submission);
        
        $newitemid = $DB->insert_record('newassign_grades', $data);
        
        // Note - the old contextid is required in order to be able to restore files stored in
        // sub plugin file areas attached to the gradeid
        $this->set_mapping('gradeid', $oldid, $newitemid, false, null, $this->task->get_old_contextid());
    }

    /**
     * Process a plugin-config restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_newassignment_feedback($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->assignment = $this->get_new_parentid('newassignment');

        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->submission = $this->get_mappingid('submission', $data->submission);
		
        $newitemid = $DB->insert_record('newassign_feedbacks', $data);

        // Note - the old contextid is required in order to be able to restore files stored in
        // sub plugin file areas attached to the gradeid
        $this->set_mapping('feedback', $oldid, $newitemid, false, null, $this->task->get_old_contextid());

    }
    
    /**
     * Process a plugin-config restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_newassignment_feedback_comment($data) {
    	global $DB;
    
    	$data = (object)$data;
    	$oldid = $data->id;
    
        $data->feedback = $this->get_mappingid('feedback', $data->feedback);
        
    	$newitemid = $DB->insert_record('newassign_feed_comment', $data);
    
    	// Note - the old contextid is required in order to be able to restore files stored in
    	// sub plugin file areas attached to the gradeid
    	$this->add_related_files('newassignfeedback_comment', 'feedbacks_comment', 'feedback', null, $oldid);
    
    }
    
    /**
     * Process a plugin-config restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_newassignment_submission_onlinetext($data) {
    	global $DB;
    
    	$data = (object)$data;
    	$oldid = $data->id;
    
    	$data->submission = $this->get_mappingid('submission', $data->submission);
    
    	$newitemid = $DB->insert_record('newassign_sub_onlinetext', $data);
    
    	// Note - the old contextid is required in order to be able to restore files stored in
    	// sub plugin file areas attached to the gradeid
    	$this->add_related_files('newassignsubmission_onlinetext', 'submissions_onlinetext', 'submission', null, $oldid);
    
    }
    
    protected function process_newassignment_grade_guide_fillings($data) {
    	global $DB;
    
    	$data = (object)$data;
    	$oldid = $data->id;
    
    	$data->gradeid = $this->get_mappingid('gradeid', $data->gradeid);
    
    	$newitemid = $DB->insert_record('newassign_guide_fillings', $data); 
    }
    
    protected function process_newassignment_grade_rubric_fillings($data) {
    	global $DB;
    
    	$data = (object)$data;
    	$oldid = $data->id;
    
    	$data->gradeid = $this->get_mappingid('gradeid', $data->gradeid);
    
    	$newitemid = $DB->insert_record('newassign_rubric_fillings', $data);
    }

    /**
     * Once the database tables have been fully restored, restore the files
     * @return void
     */
    protected function after_execute() {
        $this->add_related_files('mod_newassignment', 'intro', null);
    }
}
