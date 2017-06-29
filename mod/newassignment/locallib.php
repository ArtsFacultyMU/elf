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
 * Internal library of functions for module newmodule
 *
 * All the newmodule specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod
 * @subpackage newmodule
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

define('NEWASSIGN_SUBMISSION_ONLINE_TEXT', 'onlinetext');
define('NEWASSIGN_SUBMISSION_FILE', 'file');

define('NEWASSIGN_PUBLISH_SUBMISSIONS_NO', 0);
define('NEWASSIGN_PUBLISH_SUBMISSIONS_AFTER_SUBMISSION', 1);
define('NEWASSIGN_PUBLISH_SUBMISSIONS_AFTER_ACHIEVEMENT', 2);

define('NEWASSIGN_PUBLISH_NOW', 0);
define('NEWASSIGN_PUBLISH_AFTER_SUBMISSION', 1);
define('NEWASSIGN_PUBLISH_AFTER_DUEDATE', 2);

define('NEWASSIGN_PUBLISH_SUBMISSIONS_ANONYMOUSLY', 1);

define('NEWASSIGN_PUBLISH_FEEDBACKS_NO', 0);
define('NEWASSIGN_PUBLISH_FEEDBACKS_FILES', 1);
define('NEWASSIGN_PUBLISH_FEEDBACKS_FILES_COMMENTS', 2);

define('NEWASSIGN_PUBLISH_FEEDBACKS_ANONYMOUSLY', 1);

define('NEWASSIGN_FEEDBACK_STATUS_ACCEPTED', 'accepted');
define('NEWASSIGN_FEEDBACK_STATUS_DECLINED', 'declined');

define('NEWASSIGN_FILTER_SUBMITTED', 'submitted');
define('NEWASSIGN_FILTER_REQUIRE_GRADING', 'require_grading');

define('NEWASSIGN_GRADEHIGHEST', 'highest');
define('NEWASSIGN_GRADEAVERAGE', 'average');
define('NEWASSIGN_ATTEMPTFIRST', 'first');
define('NEWASSIGN_ATTEMPTLAST', 'last');

/** grading lib.php */
require_once($CFG->dirroot . '/grade/grading/lib.php');
require_once($CFG->dirroot . '/lib/gradelib.php');
require_once($CFG->dirroot . '/repository/lib.php');

class NewAssignment {

    protected $_instance;
    protected $_context;
    protected $_coursemodule;
    protected $_course;
    protected $_returnaction;
    protected $_returnparams;
    protected $_adminconfig;
    protected $_grading_controller;

    /** @var assign_renderer the custom renderer for this module */
    protected $_output;

    /** @var string modulename prevents excessive calls to get_string */
    private static $modulename = null;

    /** @var string modulenameplural prevents excessive calls to get_string */
    private static $modulenameplural = null;

    public function __construct($coursemodulecontext = null, $coursemodule = null, $course = null) {
        $this->_context = $coursemodulecontext;
        $this->_coursemodule = $coursemodule;
        $this->_course = $course;

        $this->_instance = null;
        $this->_returnaction = null;
        $this->_returnparams = null;

        $this->_adminconfig = null;

        global $PAGE;
        $this->_output = $PAGE->get_renderer('mod_newassignment');
    }

    /**
     * Display the assignment, used by view.php
     *
     * The assignment is displayed differently depending on your role,
     * the settings for the assignment and the status of the assignment.
     * @param string $action The current action if any.
     * @return void
     */
    public function view($action = '') {
        global $CFG;
        /** Include renderable.php */
        require_once($CFG->dirroot . '/mod/newassignment/renderable.php');

        $o = '';
        $mform = null;

        //view preprocessing
        switch ($action) {
            case 'savesubmission':
                $action = 'editsubmission';
                if ($this->process_save_submission($mform))
                    $action = 'view';
                break;
            case 'submitgrade':
                if (optional_param('saveandshownext', null, PARAM_ALPHA)) {
                    //save and show next
                    $action = 'grade';
                    $this->process_save_grade($mform, 'next');
                } else if (optional_param('saveandshowprevious', null, PARAM_ALPHA)) {
                    //save and show next
                    $action = 'grade';
                    $this->process_save_grade($mform, 'previous');
                } else if (optional_param('nosaveandprevious', null, PARAM_ALPHA)) {
                    $action = 'previousgrade';
                } else if (optional_param('nosaveandnext', null, PARAM_ALPHA)) {
                    //show next button
                    $action = 'nextgrade';
                } else if (optional_param('savegrade', null, PARAM_ALPHA)) {
                    //save changes button
                    $action = 'grade';
                    if ($this->process_save_grade($mform)) {
                        $action = 'grading';
                    }
                } else {
                    //cancel button
                    $this->process_cancel();
                    $action = 'grading';
                }
                break;
            case 'saveoptions':
                $this->process_save_grading_options();
                $action = 'grading';
                break;
            case 'quickgrade':
                $message = $this->process_save_quick_grades();
                $action = 'quickgradingresult';
                break;
        }

        $returnparams = array('rownum' => optional_param('rownum', 0, PARAM_INT));
        $this->register_return_link($action, $returnparams);

        // now show the right view page
        //rendering view page

        switch ($action) {
            case 'editsubmission':
                $o .= $this->view_edit_submission_page($mform);
                break;
            case 'grading':
                $o .= $this->view_grading_page();
                break;
            case 'grade':
                $o .= $this->view_single_grade_page($mform);
                break;
            case 'submit':
                $o .= $this->check_submit_for_grading();
                break;
            case 'publish':
                $o .= $this->view_publish_submissions_page();
                break;
            case 'downloadall':
                $o .= $this->download_submissions('all');
                break;
            case 'downloadnotgraded':
                $o .= $this->download_submissions('notgraded');
                break;
            case 'downloadactualversions':
                $o .= $this->download_submissions('actualversions');
                break;
            case 'quickgradingresult' :
                $mform = null;
                $o .= $this->view_quickgrading_result($message);
                break;
            case 'viewsubmission':
                $o .= $this->view_plugin_content('submission');
                break;
            case 'viewfeedback':
                $o .= $this->view_plugin_content('feedback');
                break;
            default :
                $o .= $this->view_submission_page();
                break;
        }

        return $o;
    }

    protected function process_cancel() {
        $rownum = required_param('rownum', PARAM_INT);
        $useridlist = optional_param('useridlist', '', PARAM_TEXT);
        if ($useridlist) {
            $useridlist = explode(',', $useridlist);
        } else {
            $useridlist = $this->get_grading_userid_list();
        }
        $last = false;
        $userid = $useridlist[$rownum];
        $grade = $this->get_user_grade($userid, false);
        if ($grade)
            $this->update_grade($grade);
    }

    public function add_instance($formdata) {
        global $DB, $CFG;
        /** Include local mod_form.php */
        require_once($CFG->dirroot . '/mod/newassignment/mod_form.php');

        $assignment = new stdClass();
        $assignment->name = $formdata->name;
        $assignment->timemodified = time();
        $assignment->course = $formdata->course;
        $assignment->intro = $formdata->intro;
        $assignment->introformat = $formdata->introformat;
        $assignment->alwaysshowdescription = $formdata->alwaysshowdescription;
        $assignment->preventlatesubmissions = $formdata->preventlatesubmissions;
        $assignment->sendnotifications = $formdata->sendnotifications;
        $assignment->sendlatenotifications = $formdata->sendlatenotifications;
        $assignment->duedate = $formdata->duedate;
        $assignment->allowsubmissionsfromdate = $formdata->allowsubmissionsfromdate;
        $assignment->grade = $formdata->grade;
        $assignment->grademethod = $formdata->grademethod;
        $assignment->submissiontype = $formdata->submissiontype;
        $assignment->submissionmaxfilesize = isset($formdata->submissionmaxfilesize) ? $formdata->submissionmaxfilesize : null;
        $assignment->submissionmaxfilecount = isset($formdata->submissionmaxfilecount) ? $formdata->submissionmaxfilecount : null;
        $assignment->submissioncomments = $formdata->submissioncomments;
        $assignment->publishtime = $formdata->publishtime;
        $assignment->publishsubmissions = $formdata->publishsubmissions;
        $assignment->publishsubmissionsanonymously = $formdata->publishsubmissionsanonymously;
        $assignment->publishfeedbacks = $formdata->publishfeedbacks;
        $assignment->publishfeedbacksanonymously = $formdata->publishfeedbacksanonymously;
        $assignment->newassigncompletition = $formdata->newassigncompletition;
        $assignment->timecreated = time();

        $assignment->id = $DB->insert_record('newassignment', $assignment);

        $this->_instance = $assignment;

        $this->update_calendar($formdata->coursemodule);
        //update gradeing settings for this module
        $this->update_gradebook(false, $formdata->coursemodule);

        return $assignment->id;
    }

    public function update_instance($formdata) {
        global $DB;

        $assignment = new stdClass();
        $assignment->id = $formdata->instance;
        $assignment->name = $formdata->name;
        $assignment->timemodified = time();
        $assignment->course = $formdata->course;
        $assignment->intro = $formdata->intro;
        $assignment->introformat = $formdata->introformat;
        $assignment->alwaysshowdescription = $formdata->alwaysshowdescription;
        $assignment->preventlatesubmissions = $formdata->preventlatesubmissions;
        $assignment->sendnotifications = $formdata->sendnotifications;
        $assignment->sendlatenotifications = $formdata->sendlatenotifications;
        $assignment->duedate = $formdata->duedate;
        $assignment->allowsubmissionsfromdate = $formdata->allowsubmissionsfromdate;
        $assignment->grade = $formdata->grade;
        $assignment->grademethod = $formdata->grademethod;
        $assignment->submissiontype = $formdata->submissiontype;
        $assignment->submissionmaxfilesize = isset($formdata->submissionmaxfilesize) ? $formdata->submissionmaxfilesize : null;
        $assignment->submissionmaxfilecount = isset($formdata->submissionmaxfilecount) ? $formdata->submissionmaxfilecount : null;
        $assignment->submissioncomments = $formdata->submissioncomments;
        $assignment->publishtime = $formdata->publishtime;
        $assignment->publishsubmissions = $formdata->publishsubmissions;
        $assignment->publishsubmissionsanonymously = $formdata->publishsubmissionsanonymously;
        $assignment->publishfeedbacks = $formdata->publishfeedbacks;
        $assignment->publishfeedbacksanonymously = $formdata->publishfeedbacksanonymously;
        $assignment->newassigncompletition = $formdata->newassigncompletition;

        $updategrades = false;
        if ($assignment->grade > 0 && $this->get_instance()->grademethod != $assignment->grademethod)
            $updategrades = true;

        $result = $DB->update_record('newassignment', $assignment);
        $this->_instance = $assignment;

        if ($updategrades)
            $this->update_grades();

        $this->update_gradebook(false, $this->get_course_module()->id);
        $this->update_calendar($this->get_course_module()->id);
        return $result;
    }

    /**
     * Actual implementation of the reset course functionality, delete all the
     * assignment submissions for course $data->courseid.
     *
     * @param $data the data submitted from the reset course.
     * @return array status array
     */
    public function reset_userdata($data) {
        global $CFG, $DB;

        $componentstr = get_string('modulenameplural', 'newassignment');
        $status = array();

        $fs = get_file_storage();
        if (!empty($data->reset_newassignment_submissions)) {

            $newassignssql = "SELECT a.id
                             FROM {newassignment} a
                           WHERE a.course=:course";
            $params = array("course" => $data->courseid);


            // Delete files associated with this assignment.
            $plugin = $this->get_submission_plugin();
            $fs->delete_area_files($this->get_context()->id, 'mod_newassignment', $plugin->get_file_area());

            require_once($CFG->dirroot . '/mod/newassignment/feedbacks/comment.php');
            $fcomment = new mod_newassignment_feedback_comment($this);

            require_once($CFG->dirroot . '/mod/newassignment/feedbacks/file.php');
            $ffile = new mod_newassignment_feedback_file($this);
            $fs->delete_area_files($this->get_context()->id, 'mod_newassignment', $ffile->get_file_area());

            $assignmentsIds = $DB->get_records_sql($newassignssql, $params);
            foreach ($assignmentsIds as $assignmentId) {
                $plugin->delete_instance($assignmentId->id);
                $ffile->delete_instance($assignmentId->id);
                $fcomment->delete_instance($assignmentId->id);
            }

            $DB->delete_records_select('newassign_grades', "assignment IN ($newassignssql)", $params);
            $DB->delete_records_select('newassign_feedbacks', "assignment IN ($newassignssql)", $params);
            $DB->delete_records_select('newassign_submissions', "assignment IN ($newassignssql)", $params);
            $status[] = array('component' => $componentstr,
                'item' => get_string('deleteallsubmissions', 'newassignment'),
                'error' => false);

            if (empty($data->reset_gradebook_grades)) {
                // Remove all grades from gradebook.
                require_once($CFG->dirroot . '/mod/newassignment/lib.php');
                newassignment_reset_gradebook($data->courseid);
            }
        }
        // Updating dates - shift may be negative too.
        if ($data->timeshift) {
            shift_course_mod_dates('newassignment', array('duedate', 'allowsubmissionsfromdate'), $data->timeshift, $data->courseid);
            $status[] = array('component' => $componentstr,
                'item' => get_string('datechanged'),
                'error' => false);
        }

        return $status;
    }

    private function update_grades() {
        global $COURSE, $DB;
        if (get_grading_manager($this->get_context(), 'mod_newassignment', 'submissions')->get_active_method() == 'rubic')
            return;
        $context = context_course::instance($COURSE->id);
        $students = $DB->get_records_sql('SELECT userid FROM {role_assignments} WHERE contextid=:contextid AND roleid=5', array('contextid' => $context->id));
        $assign = clone $this->get_instance();
        $assign->cmidnumber = $this->get_course_module()->id;
        foreach ($students as $student) {
            $grade = $this->get_user_grade($student->userid, false);

            if ($this->grading_disabled($student->userid))
                continue;
            if (!$grade)
                $grade = $this->get_final_user_grade($student->userid);
            if ($grade) {
                $gradebookgrade = $this->convert_grade_for_gradebook($grade);
                // Grading is disabled, skip user.
                newassignment_grade_item_update($assign, $gradebookgrade);
            }
        }
    }

    public function get_instance() {
        global $DB;
        if (isset($this->_instance))
            return $this->_instance;
        if ($this->get_course_module())
            $this->_instance = $DB->get_record('newassignment', array('id' => $this->get_course_module()->instance), '*', MUST_EXIST);
        if (!$this->_instance)
            throw new coding_exception('Improper use of the assignment class. Cannot load the assignment record.');
        return $this->_instance;
    }

    public function delete_instance() {
        global $DB;
        $result = true;

        $submissions = $DB->get_records('newassign_submissions', array('assignment' => $this->get_instance()->id));
        foreach ($submissions as $sub) {
            $DB->delete_records('newassign_sub_onlinetext', array('submission' => $sub->id));
        }

        $feedbacks = $DB->get_records('newassign_feedbacks', array('assignment' => $this->get_instance()->id));
        foreach ($feedbacks as $fed) {
            $DB->delete_records('newassign_feed_comment', array('feedback' => $fed->id));
        }

        // delete files associated with this assignment
        $fs = get_file_storage();
        if (!$fs->delete_area_files($this->get_context()->id)) {
            $result = false;
        }

        // delete_records will throw an exception if it fails - so no need for error checking here

        $DB->delete_records('newassign_submissions', array('assignment' => $this->get_instance()->id));
        $DB->delete_records('newassign_feedbacks', array('assignment' => $this->get_instance()->id));

        // delete items from the gradebook
        if (!$this->delete_grades()) {
            $result = false;
        }

        // delete the instance
        $DB->delete_records('newassignment', array('id' => $this->get_instance()->id));

        return $result;
    }

    /**
     * Update the gradebook information for this assignment
     *
     * @param bool $reset If true, will reset all grades in the gradbook for this assignment
     * @param int $coursemoduleid This is required because it might not exist in the database yet
     * @return bool
     */
    public function update_gradebook($reset, $coursemoduleid) {
        global $CFG;
        /** Include lib.php */
        require_once($CFG->dirroot . '/mod/newassignment/lib.php');
        $assignment = clone $this->get_instance();
        $assignment->cmidnumber = $coursemoduleid;
        $param = null;
        if ($reset) {
            $param = 'reset';
        }

        return newassignment_grade_item_update($assignment, $param);
    }

    /**
     * Delete all grades from the gradebook for this assignment
     *
     * @return bool
     */
    private function delete_grades() {
        global $CFG;

        /** gradelib.php */
        require_once($CFG->libdir . '/gradelib.php');

        return grade_update('mod/newassignment', $this->get_course()->id, 'mod', 'newassignment', $this->get_instance()->id, 0, NULL, array('deleted' => 1)) == GRADE_UPDATE_OK;
    }

    /**
     * Update the calendar entries for this assignment
     *
     * @param int $coursemoduleid - Required to pass this in because it might not exist in the database yet
     * @return bool
     */
    public function update_calendar($coursemoduleid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/calendar/lib.php');

        // special case for add_instance as the coursemodule has not been set yet.

        if ($this->get_instance()->duedate) {
            $event = new stdClass();

            if ($event->id = $DB->get_field('event', 'id', array('modulename' => 'newassignment', 'instance' => $this->get_instance()->id))) {

                $event->name = $this->get_instance()->name;

                $event->description = format_module_intro('newassignment', $this->get_instance(), $coursemoduleid);
                $event->timestart = $this->get_instance()->duedate;

                $calendarevent = calendar_event::load($event->id);
                $calendarevent->update($event);
            } else {
                $event = new stdClass();
                $event->name = $this->get_instance()->name;
                $event->description = format_module_intro('newassignment', $this->get_instance(), $coursemoduleid);
                $event->courseid = $this->get_instance()->course;
                $event->groupid = 0;
                $event->userid = 0;
                $event->modulename = 'newassignment';
                $event->instance = $this->get_instance()->id;
                $event->eventtype = 'due';
                $event->timestart = $this->get_instance()->duedate;
                $event->timeduration = 0;

                calendar_event::create($event);
            }
        } else {
            $DB->delete_records('event', array('modulename' => 'newassignment', 'instance' => $this->get_instance()->id));
        }
    }

    /**
     * Get the current course module
     *
     * @return mixed stdClass|null The course module
     */
    public function get_course_module() {
        if ($this->_coursemodule) {
            return $this->_coursemodule;
        }
        if (!$this->_context) {
            return null;
        }
        if ($this->_context->contextlevel == CONTEXT_MODULE) {
            $this->_coursemodule = get_coursemodule_from_id('newassignment', $this->_context->instanceid, 0, false, MUST_EXIST);
            return $this->_coursemodule;
        }
        return null;
    }

    /**
     * Get the current course
     * @return mixed stdClass|null The course
     */
    public function get_course() {
        global $DB;
        if ($this->_course) {
            return $this->_course;
        }

        if (!$this->_context) {
            return null;
        }
        $this->_course = $DB->get_record('course', array('id' => $this->get_course_context()->instanceid), '*', MUST_EXIST);
        return $this->_course;
    }

    /**
     * Download a zip file of all assignment submissions
     *
     * @return void
     */
    private function download_submissions($type = 'all') {
        global $CFG, $DB;

		// More efficient to load this here.
        require_once($CFG->libdir.'/filelib.php');

        // Increase the server timeout to handle the creation and sending of large zip files.
        core_php_time_limit::raise();
		
        switch ($type) {
            case 'all':
                $submissions = $DB->get_records('newassign_submissions', array('assignment' => $this->get_instance()->id));
                break;
            case 'actualversions':
                $submissions = $DB->get_records_sql('SELECT ss.id AS id, ss.userid AS userid, ss.version AS version, ss.assignment AS assignment, ss.timecreated AS timecreated, ss.timemodified AS timemodified FROM (SELECT userid, MAX(version) AS maxversion FROM {newassign_submissions} WHERE assignment = :assignmentid1 GROUP BY userid) x INNER JOIN {newassign_submissions} ss ON x.userid = ss.userid AND ss.version = x.maxversion  WHERE assignment = :assignmentid2', array('assignmentid1' => $this->get_instance()->id, 'assignmentid2' => $this->get_instance()->id));
                break;
            case 'notgraded':
                $submissions = $DB->get_records_sql('SELECT s.id AS id, g.id AS grade, s.userid AS userid, s.version AS version, s.assignment AS assignment, s.timecreated AS timecreated, s.timemodified AS timemodified FROM {newassign_submissions} s LEFT JOIN {newassign_grades} g ON s.id = g.submission WHERE s.assignment = :assignment AND g.grade IS NULL', array('assignment' => $this->get_instance()->id));
                break;
        }
		
        // load all submissions
        if (empty($submissions)) {
            print_error('errornosubmissions', 'newassignment');
            return;
        }

        // build a list of files to zip
        $filesforzipping = array();
        $fs = get_file_storage();

        $groupmode = groups_get_activity_groupmode($this->get_course_module());
        $groupid = 0;   // All users
        $groupname = '';
        if ($groupmode) {
            $groupid = groups_get_activity_group($this->get_course_module(), true);
            $groupname = groups_get_group_name($groupid) . '-';
        }

        // construct the zip file name
        $filename = str_replace(' ', '_', clean_filename($this->get_course()->shortname . '-' . $this->get_instance()->name . '-' . $groupname . $this->get_course_module()->id . ".zip")); //name of new zip file.

        $plugin = $this->get_submission_plugin();
        require_once($CFG->dirroot . '/local/elf/elflib.php');
        // get all the files for each submission
        foreach ($submissions as $submission) {
            $userid = $submission->userid; //get userid
            if ((groups_is_member($groupid, $userid) or !$groupmode or !$groupid)) {
                // get the plugins to add their own files to the zip

                $user = $DB->get_record("user", array("id" => $userid), 'id,username,firstname,lastname', MUST_EXIST);

                $prefix = $user->lastname . "_" . $user->firstname . "_" . $submission->version . "_";

                $pluginfiles = $plugin->get_files($submission);				
				foreach ($pluginfiles as $zipfilename => $file) {
					$prefixedfilename = elf_unaccent(clean_filename($prefix . '_' . $zipfilename));
					$filesforzipping[$prefixedfilename] = $file;
				}
            }
        } // end of foreach loop
        if ($zipfile = $this->pack_files($filesforzipping)) {
            switch ($type) {
                case 'all':
                    \mod_newassignment\event\submission_downloaded_all::create_from_assign($this)->trigger();
                    break;
                case 'actualversions':
                    \mod_newassignment\event\submission_downloaded_last_version::create_from_assign($this)->trigger();
                    break;
                case 'notgraded':
                    \mod_newassignment\event\submission_downloaded_not_graded::create_from_assign($this)->trigger();
                    break;
            }


            send_temp_file($zipfile, $filename); //send file and delete after sending.
        }
    }

    /**
     * Generate zip file from array of given files
     *
     * @param array $filesforzipping - array of files to pass into archive_to_pathname - this array is indexed by the final file name and each element in the array is an instance of a stored_file object
     * @return path of temp file - note this returned file does not have a .zip extension - it is a temp file.
     */
    private function pack_files($filesforzipping) {
        global $CFG;
        //create path for new zip file.
        $tempzip = tempnam($CFG->tempdir . '/', 'newassignment_');
        //zip files
        $zipper = new zip_packer();
        if ($zipper->archive_to_pathname($filesforzipping, $tempzip)) {
            return $tempzip;
        }
        return false;
    }

    /**
     * save assignment submission
     *
     * @param  moodleform $mform
     * @return bool
     */
    private function process_save_submission(&$mform) {
        global $USER, $CFG;

        // Need submit permission to submit an assignment
        require_capability('mod/newassignment:submit', $this->_context);
        require_sesskey();

        require_once($CFG->dirroot . '/mod/newassignment/submission_form.php');
        $data = new stdClass();
        $mform = new mod_newassignment_submission_form(null, array($this, $data));
        if ($mform->is_cancelled()) {
            return true;
        }
        if ($data = $mform->get_data()) {
            $submission = $this->get_user_submission($USER->id, true); //create the submission if needed & its id
            $grade = $this->get_user_grade($USER->id, false); // get the grade to check if it is locked
            if ($grade && $grade->locked) {
                print_error('submissionslocked', 'newassignment');
                return true;
            }

            $plugin = $this->get_submission_plugin();
            $plugin->save_submission($submission, $data);

            $this->update_submission($submission);

            // Logging
			$params = array(
				'context' => context_module::instance($this->get_course_module()->id),
				'courseid' => $this->get_course()->id,
				'objectid' => $submission->id,
			);

            $event = \mod_newassignment\event\submission_submited::create($params);
            $event->set_assign($this);
            $event->trigger();

            $this->notify_student_submission_receipt($submission);
            $this->notify_graders($submission);
            redirect(new moodle_url($CFG->wwwroot . '/mod/newassignment/view.php', array('id' => $this->get_course_module()->id)), get_string('submissionthanks', 'newassignment'), 0);
            return true;
        }
        return false;
    }

    /**
     * save grade
     *
     * @param  moodleform $mform
     * @return bool - was the grade saved
     */
    private function process_save_grade(&$mform, $subaction = 'none') {
        global $USER, $DB, $CFG;
        // Include grade form
        require_once($CFG->dirroot . '/mod/newassignment/gradeform.php');

        // Need submit permission to submit an assignment
        require_capability('mod/newassignment:grade', $this->get_context());
        require_sesskey();

        $rownum = required_param('rownum', PARAM_INT);
        $useridlist = optional_param('useridlist', '', PARAM_TEXT);
        if ($useridlist) {
            $useridlist = explode(',', $useridlist);
        } else {
            $useridlist = $this->get_grading_userid_list();
        }
        $last = false;
        $userid = $useridlist[$rownum];
        if ($rownum == count($useridlist) - 1) {
            $last = true;
        }

        $data = new stdClass();
        $mform = new mod_newassignment_grade_form(null, array($this, $data, array('rownum' => $rownum, 'useridlist' => $useridlist, 'last' => false)), 'post', '', array('class' => 'gradeform'));

        if ($formdata = $mform->get_data()) {
            $grade = $this->get_user_grade($userid, true);
            $oldgrade = $grade;
            $gradingdisabled = $this->grading_disabled($userid);
            $gradinginstance = $this->get_grading_instance($userid, $gradingdisabled);
            if (!$gradingdisabled) {
                if ($gradinginstance) {
                    $this->get_newassign_grading_controller()->update_grade($formdata->advancedgrading, $grade->id);
                    $grade->grade = $gradinginstance->submit_and_get_grade($formdata->advancedgrading, $grade->id);
                } else {
                    // handle the case when grade is set to No Grade
                    if (isset($formdata->grade)) {
                        $grade->grade = grade_floatval(unformat_float($formdata->grade));
                    }
                }
            }
            $grade->grader = $USER->id;

            $adminconfig = $this->get_admin_config();

            $feedback = $this->get_user_feedback($userid, true);
            $feedback->status = $formdata->feedback_status;
            require_once($CFG->dirroot . '/mod/newassignment/feedbacks/comment.php');
            $fcomment = new mod_newassignment_feedback_comment($this);
            $fcomment->save($feedback, $formdata);

            require_once($CFG->dirroot . '/mod/newassignment/feedbacks/file.php');
            $ffile = new mod_newassignment_feedback_file($this);
            $ffile->save($feedback, $formdata);

            $this->process_outcomes($userid, $formdata);

            $grade->mailed = 0;

            $this->update_grade($grade);

            $this->update_feedback($feedback, $grade);

            $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

            \mod_newassignment\event\submission_graded::create_from_grade($this, $grade)->trigger();
        } else
            return false;

        switch ($subaction) {
            case 'next':
                redirect(new moodle_url($CFG->wwwroot . '/mod/newassignment/view.php', array('id' => $this->get_course_module()->id, 'action' => 'grade', 'rownum' => $rownum + 1)), get_string('submissionthanks', 'newassignment'), 0);
                break;
            case 'previous':
                redirect(new moodle_url($CFG->wwwroot . '/mod/newassignment/view.php', array('id' => $this->get_course_module()->id, 'action' => 'grade', 'rownum' => $rownum - 1)), get_string('submissionthanks', 'newassignment'), 0);
                break;
            case 'none':
                redirect(new moodle_url($CFG->wwwroot . '/mod/newassignment/view.php', array('id' => $this->get_course_module()->id, 'action' => 'grading')), get_string('submissionthanks', 'newassignment'), 0);
                break;
        }
        return true;
    }

    /**
     * save grading options
     *
     * @return void
     */
    private function process_save_grading_options() {
        global $USER, $CFG;

        // Include grading options form
        require_once($CFG->dirroot . '/mod/newassignment/gradingoptionsform.php');

        // Need submit permission to submit an assignment
        require_capability('mod/newassignment:grade', $this->get_context());

        $mform = new mod_newassignment_grading_options_form(null, array('cm' => $this->get_course_module()->id,
                    'contextid' => $this->get_context()->id,
                    'userid' => $USER->id,
                    'showquickgrading' => false));
        if ($formdata = $mform->get_data()) {
            set_user_preference('newassignment_perpage', $formdata->perpage);
            set_user_preference('newassignment_filter', $formdata->filter);
        }
    }

    private function process_save_quick_grades() {
        global $USER, $DB, $CFG;

        /** gradelib.php */
        require_once($CFG->libdir . '/gradelib.php');

        // Need grade permission
        require_capability('mod/newassignment:grade', $this->get_context());

        // make sure advanced grading is disabled
        $gradingmanager = get_grading_manager($this->get_context(), 'mod_newassignment', 'submissions');
        $controller = $gradingmanager->get_active_controller();
        if (!empty($controller))
            return get_string('errorquickgradingvsadvancedgrading', 'newassignment');

        $users = array();
        // first check all the last modified values
        $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        $participants = $this->list_participants($currentgroup, true);

        // gets a list of possible users and look for values based upon that.
        foreach (array_keys($participants) as $userid) {
            $modified = optional_param('grademodified_' . $userid, -1, PARAM_INT);
            $record = new stdClass;
            $record->userid = $userid;
            if ($modified >= 0) {
                // gather the userid, updated grade and last modified value
                $record->grade = unformat_float(required_param('quickgrade_' . $record->userid, PARAM_TEXT));
                $record->lastmodified = $modified;
                $record->gradinginfo = grade_get_grades($this->get_course()->id, 'mod', 'newassignment', $this->get_instance()->id, array($userid));
            } else {
                $record->lastmodified = time();
            }
            $users[$userid] = $record;
        }
        if (empty($users)) {
            // Quick check to see whether we have any users to update and we don't
            return get_string('quickgradingchangessaved', 'newassignment'); // Technical lie
        }

        list($userids, $params) = $DB->get_in_or_equal(array_keys($users), SQL_PARAMS_NAMED);
        $params['assignment1'] = $params['assignment2'] = $params['assignment3'] = $this->get_instance()->id;
        // check them all for currency
        $sql = 'SELECT u.id as userid, g.grade as grade, g.timemodified as lastmodified
              FROM {user} u 
                    LEFT JOIN (SELECT ss.id, ss.userid, ss.version, ss.assignment, ss.timecreated, ss.timemodified FROM (SELECT userid, MAX(version) AS maxversion FROM mdl_newassign_submissions WHERE assignment = :assignment1 GROUP BY userid) x INNER JOIN mdl_newassign_submissions ss ON x.userid = ss.userid AND ss.version = x.maxversion  WHERE assignment = :assignment2) s ON s.userid = u.id
                    LEFT JOIN {newassign_grades} g ON s.id = g.submission AND g.assignment = :assignment3
             WHERE u.id ' . $userids;
        $currentgrades = $DB->get_recordset_sql($sql, $params);

        $modifiedgrades = array();
        $modifiedfeedbacks = array();
        foreach ($currentgrades as $current) {
            $modified = $users[(int) $current->userid];
            $grade = $this->get_user_grade($modified->userid, false);

            // check to see if the outcomes were modified
            if (isset($modified->grade) && $CFG->enableoutcomes) {
                foreach ($modified->gradinginfo->outcomes as $outcomeid => $outcome) {
                    $oldoutcome = $outcome->grades[$modified->userid]->grade;
                    $newoutcome = optional_param('outcome_' . $outcomeid . '_' . $modified->userid, -1, PARAM_FLOAT);
                    if ($oldoutcome != $newoutcome) {
                        // can't check modified time for outcomes because it is not reported
                        $modifiedgrades[$modified->userid] = $modified;
                        continue;
                    }
                }
            }

            // let plugins participate
            require_once($CFG->dirroot . '/mod/newassignment/feedbacks/comment.php');
            $plugin = new mod_newassignment_feedback_comment($this);
            $feedback = $this->get_user_feedback($modified->userid, false);

            if (optional_param('feedbackstatus_quick_' . $modified->userid, 'none', PARAM_TEXT) == 'none')
                continue;

            if ($feedback && $feedback->status != required_param('feedbackstatus_quick_' . $modified->userid, PARAM_TEXT)) {
                $modifiedfeedbacks[$modified->userid] = $modified;
            } elseif (!$feedback && required_param('feedbackstatus_quick_' . $modified->userid, PARAM_TEXT) != 'none') {
                $modifiedfeedbacks[$modified->userid] = $modified;
            } elseif ($feedback && $plugin->is_quickgrading_modified($modified->userid, $feedback)) {
                if ((int) $current->lastmodified > (int) $modified->lastmodified) {
                    return get_string('errorrecordmodified', 'newassignment');
                } else {
                    $modifiedfeedbacks[$modified->userid] = $modified;
                }
            }

            if (!isset($current->grade) && !isset($modified->grade))
                continue;

            if (($current->grade < 0 || $current->grade === NULL) &&
                    ($modified->grade < 0 || $modified->grade === NULL)) {
                // different ways to indicate no grade
                continue;
            }

            // Treat 0 and null as different values
            if ($current->grade !== null) {
                $current->grade = floatval($current->grade);
            }
            if ($current->grade !== $modified->grade) {
                // grade changed
                if ($this->grading_disabled($modified->userid))
                    continue;
                if ((int) $current->lastmodified > (int) $modified->lastmodified) {
                    // error - record has been modified since viewing the page
                    return get_string('errorrecordmodified', 'assign');
                } else {
                    $modifiedgrades[$modified->userid] = $modified;
                }
            }
        }
        $currentgrades->close();
        // ok - ready to process the updates
        foreach ($modifiedgrades as $userid => $modified) {
            $grade = $this->get_user_grade($userid, true);
            if (isset($modified->grade))
                $grade->grade = grade_floatval(unformat_float($modified->grade));
            $grade->grader = $USER->id;

            $this->update_grade($grade);

            $feedback = $this->get_user_feedback($modified->userid, true);
            $feedback->status = required_param('feedbackstatus_quick_' . $userid, PARAM_TEXT);
            $feedback->timemodified = time();

            $this->update_feedback($feedback);

            // save plugins data
            $plugin->save_quickgrading_changes($userid, $feedback);

            if (isset($modifiedfeedbacks[$userid]))
                unset($modifiedfeedbacks[$userid]);

            // save outcomes
            if (isset($modified->grade) && $CFG->enableoutcomes) {
                $data = array();
                foreach ($modified->gradinginfo->outcomes as $outcomeid => $outcome) {
                    $oldoutcome = $outcome->grades[$modified->userid]->grade;
                    $newoutcome = optional_param('outcome_' . $outcomeid . '_' . $modified->userid, -1, PARAM_INT);
                    if ($oldoutcome != $newoutcome) {
                        $data[$outcomeid] = $newoutcome;
                    }
                }
                if (count($data) > 0) {
                    grade_update_outcomes('mod/newassignment', $this->course->id, 'mod', 'assign', $this->get_instance()->id, $userid, $data);
                }
            }

            \mod_newassignment\event\submission_graded::create_from_grade($this, $grade)->trigger();
        }

        foreach ($modifiedfeedbacks as $userid => $modified) {
            $feedback = $this->get_user_feedback($modified->userid, true);
            $feedback->status = required_param('feedbackstatus_quick_' . $userid, PARAM_TEXT);
            $feedback->timemodified = time();
            $this->update_feedback($feedback);

            // save plugins data
            $plugin->save_quickgrading_changes($userid, $feedback);
        }

        return get_string('quickgradingchangessaved', 'assign');
    }

    /**
     * Ask the user to confirm they want to submit their work for grading
     * @return string
     */
    private function check_submit_for_grading() {
        global $USER;

        $o = '';
        $o .= $this->_output->header();
        $o .= $this->_output->render(new newassignment_submit_for_grading_page($this->get_course_module()->id));
        $o .= $this->_output->render_footer();
        return $o;
    }

    /**
     * Update a grade in the grade table for the assignment and in the gradebook
     *
     * @param stdClass $grade a grade record keyed on id
     * @return bool true for success
     */
    private function update_grade($grade) {
        global $DB;

        $grade->timemodified = time();

        if ($grade->grade && $grade->grade != -1) {
            if ($this->get_instance()->grade > 0) {
                if (!is_numeric($grade->grade)) {
                    return false;
                } else if ($grade->grade > $this->get_instance()->grade) {
                    return false;
                } else if ($grade->grade < 0) {
                    return false;
                }
            } else {
                // this is a scale
                if ($scale = $DB->get_record('scale', array('id' => -($this->get_instance()->grade)))) {
                    $scaleoptions = make_menu_from_list($scale->scale);
                    if (!array_key_exists((int) $grade->grade, $scaleoptions)) {
                        return false;
                    }
                }
            }
        }

        $result = $DB->update_record('newassign_grades', $grade);
        if ($result) {
            $this->gradebook_item_update(null, $grade);
        }
        return $result;
    }

    /**
     * save outcomes submitted from grading form
     *
     * @param int $userid
     * @param stdClass $formdata
     */
    private function process_outcomes($userid, $formdata) {
        global $CFG, $USER;

        if (empty($CFG->enableoutcomes)) {
            return;
        }
        if ($this->grading_disabled($userid)) {
            return;
        }

        require_once($CFG->libdir . '/gradelib.php');

        $data = array();
        $gradinginfo = grade_get_grades($this->get_course()->id, 'mod', 'newassignment', $this->get_instance()->id, $userid);

        if (!empty($gradinginfo->outcomes)) {
            foreach ($gradinginfo->outcomes as $index => $oldoutcome) {
                $name = 'outcome_' . $index;
                if (isset($formdata->{$name}[$userid]) and $oldoutcome->grades[$userid]->grade != $formdata->{$name}[$userid]) {
                    $data[$index] = $formdata->{$name}[$userid];
                }
            }
        }
        if (count($data) > 0) {
            grade_update_outcomes('mod/newassignment', $this->course->id, 'mod', 'newassignment', $this->get_instance()->id, $userid, $data);
        }
    }

    /**
     * Print the grading page for a single user submission
     *
     * @param moodleform $mform
     * @param int $offset
     * @return string
     */
    private function view_single_grade_page($mform, $offset = 0) {
        global $DB, $CFG;

        $o = '';

        // Include grade form
        require_once($CFG->dirroot . '/mod/newassignment/gradeform.php');

        // Need submit permission to submit an assignment
        require_capability('mod/newassignment:grade', $this->get_context());

        $o .= $this->_output->render(new newassignment_header($this->get_instance(),
                        $this->get_context(), false, $this->get_course_module()->id, get_string('grading', 'newassignment')));

        $rownum = required_param('rownum', PARAM_INT) + $offset;
        $useridlist = optional_param('useridlist', '', PARAM_TEXT);
        if ($useridlist) {
            $useridlist = explode(',', $useridlist);
        } else {
            $useridlist = $this->get_grading_userid_list();
        }
        $last = false;
        $userid = $useridlist[$rownum];
        if ($rownum == count($useridlist) - 1) {
            $last = true;
        }

        // the placement of this is important so can pass the list of userids above
        if ($offset) {
            $_POST = array();
        }
        if (!$userid) {
            throw new coding_exception('Row is out of bounds for the current grading table: ' . $rownum);
        }
        $user = $DB->get_record('user', array('id' => $userid));
        if ($user) {
            $o .= $this->_output->render(new newassignment_user_summary($user, $this->get_course()->id, has_capability('moodle/site:viewfullnames', $this->get_course_context())));
        }
        $submission = $this->get_last_user_submission($userid, false);

        $submisioncomments = false;
        if ($this->get_instance()->submissioncomments) {
            require_once($CFG->dirroot . '/mod/newassignment/submissions/comment.php');
            $submisioncomments = new mod_newassignment_submission_comment($this);
        }

        // get the current grade
        $grade = $this->get_user_grade($userid, false);
        if ($this->can_view_submission($userid)) {
            $gradelocked = $this->grading_disabled($userid);
            $o .= $this->_output->render(new newassignment_submission_status($this->get_instance()->allowsubmissionsfromdate,
                            $this->get_instance()->alwaysshowdescription,
                            $submission,
                            $this->get_submission_plugin(),
                            'none',
                            $submisioncomments,
                            $this->get_instance()->grademethod,
                            $gradelocked,
                            $this->is_graded($userid),
                            $this->get_instance()->duedate,
                            $this->_returnaction,
                            $this->_returnparams,
                            $this->get_course_module()->id,
                            newassignment_submission_status::GRADER_VIEW,
                            false
                    ));
        }
        if ($grade) {
            $data = new stdClass();
            if ($grade->grade !== NULL && $grade->grade >= 0) {
                $data->grade = format_float($grade->grade, 2);
            }
        } else {
            $data = new stdClass();
            $data->grade = '';
        }

        // now show the grading form
        if (!$mform) {
            $mform = new mod_newassignment_grade_form(null, array($this, $data, array('rownum' => $rownum, 'useridlist' => $useridlist, 'last' => $last)), 'post', '', array('class' => 'gradeform'));
        }
        $o .= $this->_output->render(new newassignment_form('gradingform', $mform));

        \mod_newassignment\event\grading_form_viewed::create_from_user($this, $user)->trigger();

        $o .= $this->_output->render_footer();
        return $o;
    }

    /**
     * View edit submissions page.
     *
     * @param moodleform $mform
     * @return void
     */
    private function view_edit_submission_page($mform) {
        global $CFG, $PAGE, $USER, $DB;

        $o = '';
        // Include submission form
        require_once($CFG->dirroot . '/mod/newassignment/submission_form.php');
        // Need submit permission to submit an assignment
        require_capability('mod/newassignment:submit', $this->_context);

        if (!$this->submissions_open()) {
            return $this->view_student_error_message();
        }
        $o .= $this->_output->render(new newassignment_header($this->get_instance(),
                        $this->get_context(),
                        $this->show_intro(),
                        $this->get_course_module()->id,
                        get_string('editsubmission', 'newassignment')));
        $o .= $this->plagiarism_print_disclosure();
        $data = new stdClass();

        if (!$mform) {
            $mform = new mod_newassignment_submission_form(null, array($this, $data));
        }
        $PAGE->requires->js_init_call('M.mod_newassignment.init_submission', array(get_string('confirmsubmission', 'newassignment'),get_string('confirmsubmissionok', 'newassignment'),get_string('confirmsubmissioncancel', 'newassignment')));
        $o .= $this->_output->render(new newassignment_form('editsubmissionform', $mform));

        $o .= $this->_output->render_footer();
        $user = $DB->get_record('user', array('id'=>$USER->id), '*', MUST_EXIST);
        \mod_newassignment\event\submission_form_viewed::create_from_user($this, $user)->trigger();
        return $o;
    }

    /**
     * View entire grading page.
     *
     * @return string
     */
    private function view_grading_page() {
        global $CFG;

        $o = '';
        // Need submit permission to submit an assignment
        require_capability('mod/newassignment:grade', $this->get_context());
        require_once($CFG->dirroot . '/mod/newassignment/gradeform.php');

        // only load this if it is

        $o .= $this->view_grading_table();

        $o .= $this->_output->render_footer();
        \mod_newassignment\event\grading_table_viewed::create_from_assign($this)->trigger();
        return $o;
    }

    /**
     * View the grading table of all submissions for this assignment
     *
     * @return string
     */
    private function view_grading_table() {
        global $USER, $CFG;
        // Include grading options form
        require_once($CFG->dirroot . '/mod/newassignment/gradingoptionsform.php');
        require_once($CFG->dirroot . '/mod/newassignment/quickgradingform.php');
        require_once($CFG->dirroot . '/mod/newassignment/gradingtable.php');
        $o = '';

        $links = array();
        if (has_capability('gradereport/grader:view', $this->get_course_context()) &&
                has_capability('moodle/grade:viewall', $this->get_course_context())) {
            $gradebookurl = '/grade/report/grader/index.php?id=' . $this->get_course()->id;
            $links[$gradebookurl] = get_string('viewgradebook', 'newassignment');
        }

        $downloadurl = '/mod/newassignment/view.php?id=' . $this->get_course_module()->id . '&action=downloadnotgraded';
        $links[$downloadurl] = get_string('downloadnotgraded', 'newassignment');
        $downloadurl = '/mod/newassignment/view.php?id=' . $this->get_course_module()->id . '&action=downloadactualversions';
        $links[$downloadurl] = get_string('downloadactualversions', 'newassignment');
        $downloadurl = '/mod/newassignment/view.php?id=' . $this->get_course_module()->id . '&action=downloadall';
        $links[$downloadurl] = get_string('downloadall', 'newassignment');

        $gradingactions = new url_select($links);

        $gradingmanager = get_grading_manager($this->get_context(), 'mod_newassignment', 'submissions');

        $perpage = get_user_preferences('newassignment_perpage', 10);
        $filter = get_user_preferences('newassignment_filter', '');
        $controller = $gradingmanager->get_active_controller();
        $showquickgrading = empty($controller);
        if (optional_param('action', '', PARAM_ALPHA) == 'saveoptions') {
            $quickgrading = optional_param('quickgrading', false, PARAM_BOOL);
            set_user_preference('newassignment_quickgrading', $quickgrading);
        }
        $quickgrading = get_user_preferences('newassignment_quickgrading', false);

        // print options  for changing the filter and changing the number of results per page
        $gradingoptionsform = new mod_newassignment_grading_options_form(null,
                        array('cm' => $this->get_course_module()->id,
                            'contextid' => $this->get_context()->id,
                            'userid' => $USER->id,
                            'showquickgrading' => $showquickgrading,
                            'quickgrading' => $quickgrading),
                        'post', '',
                        array('class' => 'newassign_gradingoptionsform'));

        $gradingoptionsdata = new stdClass();
        $gradingoptionsdata->perpage = $perpage;
        $gradingoptionsdata->filter = $filter;
        $gradingoptionsform->set_data($gradingoptionsdata);

        // plagiarism update status apearring in the grading book
        if (!empty($CFG->enableplagiarism)) {
            /** Include plagiarismlib.php */
            require_once($CFG->libdir . '/plagiarismlib.php');
            plagiarism_update_status($this->get_course(), $this->get_course_module());
        }

        $actionformtext = $this->_output->render($gradingactions);
        $o .= $this->_output->render(new newassignment_header($this->get_instance(),
                        $this->get_context(), false, $this->get_course_module()->id, get_string('grading', 'newassignment'), $actionformtext));
        $o .= groups_print_activity_menu($this->get_course_module(), $CFG->wwwroot . '/mod/newassignment/view.php?id=' . $this->get_course_module()->id . '&action=grading', true);


        // load and print the table of submissions
        if ($showquickgrading && $quickgrading) {
            $table = $this->_output->render(new newassignment_grading_table($this, $perpage, $filter, 0, true));
            $quickgradingform = new mod_newassignment_quick_grading_form(null,
                            array('cm' => $this->get_course_module()->id,
                                'gradingtable' => $table));
            $o .= $this->_output->render(new newassignment_form('quickgradingform', $quickgradingform));
        } else {
            $o .= $this->_output->render(new newassignment_grading_table($this, $perpage, $filter, 0, false));
        }

        $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        $users = array_keys($this->list_participants($currentgroup, true));
        $o .= $this->_output->render(new newassignment_form('newassign_gradingoptionsform', $gradingoptionsform, ''));
        return $o;
    }

    /**
     * display the submission that is used by a plugin
     * Uses url parameters 'sid', 'gid' and 'plugin'
     * @param string $pluginsubtype
     * @return string
     */
    private function view_plugin_content($pluginsubtype) {
        global $USER, $DB, $CFG;

        $o = '';

        $itemid = optional_param('itemid', 0, PARAM_INT);
        $item = null;
        if ($pluginsubtype == 'submission') {
            $plugin = $this->get_submission_plugin();
            if ($itemid <= 0) {
                throw new coding_exception('Submission id should not be 0');
            }
            $item = $DB->get_record('newassign_submissions', array('id' => $itemid));

            // permissions
            if ($item->userid != $USER->id && $this->get_instance()->publishsubmissions == NEWASSIGN_PUBLISH_SUBMISSIONS_NO) {
                require_capability('mod/newassignment:grade', $this->get_context());
            }
            $o .= $this->_output->render(new newassignment_header($this->get_instance(),
                            $this->get_context(),
                            $this->show_intro(),
                            $this->get_course_module()->id,
                            $plugin->get_name()));
            $o .= $this->_output->render(new newassignment_submission_plugin_submission($plugin,
                            $item,
                            $this->get_course_module()->id,
                            $this->_returnaction,
                            $this->_returnparams));

            \mod_newassignment\event\submission_viewed::create_from_submission($this, $item)->trigger();
        } else {
            require_once($CFG->dirroot . '/mod/newassignment/feedbacks/comment.php');
            $plugin = new mod_newassignment_feedback_comment($this);
            if ($itemid <= 0) {
                throw new coding_exception('Feedback id should not be 0');
            }
            $item = $DB->get_record('newassign_feedbacks', array('id' => $itemid));
            // permissions
            if ($item->userid != $USER->id && $this->get_instance()->publishfeedbacks == NEWASSIGN_PUBLISH_FEEDBACKS_NO) {
                require_capability('mod/newassignment:grade', $this->get_context());
            }
            $o .= $this->_output->render(new newassignment_header($this->get_instance(),
                            $this->get_context(),
                            $this->show_intro(),
                            $this->get_course_module()->id,
                            $plugin->get_name()));
            $o .= $this->_output->render(new newassignment_feedback_plugin_feedback($plugin,
                            $item,
                            $this->get_course_module()->id,
                            $this->_returnaction,
                            $this->_returnparams));
            \mod_newassignment\event\feedback_viewed::create_from_feedback($this, $item);
        }


        $o .= $this->view_return_links();

        $o .= $this->_output->render_footer();
        return $o;
    }

    /**
     * View a link to go back to the previous page. Uses url parameters returnaction and returnparams.
     *
     * @return string
     */
    private function view_return_links() {

        $returnaction = optional_param('returnaction', '', PARAM_ALPHA);
        $returnparams = optional_param('returnparams', '', PARAM_TEXT);

        $params = array();
        parse_str($returnparams, $params);
        $params = array_merge(array('id' => $this->get_course_module()->id, 'action' => $returnaction), $params);

        return $this->_output->single_button(new moodle_url('/mod/newassignment/view.php', $params), get_string('back'), 'get');
    }

    private function view_publish_submissions_page() {
        global $CFG;

        require_once($CFG->dirroot . '/mod/newassignment/publishtable.php');

        $o = '';

        $o .= $this->_output->render(new newassignment_header($this->get_instance(),
                        $this->get_context(),
                        false,
                        $this->get_course_module()->id, get_string('publishsubpage', 'newassignment'), '', false));


        $o .= $this->_output->render(new newassignment_publish_table($this));


        $o .= $this->_output->render_footer();
        \mod_newassignment\event\submission_status_viewed::create_from_assign($this)->trigger();
        return $o;
    }

    /**
     * Display a grading error
     *
     * @param string $message - The description of the result
     * @return string
     */
    private function view_quickgrading_result($message) {
        $o = '';
        $o .= $this->_output->render(new newassignment_header($this->get_instance(),
                        $this->get_context(),
                        $this->show_intro(),
                        $this->get_course_module()->id,
                        get_string('quickgradingresult', 'newassignment')));
        $o .= $this->_output->render(new newassignment_quickgrading_result($message, $this->get_course_module()->id));
        $o .= $this->_output->render_footer();
        return $o;
    }

    /**
     * View submissions page (contains details of current submission).
     *
     * @return string
     */
    private function view_submission_page() {
        global $CFG, $USER;

        $o = '';
        $o .= $this->_output->render(new newassignment_header($this->get_instance(),
                        $this->get_context(),
                        $this->show_intro(),
                        $this->get_course_module()->id, '', '', $this->can_view_others_submissions()));

        if ($this->can_grade()) {
            $o .= $this->_output->render(new newassignment_grading_summary($this->count_participants(0),
                            $this->count_submissions(),
                            $this->get_instance()->duedate,
                            $this->get_course_module()->id
                    ));
        }

        if ($this->can_view_submission($USER->id)) {
            $o .= $this->view_student_summary($USER, true);
        }


        $o .= $this->_output->render_footer();
        \mod_newassignment\event\submission_status_viewed::create_from_assign($this)->trigger();
        return $o;
    }

    /**
     * Print 2 tables of information with no action links -
     * the submission summary and the grading summary
     *
     * @param stdClass $user the user to print the report for
     * @param bool $showlinks - Return plain text or links to the profile
     * @return string - the html summary
     */
    public function view_student_summary($user, $showlinks) {
        global $CFG, $DB, $PAGE;

        $grade = $this->get_user_grade($user->id, false);
        $submission = $this->get_last_user_submission($user->id);
        $o = '';

        if ($this->can_view_submission($user->id)) {
            $showedit = has_capability('mod/newassignment:submit', $this->_context) && $this->submissions_open() && $showlinks;
            $gradelocked = $this->grading_disabled($user->id);

            $submisioncomments = false;
            if ($this->get_instance()->submissioncomments) {
                require_once($CFG->dirroot . '/mod/newassignment/submissions/comment.php');
                $submisioncomments = new mod_newassignment_submission_comment($this);
            }
            $isGraded = $this->is_graded($user->id);
            $feedback = $this->get_user_feedback($user->id, false);
            $o .= $this->_output->render(new newassignment_submission_status($this->get_instance()->allowsubmissionsfromdate,
                            $this->get_instance()->alwaysshowdescription,
                            $submission,
                            $this->get_submission_plugin(),
                            $this->get_available_submission_action($user->id, $submission),
                            $submisioncomments,
                            $this->get_instance()->grademethod,
                            $gradelocked,
                            $isGraded || ($feedback != false),
                            $this->get_instance()->duedate,
                            $this->_returnaction,
                            $this->_returnparams,
                            $this->get_course_module()->id,
                            newassignment_submission_status::STUDENT_VIEW,
                            $showedit
                    ));
            require_once($CFG->libdir . '/gradelib.php');
            require_once($CFG->dirroot . '/grade/grading/lib.php');

            $gradinginfo = grade_get_grades($this->get_course()->id, 'mod', 'newassignment', $this->get_instance()->id, $user->id);

            $gradingitem = $gradinginfo->items[0];
            $gradebookgrade = $gradingitem->grades[$user->id];

            if ($isGraded || ($feedback != false)) {

                $gradefordisplay = '';
                $actualgrade = '-';
                if (!$gradebookgrade->hidden && $this->get_instance()->grade != 0) {
                    $gradingmanager = get_grading_manager($this->get_context(), 'mod_newassignment', 'submissions');
                    $gradefordisplay = $this->display_grade($gradebookgrade->grade, false);
                    if ($controller = $gradingmanager->get_active_controller()) {
                        if ($grade)
                            $actualgrade = $this->display_advanced_grade($grade);
                    } else {
                        if ($grade)
                            $actualgrade = $this->display_grade($grade->grade, false);
                    }
                }
                $gradeddate = $feedback->timemodified;
                $grader = $DB->get_record('user', array('id' => $gradebookgrade->usermodified));

                $feedbackstatus = new newassignment_feedback_status($gradefordisplay, $actualgrade,
                                $gradeddate,
                                $grader,
                                $grade,
                                $gradebookgrade->hidden || $this->get_instance()->grade == 0,
                                $this->get_course_module()->id,
                                $this->_returnaction,
                                $this->_returnparams,
                                $this,
                                $feedback);

                $o .= $this->_output->render($feedbackstatus);
            }
        }
        return $o;
    }

    protected function get_newassign_grading_controller() {
        global $CFG;
        if (!isset($this->_grading_controller)) {
            $gradingmanager = get_grading_manager($this->get_context(), 'mod_newassignment', 'submissions');
            require_once($CFG->dirroot . '/mod/newassignment/grade/' . $gradingmanager->get_active_method() . '.php');
            $className = 'newassign_grade_' . $gradingmanager->get_active_method();
            $this->_grading_controller = new $className($gradingmanager->get_active_controller(), $this);
        }
        return $this->_grading_controller;
    }

    public function display_advanced_grade($grade) {
        global $CFG, $DB;

        $html = $this->get_newassign_grading_controller()->display_grade($grade->id);

        return $html . format_float(($grade->grade), 2) . '&nbsp;/&nbsp;' . format_float($this->get_instance()->grade, 2);
    }

    /**
     * message for students when assignment submissions have been closed
     *
     * @return string
     */
    private function view_student_error_message() {
        global $CFG;

        $o = '';
        // Need submit permission to submit an assignment
        require_capability('mod/newassignment:submit', $this->_context);

        $o .= $this->_output->render(new newassignment_header($this->get_instance(),
                        $this->get_context(),
                        $this->show_intro(),
                        $this->get_course_module()->id,
                        get_string('editsubmission', 'newassignment')));

        $o .= $this->_output->notification(get_string('submissionsclosed', 'newassignment'));

        $o .= $this->_output->render_footer();

        return $o;
    }

    public function render_area_files($component, $area, $itemid) {
        global $USER;

        $fs = get_file_storage();
        $browser = get_file_browser();
        $files = $fs->get_area_files($this->get_context()->id, $component, $area, $itemid, "timemodified", false);
        return $this->_output->newassignment_files($this->get_context(), $itemid, $area, $component);
    }

    /**
     * add elements to grade form
     *
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @param array $params
     * @return void
     */
    public function add_grade_form_elements(MoodleQuickForm $mform, stdClass $data, $params) {
        global $USER, $CFG;
        $settings = $this->get_instance();

        $rownum = $params['rownum'];
        $last = $params['last'];
        $useridlist = $params['useridlist'];
        $userid = $useridlist[$rownum];
        $grade = $this->get_user_grade($userid, false);

        $gradinginfo = grade_get_grades($this->get_course()->id, 'mod', 'newassignment', $this->get_instance()->id, $userid);

        // add advanced grading
        $gradingdisabled = $this->grading_disabled($userid);
        $gradinginstance = $this->get_grading_instance($userid, $gradingdisabled);
        if ($gradinginstance) {
            $gradingelement = $mform->addElement('grading', 'advancedgrading', get_string('grade') . ':', array('gradinginstance' => $gradinginstance));
            if ($gradingdisabled) {
                $gradingelement->freeze();
            } else {
                $mform->addElement('hidden', 'advancedgradinginstanceid', $gradinginstance->get_id());
                $mform->setType('advancedgradinginstanceid', PARAM_INT);
            }
        } else {
            // use simple direct grading
            if ($this->get_instance()->grade > 0) {
                $gradingelement = $mform->addElement('text', 'grade', get_string('gradeoutof', 'assign', $this->get_instance()->grade));
                $mform->addHelpButton('grade', 'gradeoutofhelp', 'newassignment');
                $mform->setType('grade', PARAM_TEXT);
                if ($gradingdisabled) {
                    $gradingelement->freeze();
                }
            } else {
                $grademenu = make_grades_menu($this->get_instance()->grade);
                if (count($grademenu) > 0) {
                    $gradingelement = $mform->addElement('select', 'grade', get_string('grade') . ':', $grademenu);
                    $mform->setType('grade', PARAM_INT);
                    if ($gradingdisabled) {
                        $gradingelement->freeze();
                    }
                }
            }
        }

        $feedback = $this->get_user_feedback($userid, false);
        $mform->addElement('select', 'feedback_status', get_string('feedbackstatus', 'newassignment'), array(NEWASSIGN_FEEDBACK_STATUS_ACCEPTED => get_string('feedbackstatus_accepted', 'newassignment'), NEWASSIGN_FEEDBACK_STATUS_DECLINED => get_string('feedbackstatus_declined', 'newassignment')));
        if ($feedback)
            $mform->setDefault('feedback_status', $feedback->status);
        else
            $mform->setDefault('feedback_status', NEWASSIGN_FEEDBACK_STATUS_ACCEPTED);

        if (!empty($CFG->enableoutcomes)) {
            foreach ($gradinginfo->outcomes as $index => $outcome) {
                $options = make_grades_menu(-$outcome->scaleid);
                if ($outcome->grades[$userid]->locked) {
                    $options[0] = get_string('nooutcome', 'grades');
                    $mform->addElement('static', 'outcome_' . $index . '[' . $userid . ']', $outcome->name . ':', $options[$outcome->grades[$userid]->grade]);
                } else {
                    $options[''] = get_string('nooutcome', 'grades');
                    $attributes = array('id' => 'menuoutcome_' . $index);
                    $mform->addElement('select', 'outcome_' . $index . '[' . $userid . ']', $outcome->name . ':', $options, $attributes);
                    $mform->setType('outcome_' . $index . '[' . $userid . ']', PARAM_INT);
                    $mform->setDefault('outcome_' . $index . '[' . $userid . ']', $outcome->grades[$userid]->grade);
                }
            }
        }

        if (has_all_capabilities(array('gradereport/grader:view', 'moodle/grade:viewall'), $this->get_course_context())) {
            $grade = $this->_output->action_link(new moodle_url('/grade/report/grader/index.php',
                            array('id' => $this->get_course()->id)), $gradinginfo->items[0]->grades[$userid]->str_grade);
        } else {
            $grade = $gradinginfo->items[0]->grades[$userid]->str_grade;
        }
        $mform->addElement('static', 'finalgrade', get_string('currentgrade', 'newassignment') . ':', $grade);


        $mform->addElement('static', 'progress', '', get_string('gradingstudentprogress', 'newassignment', array('index' => $rownum + 1, 'count' => count($useridlist))));

        // plugins
        $this->add_feedback_grade_elements($feedback, $mform, $data);

        // hidden params
        $mform->addElement('hidden', 'id', $this->get_course_module()->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'rownum', $rownum);
        $mform->setType('rownum', PARAM_INT);
        $mform->addElement('hidden', 'useridlist', implode(',', $useridlist));
        $mform->setType('useridlist', PARAM_TEXT);
        $mform->addElement('hidden', 'ajax', optional_param('ajax', 0, PARAM_INT));
        $mform->setType('ajax', PARAM_INT);

        $mform->addElement('hidden', 'action', 'submitgrade');
        $mform->setType('action', PARAM_ALPHA);


        $buttonarray = array();
        if ($rownum > 0) {
            $buttonarray[] = $mform->createElement('submit', 'saveandshowprevious', get_string('saveprevious', 'newassignment'));
        }
        $buttonarray[] = $mform->createElement('submit', 'savegrade', get_string('savechanges', 'newassignment'));
        if (!$last) {
            $buttonarray[] = $mform->createElement('submit', 'saveandshownext', get_string('savenext', 'newassignment'));
        }
        $buttonarray[] = $mform->createElement('cancel', 'cancelbutton', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Get an instance of a grading form if advanced grading is enabled
     * This is specific to the assignment, marker and student
     *
     * @param int $userid - The student userid
     * @param bool $gradingdisabled
     * @return mixed gradingform_instance|null $gradinginstance
     */
    public function get_grading_instance($userid, $gradingdisabled) {
        global $CFG, $USER;

        $grade = $this->get_user_grade($userid, false);
        $grademenu = make_grades_menu($this->get_instance()->grade);

        $advancedgradingwarning = false;
        $gradingmanager = get_grading_manager($this->get_context(), 'mod_newassignment', 'submissions');
        $gradinginstance = null;
        if ($gradingmethod = $gradingmanager->get_active_method()) {
            $controller = $gradingmanager->get_controller($gradingmethod);
            if ($controller->is_form_available()) {
                $itemid = null;
                if ($grade) {
                    $itemid = $grade->id;
                }
                if ($gradingdisabled && $itemid) {
                    $gradinginstance = ($controller->get_current_instance($USER->id, $itemid));
                } else if (!$gradingdisabled) {
                    $instanceid = optional_param('advancedgradinginstanceid', 0, PARAM_INT);
                    $gradinginstance = ($controller->get_or_create_instance($instanceid, $USER->id, $itemid));
                }
            } else {
                $advancedgradingwarning = $controller->form_unavailable_notification();
            }
        }
        if ($gradinginstance) {
            $gradinginstance->get_controller()->set_grade_range($grademenu);
        }
        return $gradinginstance;
    }

    /**
     * Utility function to get the userid for every row in the grading table
     * so the order can be frozen while we iterate it
     *
     * @return array An array of userids
     */
    private function get_grading_userid_list() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/newassignment/gradingtable.php');
        $filter = get_user_preferences('newassignment_filter', '');
        $table = new newassignment_grading_table($this, 0, $filter, 0, false);

        $useridlist = $table->get_column_data('userid');

        return $useridlist;
    }

    /**
     * add elements in grading plugin form
     *
     * @param mixed $grade stdClass|null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return void
     */
    private function add_feedback_grade_elements($feedback, MoodleQuickForm $mform, stdClass $data) {
        global $CFG;

        require_once($CFG->dirroot . '/mod/newassignment/feedbacks/comment.php');
        $fcomment = new mod_newassignment_feedback_comment($this);
        $mform->addElement('header', 'feedbackcomment', get_string('feedbackcomment', 'newassignment'));
        $fcomment->get_form_elements($feedback, $mform, $data);

        require_once($CFG->dirroot . '/mod/newassignment/feedbacks/file.php');
        $ffile = new mod_newassignment_feedback_file($this);
        $mform->addElement('header', 'feedbackfile', get_string('feedbackfile', 'newassignment'));
        $ffile->get_form_elements($feedback, $mform, $data);
    }

    /**
     * add elements to submission form
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return void
     */
    public function add_submission_form_elements(MoodleQuickForm $mform, stdClass $data) {
        global $CFG, $USER;


        $submission = $this->get_user_submission($USER->id, false);

        $plugin = $this->get_submission_plugin();
        $plugin->submission_form($submission, $mform, $data);

        // hidden params
        $mform->addElement('hidden', 'id', $this->get_course_module()->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action', 'savesubmission');
        $mform->setType('action', PARAM_TEXT);
        // buttons
    }

    /**
     * Capture the output of the plagiarism plugins disclosures and return it as a string
     *
     * @return void
     */
    private function plagiarism_print_disclosure() {
        global $CFG;
        $o = '';

        if (!empty($CFG->enableplagiarism)) {
            /** Include plagiarismlib.php */
            require_once($CFG->libdir . '/plagiarismlib.php');
            ob_start();

            plagiarism_print_disclosure($this->get_course_module()->id);
            $o = ob_get_contents();
            ob_end_clean();
        }

        return $o;
    }

    /**
     * Return a grade in user-friendly form, whether it's a scale or not
     *
     * @param mixed $grade int|null
     * @param boolean $editing Are we allowing changes to this grade?
     * @param int $userid The user id the grade belongs to
     * @param int $modified Timestamp from when the grade was last modified
     * @return string User-friendly representation of grade
     */
    public function display_grade($grade, $editing, $userid = 0, $modified = 0) {
        global $DB;

        static $scalegrades = array();

        if ($this->get_instance()->grade >= 0) {
            // Normal number
            if ($editing && $this->get_instance()->grade > 0) {
                if ($grade < 0) {
                    $displaygrade = '';
                } else {
                    $displaygrade = format_float($grade);
                }
                $o = '<input type="text" name="quickgrade_' . $userid . '" value="' . $displaygrade . '" size="6" maxlength="10" class="quickgrade"/>';
                $o .= '&nbsp;/&nbsp;' . format_float($this->get_instance()->grade, 2);
                $o .= '<input type="hidden" name="grademodified_' . $userid . '" value="' . $modified . '"/>';
                return $o;
            } else {
                if ($grade == -1 || $grade === null) {
                    return '-';
                } else {
                    return format_float(($grade), 2) . '&nbsp;/&nbsp;' . format_float($this->get_instance()->grade, 2);
                }
            }
        } else {
            // Scale
            if (empty($this->cache['scale'])) {
                if ($scale = $DB->get_record('scale', array('id' => -($this->get_instance()->grade)))) {
                    $this->cache['scale'] = make_menu_from_list($scale->scale);
                } else {
                    return '-';
                }
            }
            if ($editing) {
                $o = '<select name="quickgrade_' . $userid . '" class="quickgrade">';
                $o .= '<option value="-1">' . get_string('nograde') . '</option>';
                foreach ($this->cache['scale'] as $optionid => $option) {
                    $selected = '';
                    if ($grade == $optionid) {
                        $selected = 'selected="selected"';
                    }
                    $o .= '<option value="' . $optionid . '" ' . $selected . '>' . $option . '</option>';
                }
                $o .= '</select>';
                $o .= '<input type="hidden" name="grademodified_' . $userid . '" value="' . $modified . '"/>';
                return $o;
            } else {
                $scaleid = (int) $grade;
                if (isset($this->cache['scale'][$scaleid])) {
                    return $this->cache['scale'][$scaleid];
                }
                return '-';
            }
        }
    }

    /**
     * Is this assignment open for submissions?
     *
     * Check the due date,
     * prevent late submissions,
     * has this person already submitted,
     * is the assignment locked?
     *
     * @return bool
     */
    private function submissions_open() {
        global $USER;

        $time = time();
        $dateopen = true;
        if ($this->get_instance()->preventlatesubmissions && $this->get_instance()->duedate) {
            $dateopen = ($this->get_instance()->allowsubmissionsfromdate <= $time && $time <= $this->get_instance()->duedate);
        } else {
            $dateopen = ($this->get_instance()->allowsubmissionsfromdate <= $time);
        }

        if (!$dateopen) {
            return false;
        }

        // now check if this user has already submitted etc.
        if (!is_enrolled($this->get_course_context(), $USER)) {
            return false;
        }
        if ($submission = $this->get_user_submission($USER->id, false)) {
            return false;
        }

        if ($this->grading_disabled($USER->id)) {
            return false;
        }

        return true;
    }

    /**
     * Determine if this users grade is locked or overridden
     *
     * @param int $userid - The student userid
     * @return bool $gradingdisabled
     */
    public function grading_disabled($userid) {
        global $CFG;
        /** gradelib.php */
        require_once($CFG->libdir . '/gradelib.php');

        $gradinginfo = grade_get_grades($this->get_course()->id, 'mod', 'newassignment', $this->get_instance()->id, array($userid));
        if (!$gradinginfo) {
            return false;
        }

        if (!isset($gradinginfo->items[0]->grades[$userid])) {
            return false;
        }
        $gradingdisabled = $gradinginfo->items[0]->grades[$userid]->locked || $gradinginfo->items[0]->grades[$userid]->overridden;
        return $gradingdisabled;
    }

    /**
     * Get the context of the current course
     * @return mixed context|null The course context
     */
    public function get_course_context() {
        if (!$this->_context && !$this->_course) {
            throw new coding_exception('Improper use of the assignment class. Cannot load the course context.');
        }
        if ($this->_context) {
            return $this->_context->get_course_context();
        } else {
            return context_course::instance($this->_course->id);
        }
    }

    /**
     * Set the action and parameters that can be used to return to the current page
     *
     * @param string $action The action for the current page
     * @param array $params An array of name value pairs which form the parameters to return to the current page
     * @return void
     */
    public function register_return_link($action, $params) {
        $this->_returnaction = $action;
        $this->_returnparams = $params;
    }

    /**
     * Does this user have grade permission for this assignment
     *
     * @return bool
     */
    private function can_grade() {
        // Permissions check
        if (!has_capability('mod/newassignment:grade', $this->_context)) {
            return false;
        }

        return true;
    }

    public function get_available_submission_action($userid, $submission = null) {
        global $DB;
        if ($submission == null)
            $submission = $this->get_last_user_submission($userid);

        if ($submission == false)
            return 'addfirst';
        $feedback = $DB->get_record('newassign_feedbacks', array('submission' => $submission->id));
        if (!$feedback || $feedback->status == NEWASSIGN_FEEDBACK_STATUS_ACCEPTED)
            return 'none';
        if ($feedback->status == NEWASSIGN_FEEDBACK_STATUS_DECLINED)
            return 'addnext';

        return 'none';
    }

    /**
     * Load a count of users enrolled in the current course with the specified permission and group (optional)
     *
     * @param string $status The submission status - should match one of the constants
     * @return int number of matching submissions
     */
    public function count_submissions() {
        global $DB;
        return $DB->count_records_sql("SELECT COUNT(DISTINCT(userid))
                                     FROM {newassign_submissions}
                                    WHERE assignment = ?", array($this->get_course_module()->instance));
    }

    /**
     * This will retrieve a grade object from the db, optionally creating it if required
     *
     * @param int $userid The user we are grading
     * @param bool $create If true the grade will be created if it does not exist
     * @return stdClass The grade record
     */
    private function get_user_grade($userid, $create) {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $submission = $this->get_last_user_submission($userid);

        if (!$submission && !$create)
            return false;

        if ($submission != false) {
            $grade = $DB->get_record('newassign_grades', array('assignment' => $this->get_instance()->id, 'userid' => $userid, 'submission' => $submission->id));
            if ($grade)
                return $grade;
        }

        if ($create) {
            if (!$submission)
                $submission = $this->get_user_submission($userid, true);
            $grade = new stdClass();
            $grade->assignment = $this->get_instance()->id;
            $grade->submission = $submission->id;
            $grade->userid = $userid;
            $grade->timecreated = time();
            $grade->timemodified = $grade->timecreated;
            $grade->locked = 0;
            $grade->grade = -1;
            $grade->grader = $USER->id;
            $gid = $DB->insert_record('newassign_grades', $grade);
            $grade->id = $gid;
            return $grade;
        }
        return false;
    }

    public function get_final_user_grade($userid) {
        global $DB;
        $submission = $this->get_last_user_submission($userid);
        if (!$submission)
            return false;
        $grade = $DB->get_record('newassign_grades', array('assignment' => $this->get_instance()->id, 'userid' => $userid, 'submission' => $submission->id));
        if ($grade)
            return $grade;
        if ($submission->version == 1)
            return false;
        return $DB->get_record_sql('SELECT g.id as id, g.grade as grade,  g.assignment as assignment, g.submission as submission, 
    				g.userid as userid, g.timecreated as timecreated, g.timemodified as timemodified, g.grader as grader
    				FROM (SELECT * FROM {newassign_grades} WHERE userid=:userid1 AND assignment=:assignment1) as g 
    					INNER JOIN (SELECT id FROM {newassign_submissions} WHERE assignment=:assignment2 AND userid=:userid2 AND version=:version) as s
    				ON g.submission = s.id', array('userid1' => $userid, 'userid2' => $userid,
                    'assignment1' => $this->get_instance()->id,
                    'assignment2' => $this->get_instance()->id,
                    'version' => $submission->version - 1));
    }

    private function get_last_user_submission($userid) {
        global $DB;
        $submissions = $DB->get_records('newassign_submissions', array('assignment' => $this->get_instance()->id, 'userid' => $userid), 'version DESC');
        if (count($submissions))
            return reset($submissions);
        return false;
    }

    public function get_all_user_submissions_with_feedbacks($userid) {
        global $DB;
		$assignmentId = $this->get_instance()->id;
        return $DB->get_records_sql('SELECT s.id as submissionid, s.version as version, s.timecreated as submissioncreated, s.timemodified as submissionmodified, f.id as feedbackid, f.timecreated as feedbackcreated, f.timemodified as feedbackmodified, g.id as gradeid, g.grade as grade, g.grader as grader
    			FROM {newassign_submissions} s LEFT JOIN (SELECT * FROM {newassign_feedbacks} WHERE assignment = :assignment1) f ON s.id = f.submission LEFT JOIN (SELECT * FROM {newassign_grades} WHERE assignment = :assignment2) g ON s.id = g.submission WHERE s.userid = :user AND s.assignment = :assignment3 ORDER BY s.version DESC', array('user' => $userid, 'assignment1' => $assignmentId, 'assignment2' => $assignmentId, 'assignment3' => $assignmentId));
    }

    /**
     * Load the submission object for a particular user, optionally creating it if required
     *
     * @param int $userid The id of the user whose submission we want or 0 in which case USER->id is used
     * @param bool $create optional Defaults to false. If set to true a new submission object will be created in the database
     * @return stdClass The submission
     */
    private function get_user_submission($userid, $create) {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }
        // if the userid is not null then use userid
        $submissions = $DB->get_records('newassign_submissions', array('assignment' => $this->get_instance()->id, 'userid' => $userid), 'version DESC');

        if (count($submissions)) {
            if (!$DB->record_exists('newassign_feedbacks', array('submission' => reset($submissions)->id)))
                return reset($submissions);
        }
        if ($create) {
            $submission = new stdClass();
            $submission->assignment = $this->get_instance()->id;
            $submission->userid = $userid;
            $submission->timecreated = time();
            $submission->timemodified = $submission->timecreated;
            $submission->version = count($submissions) + 1;
            $sid = $DB->insert_record('newassign_submissions', $submission);
            $submission->id = $sid;
            return $submission;
        }
        return false;
    }

    private function get_user_feedback($userid, $create, $status = NEWASSIGN_FEEDBACK_STATUS_ACCEPTED) {
        global $DB;

        $submission = $this->get_last_user_submission($userid);

        if (!$submission && !$create)
            return false;

        if ($submission != false) {
            $feedback = $DB->get_record('newassign_feedbacks', array('submission' => $submission->id));
            if ($feedback)
                return $feedback;
        }

        if ($create) {
            if (!$submission)
                $submission = $this->get_user_submission($userid, true);
            $feedback = new stdClass();
            $feedback->assignment = $this->get_instance()->id;
            $feedback->submission = $submission->id;
            $feedback->userid = $userid;
            $feedback->timecreated = time();
            $feedback->timemodified = $feedback->timecreated;
            $feedback->status = $status;
            $feedback->id = $DB->insert_record('newassign_feedbacks', $feedback);
            return $feedback;
        }
        return false;
    }

    /**
     * Perform an access check to see if the current $USER can view this users submission
     *
     * @param int $userid
     * @return bool
     */
    public function can_view_submission($userid = 0) {
        global $USER;
        /*
          if (!is_enrolled($this->get_course_context(), $USER->id)) {
          return false;
          } */
        if ($userid == $USER->id && !has_capability('mod/newassignment:submit', $this->get_context())) {
            return false;
        }
        if ($userid != $USER->id && !has_capability('mod/newassignment:grade', $this->get_context())) {
            return false;
        }
        return true;
    }

    public function can_view_others_submissions() {
        global $USER, $DB;
        if (!is_enrolled($this->get_course_context(), $USER->id)) {
            return false;
        }

        if ($this->get_instance()->publishsubmissions == NEWASSIGN_PUBLISH_SUBMISSIONS_NO && $this->get_instance()->publishfeedbacks == NEWASSIGN_PUBLISH_FEEDBACKS_NO)
            return false;

        if (!has_capability('mod/newassignment:submit', $this->get_context()))
            return false;

        if ($this->get_instance()->publishsubmissions == NEWASSIGN_PUBLISH_SUBMISSIONS_AFTER_SUBMISSION)
            if ($DB->count_records('newassign_submissions', array('assignment' => $this->get_instance()->id)) == 0)
                return false;

        if ($this->get_instance()->publishsubmissions == NEWASSIGN_PUBLISH_SUBMISSIONS_AFTER_ACHIEVEMENT)
            if ($DB->count_records_sql('SELECT COUNT(*) FROM {newassign_submissions} AS s INNER JOIN {newassign_feedbacks} AS f ON s.id = f.submission WHERE s.assignment = :assignment AND f.status = :status', array('assignment' => $this->get_instance()->id, 'status' => NEWASSIGN_FEEDBACK_STATUS_ACCEPTED)) == 0)
                return false;

        if ($this->get_instance()->publishtime == NEWASSIGN_PUBLISH_AFTER_DUEDATE && $this->get_instance()->duedate > time())
            return false;

        if ($this->get_instance()->publishtime == NEWASSIGN_PUBLISH_AFTER_SUBMISSION && $DB->count_records('newassign_submissions', array('userid' => $USER->id, 'assignment' => $this->get_instance()->id)) == 0)
            return false;

        return true;
    }

    /**
     * Get context module
     *
     * @return context
     */
    public function get_context() {
        return $this->_context;
    }

    /**
     * Based on the current assignment settings should we display the intro
     * @return bool showintro
     */
    private function show_intro() {
        if ($this->get_instance()->alwaysshowdescription ||
                time() > $this->get_instance()->allowsubmissionsfromdate) {
            return true;
        }
        return false;
    }

    /**
     * Load a count of users enrolled in the current course with the specified permission and group (0 for no group)
     *
     * @param int $currentgroup
     * @return int number of matching users
     */
    public function count_participants($currentgroup) {
        return count_enrolled_users($this->_context, "mod/newassignment:submit", $currentgroup);
    }

    /**
     * See if this assignment has a grade yet
     *
     * @param int $userid
     * @return bool
     */
    private function is_graded($userid) {
        $grade = $this->get_user_grade($userid, false);
        if ($grade) {
            return ($grade->grade !== NULL);
        }
        return false;
    }

    /**
     * update grades in the gradebook based on submission time
     *
     * @param stdClass $submission
     * @param bool $updatetime
     * @return bool
     */
    private function update_submission(stdClass $submission, $updatetime = true) {
        global $DB;

        if ($updatetime) {
            $submission->timemodified = time();
        }
        $result = $DB->update_record('newassign_submissions', $submission);
        /* if ($result) {
          $this->gradebook_item_update($submission);
          } */
        return $result;
    }

    /**
     * update grades in the gradebook based on submission time
     *
     * @param stdClass $submission
     * @param bool $updatetime
     * @return bool
     */
    private function update_feedback(stdClass $feedback) {
        global $DB;

        $feedback->timemodified = time();
        $result = $DB->update_record('newassign_feedbacks', $feedback);

        if ($feedback->status == NEWASSIGN_FEEDBACK_STATUS_ACCEPTED) {
            $completion = new completion_info($this->get_course());
            if ($completion->is_enabled($this->get_course_module()) && $this->get_instance()->newassigncompletition == 1) {
                $completion->update_state($this->get_course_module(), COMPLETION_COMPLETE, $feedback->userid);
            }
        } else {
            $completion = new completion_info($this->get_course());
            if ($completion->is_enabled($this->get_course_module()) && $this->get_instance()->newassigncompletition == 1) {
                $completion->update_state($this->get_course_module(), COMPLETION_INCOMPLETE, $feedback->userid);
            }
        }

        return $result;
    }

    /**
     * Load a list of users enrolled in the current course with the specified permission and group (0 for no group)
     *
     * @param int $currentgroup
     * @param bool $idsonly
     * @return array List of user records
     */
    public function list_participants($currentgroup, $idsonly) {
        if ($idsonly) {
            return get_enrolled_users($this->get_context(), "mod/newassignment:submit", $currentgroup, 'u.id');
        } else {
            return get_enrolled_users($this->get_context(), "mod/newassignment:submit", $currentgroup);
        }
    }

    /**
     * update grades in the gradebook
     *
     * @param mixed $submission stdClass|null
     * @param mixed $grade stdClass|null
     * @return bool
     */
    private function gradebook_item_update($submission = NULL, $grade = NULL) {

        if ($submission != NULL) {
            $gradebookgrade = $this->convert_submission_for_gradebook($submission);
        } else {
            $gradebookgrade = $this->convert_grade_for_gradebook($grade);
        }
        // Grading is disabled, return.
        if ($this->grading_disabled($gradebookgrade['userid'])) {
            return false;
        }
        $assign = clone $this->get_instance();
        $assign->cmidnumber = $this->get_course_module()->id;

        return newassignment_grade_item_update($assign, $gradebookgrade);
    }

    /**
     * convert the final raw grade(s) in the  grading table for the gradebook
     *
     * @param stdClass $grade
     * @return array
     */
    private function convert_grade_for_gradebook(stdClass $grade) {
        global $DB;
        $gradebookgrade = array();
        // trying to match those array keys in grade update function in gradelib.php
        // with keys in th database table assign_grades
        // starting around line 262
        $gradingmanager = get_grading_manager($this->get_context(), 'mod_newassignment', 'submissions');


        $newgrade = false;
        switch ($this->get_instance()->grademethod) {
            case NEWASSIGN_ATTEMPTFIRST:
                $newgrade = $this->get_first_user_grade($grade->userid);
                break;
            case NEWASSIGN_ATTEMPTLAST:
                $gradebookgrade['rawgrade'] = $grade->grade;
                break;
            case NEWASSIGN_GRADEAVERAGE:
                $newgrade = $this->get_average_user_grade($grade->userid);
                break;
            case NEWASSIGN_GRADEHIGHEST:
                $newgrade = $this->get_highest_user_grade($grade->userid);
                break;
            default:
                $gradebookgrade['rawgrade'] = $grade->grade;
                break;
        }
        if ($newgrade)
            $gradebookgrade['rawgrade'] = $newgrade->grade;
        else
            $gradebookgrade['rawgrade'] = $grade->grade;


        $gradebookgrade['userid'] = $grade->userid;
        $gradebookgrade['usermodified'] = $grade->grader;
        $gradebookgrade['datesubmitted'] = NULL;
        $gradebookgrade['dategraded'] = $grade->timemodified;
        if (isset($grade->feedbackformat)) {
            $gradebookgrade['feedbackformat'] = $grade->feedbackformat;
        }
        if (isset($grade->feedbacktext)) {
            $gradebookgrade['feedback'] = $grade->feedbacktext;
        }

        return $gradebookgrade;
    }

    public function get_first_user_grade($userid) {
        global $DB;
        return $DB->get_record_sql('SELECT g.grade AS grade, g.id AS id FROM (SELECT grade, submission, id FROM {newassign_grades} WHERE assignment=:assignment1 AND userid=:userid1) AS g INNER JOIN (SELECT id FROM {newassign_submissions} WHERE userid=:userid2 AND assignment=:assignment2 AND version=1) AS s ON g.submission = s.id', array('assignment1' => $this->get_instance()->id,
                    'assignment2' => $this->get_instance()->id,
                    'userid1' => $userid,
                    'userid2' => $userid));
    }

    private function get_average_user_grade($userid) {
        global $DB;
        if ($DB->count_records('newassign_grades', array('assignment' => $this->get_instance()->id, 'userid' => $userid)) == 0)
            return false;
        return $DB->get_record_sql('SELECT AVG(grade) AS grade FROM {newassign_grades} WHERE assignment=:assignment AND userid=:userid', array('assignment' => $this->get_instance()->id, 'userid' => $userid));
    }

    private function get_highest_user_grade($userid, $newgrade = null) {
        global $DB;
        if ($DB->count_records('newassign_grades', array('assignment' => $this->get_instance()->id, 'userid' => $userid)) == 0)
            return false;
        return $DB->get_record_sql('SELECT MAX(grade) AS grade FROM {newassign_grades} WHERE assignment=:assignment AND userid=:userid', array('assignment' => $this->get_instance()->id, 'userid' => $userid));
    }

    /**
     * convert submission details for the gradebook
     *
     * @param stdClass $submission
     * @return array
     */
    private function convert_submission_for_gradebook(stdClass $submission) {
        $gradebookgrade = array();

        $gradebookgrade['userid'] = $submission->userid;
        $gradebookgrade['usermodified'] = $submission->userid;
        $gradebookgrade['datesubmitted'] = $submission->timemodified;

        return $gradebookgrade;
    }

    /**
     * Take a grade object and print a short summary for the log file.
     * The size limit for the log file is 255 characters, so be careful not
     * to include too much information.
     *
     * @param stdClass $grade
     * @return string
     */
    public function format_grade_for_log(stdClass $grade) {
        global $DB;

        $user = $DB->get_record('user', array('id' => $grade->userid), '*', MUST_EXIST);

        $info = get_string('gradestudent', 'newassignment', array('id' => $user->id, 'fullname' => fullname($user)));
        if ($grade->grade != '') {
            $info .= get_string('grade') . ': ' . $this->display_grade($grade->grade, false) . '. ';
        } else {
            $info .= get_string('nograde', 'newassignment');
        }
        return $info;
    }

    /**
     * Take a submission object and print a short summary for the log file.
     * The size limit for the log file is 255 characters, so be careful not
     * to include too much information.
     *
     * @param stdClass $submission
     * @return string
     */
    private function format_submission_for_log(stdClass $submission) {
        global $CFG;
        $info = '';
        $info .= get_string('submissionstatus', 'newassignment') . ': ' . get_string('submissionstatus_submitted', 'newassignment') . '. <br>';
        // format_for_log here iterating every single log INFO  from either submission or grade in every assignment plugin

        $plugin = $this->get_submission_plugin();
        $info .= $plugin->format_for_log($submission);

        return $info;
    }

    /**
     * Notify student upon successful submission
     *
     * @global moodle_database $DB
     * @param stdClass $submission
     * @return void
     */
    private function notify_student_submission_receipt(stdClass $submission) {
        global $DB;

        $adminconfig = $this->get_admin_config();
        if (!$adminconfig->submissionreceipts) {
            // No need to do anything
            return;
        }
        $user = $DB->get_record('user', array('id' => $submission->userid), '*', MUST_EXIST);
        $this->send_notification($user, $user, 'submissionreceipt', 'newassignment_notification', $submission->timemodified);
    }

    /**
     * Send notifications to graders upon student submissions
     *
     * @global moodle_database $DB
     * @param stdClass $submission
     * @return void
     */
    private function notify_graders(stdClass $submission) {
        global $DB;
        
        $late = $this->get_instance()->duedate && ($this->get_instance()->duedate < time());

        if (!$this->get_instance()->sendnotifications && !($late && $this->get_instance()->sendlatenotifications)) {          // No need to do anything
            return;
        }

        $user = $DB->get_record('user', array('id' => $submission->userid), '*', MUST_EXIST);
        if ($teachers = $this->get_graders($user->id)) {
            foreach ($teachers as $teacher) {
                $this->send_notification($user, $teacher, 'gradersubmissionupdated', 'newassignment_notification', $submission->timemodified);
            }
        }
    }

    /**
     * Returns a list of teachers that should be grading given submission
     *
     * @param int $userid
     * @return array
     */
    private function get_graders($userid) {
        //potential graders
        $potentialgraders = get_enrolled_users($this->get_context(), "mod/newassignment:grade");

        $graders = array();
        if (groups_get_activity_groupmode($this->get_course_module()) == SEPARATEGROUPS) {   // Separate groups are being used
            if ($groups = groups_get_all_groups($this->get_course()->id, $userid)) {  // Try to find all groups
                foreach ($groups as $group) {
                    foreach ($potentialgraders as $grader) {
                        if ($grader->id == $userid) {
                            continue; // do not send self
                        }
                        if (groups_is_member($group->id, $grader->id)) {
                            $graders[$grader->id] = $grader;
                        }
                    }
                }
            } else {
                // user not in group, try to find graders without group
                foreach ($potentialgraders as $grader) {
                    if ($grader->id == $userid) {
                        continue; // do not send self
                    }
                    if (!groups_has_membership($this->get_course_module(), $grader->id)) {
                        $graders[$grader->id] = $grader;
                    }
                }
            }
        } else {
            foreach ($potentialgraders as $grader) {
                if ($grader->id == $userid) {
                    continue; // do not send self
                }
                // must be enrolled
                if (is_enrolled($this->get_course_context(), $grader->id)) {
                    $graders[$grader->id] = $grader;
                }
            }
        }
        return $graders;
    }

    /** Load and cache the admin config for this module
     *
     * @return stdClass the plugin config
     */
    public function get_admin_config() {
        if ($this->_adminconfig) {
            return $this->_adminconfig;
        }
        $this->_adminconfig = get_config('newassignment');
        return $this->_adminconfig;
    }

    /**
     * Message someone about something
     *
     * @param stdClass $userfrom
     * @param stdClass $userto
     * @param string $messagetype
     * @param string $eventtype
     * @param int $updatetime
     * @return void
     */
    public function send_notification($userfrom, $userto, $messagetype, $eventtype, $updatetime) {
        self::send_assignment_notification($userfrom, $userto, $messagetype, $eventtype, $updatetime, $this->get_course_module(), $this->get_context(), $this->get_course(), $this->get_module_name(), $this->get_instance()->name);
    }

    /**
     * Format a notification for plain text
     *
     * @param string $messagetype
     * @param stdClass $info
     * @param stdClass $course
     * @param stdClass $context
     * @param string $modulename
     * @param string $assignmentname
     */
    private static function format_notification_message_text($messagetype, $info, $course, $context, $modulename, $assignmentname) {
        $posttext = format_string($course->shortname, true, array('context' => $context->get_course_context())) . ' -> ' .
                $modulename . ' -> ' .
                format_string($assignmentname, true, array('context' => $context)) . "\n";
        $posttext .= '---------------------------------------------------------------------' . "\n";
        $posttext .= get_string($messagetype . 'text', "newassignment", $info) . "\n";
        $posttext .= "\n---------------------------------------------------------------------\n";
        return $posttext;
    }

    /**
     * Format a notification for HTML
     *
     * @param string $messagetype
     * @param stdClass $info
     * @param stdClass $course
     * @param stdClass $context
     * @param string $modulename
     * @param stdClass $coursemodule
     * @param string $assignmentname
     * @param stdClass $info
     */
    private static function format_notification_message_html($messagetype, $info, $course, $context, $modulename, $coursemodule, $assignmentname) {
        global $CFG;
        $posthtml = '<p><font face="sans-serif">' .
                '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $course->id . '">' . format_string($course->shortname, true, array('context' => $context->get_course_context())) . '</a> ->' .
                '<a href="' . $CFG->wwwroot . '/mod/newassignment/index.php?id=' . $course->id . '">' . $modulename . '</a> ->' .
                '<a href="' . $CFG->wwwroot . '/mod/newassignment/view.php?id=' . $coursemodule->id . '">' . format_string($assignmentname, true, array('context' => $context)) . '</a></font></p>';
        $posthtml .= '<hr /><font face="sans-serif">';
        $posthtml .= '<p>' . get_string($messagetype . 'html', 'newassignment', $info) . '</p>';
        $posthtml .= '</font><hr />';
        return $posthtml;
    }

    /**
     * Message someone about something (static so it can be called from cron)
     *
     * @param stdClass $userfrom
     * @param stdClass $userto
     * @param string $messagetype
     * @param string $eventtype
     * @param int $updatetime
     * @param stdClass $coursemodule
     * @param stdClass $context
     * @param stdClass $course
     * @param string $modulename
     * @param string $assignmentname
     * @return void
     */
    public static function send_assignment_notification($userfrom, $userto, $messagetype, $eventtype, $updatetime, $coursemodule, $context, $course, $modulename, $assignmentname) {
        global $CFG;
        $info = new stdClass();
        $info->username = fullname($userfrom, true);
        $info->assignment = format_string($assignmentname, true, array('context' => $context));
        $info->url = $CFG->wwwroot . '/mod/newassignment/view.php?id=' . $coursemodule->id;
        $info->timeupdated = strftime('%c', $updatetime);

        $postsubject = get_string($messagetype . 'small', 'newassignment', $info);
        $posttext = self::format_notification_message_text($messagetype, $info, $course, $context, $modulename, $assignmentname);
        $posthtml = ($userto->mailformat == 1) ? self::format_notification_message_html($messagetype, $info, $course, $context, $modulename, $coursemodule, $assignmentname) : '';

        $eventdata = new stdClass();
        $eventdata->modulename = 'newassignment';
        $eventdata->userfrom = $userfrom;
        $eventdata->userto = $userto;
        $eventdata->subject = $postsubject;
        $eventdata->fullmessage = $posttext;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml = $posthtml;
        $eventdata->smallmessage = $postsubject;

        $eventdata->name = $eventtype;
        $eventdata->component = 'mod_newassignment';
        $eventdata->notification = 1;
        $eventdata->contexturl = $info->url;
        $eventdata->contexturlname = $info->assignment;
        message_send($eventdata);
    }

    public function get_submission_plugin() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/newassignment/submissions/' . $this->get_instance()->submissiontype . '.php');
        $class = "mod_newassignment_submission_" . $this->get_instance()->submissiontype;
        return new $class($this);
    }

    private function is_first_submission($userid) {
        global $DB;
        return !$DB->record_exists('newassign_submissions', array('assignment' => $this->get_instance()->id, 'userid' => $userid));
    }

    /**
     * Get the name of the current module.
     *
     * @return string the module name (Assignment)
     */
    protected function get_module_name() {
        if (isset(self::$modulename)) {
            return self::$modulename;
        }
        self::$modulename = get_string('modulename', 'newassignment');
        return self::$modulename;
    }

    /**
     * Get the plural name of the current module.
     *
     * @return string the module name plural (Assignments)
     */
    protected function get_module_name_plural() {
        if (isset(self::$modulenameplural)) {
            return self::$modulenameplural;
        }
        self::$modulenameplural = get_string('modulenameplural', 'newassignment');
        return self::$modulenameplural;
    }

    public function check_completition($userid) {
        $feedback = $this->get_user_feedback($userid, false);
        if (!$feedback) {
            return false;
        }
        if ($feedback->status == NEWASSIGN_FEEDBACK_STATUS_ACCEPTED) {
            return true;
        }
        return false;
    }

}