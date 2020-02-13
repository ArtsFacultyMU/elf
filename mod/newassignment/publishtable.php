<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/mod/newassignment/locallib.php');

class newassignment_publish_table extends table_sql implements renderable {

	/** @var assign $assignment */
	private $assignment = null;
	/** @var int $rownum (global index of current row in table) */
	private $rownum = -1;
	/** @var renderer_base for getting output */
	private $output = null;
	/** @var int $tablemaxrows */
	private $tablemaxrows = 10000;
	
	
	function __construct(NewAssignment $assignment) {
		global $CFG, $PAGE, $DB;
		parent::__construct('mod_newassignment_publishing');
		$this->assignment = $assignment;
		$this->output = $PAGE->get_renderer('mod_newassignment');
	
		$this->define_baseurl(new moodle_url($CFG->wwwroot . '/mod/newassignment/view.php', array('action'=>'publish', 'id'=>$assignment->get_course_module()->id)));
	
		// do some business - then set the sql	
		
		$params = array();
		$params['assignmentid1'] = $params['assignmentid2'] = $params['assignmentid3'] = $params['assignmentid4'] = (int)$this->assignment->get_instance()->id;

		$currentgroup = groups_get_activity_group($assignment->get_course_module(), true);
				
		$users = array_keys( $assignment->list_participants($currentgroup, true));
		if (count($users) == 0) {
			// insert a record that will never match to the sql is still valid.
			$users[] = -1;
		}
		$extrauserfields = get_extra_user_fields($this->assignment->get_context());
		$fields = user_picture::fields('u',$extrauserfields,'studentid','student') . ', ';
		$fields .= 's.id as submissionid, s.version as submissionversion, s.timemodified as timesubmitted, ';
		$fields .= 'g.id as gradeid, g.grade as grade, g.timemodified as timemarked, ';
		$fields .= user_picture::fields('gu',$extrauserfields,'graderid','grader').', ';
		$fields .= 'f.id as feedbackid, f.status as feedbackstatus';
		$from = '{user} u LEFT JOIN (SELECT ss.id, ss.userid, ss.version, ss.assignment, ss.timecreated, ss.timemodified FROM (SELECT userid, MAX(version) AS maxversion FROM {newassign_submissions} WHERE assignment = :assignmentid1 GROUP BY userid) x INNER JOIN {newassign_submissions} ss ON x.userid = ss.userid AND ss.version = x.maxversion  WHERE assignment = :assignmentid2) s ON s.userid = u.id' .
				' LEFT JOIN (SELECT * FROM {newassign_grades} WHERE assignment = :assignmentid3) g ON s.id = g.submission LEFT JOIN (SELECT * FROM {newassign_feedbacks} WHERE assignment = :assignmentid4) f ON s.id = f.submission LEFT JOIN {user} gu ON g.grader = gu.id';
	
		$userparams = array();
		$userindex = 0;
		
		list($userwhere, $userparams) = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED, 'user');
		$where = 'u.id ' . $userwhere;
		$params = array_merge($params, $userparams);
		
		if($assignment->get_instance()->publishsubmissions == NEWASSIGN_PUBLISH_SUBMISSIONS_AFTER_ACHIEVEMENT) {
			$where .= ' AND f.status = :feedbackstatus';
			$params['feedbackstatus'] = NEWASSIGN_FEEDBACK_STATUS_ACCEPTED;
		} elseif($assignment->get_instance()->publishsubmissions == NEWASSIGN_PUBLISH_SUBMISSIONS_AFTER_SUBMISSION) {
			$where .= ' AND s.id IS NOT NULL';
		}	
		
		$this->set_sql($fields, $from, $where, $params);
	
		
		
		$columns = array();
		$headers = array();

		if($assignment->get_instance()->publishsubmissions != NEWASSIGN_PUBLISH_SUBMISSIONS_NO) {
			if($assignment->get_instance()->publishsubmissionsanonymously != NEWASSIGN_PUBLISH_SUBMISSIONS_ANONYMOUSLY) {
				// User picture
				$columns[] = 'studentpicture';
				$headers[] = get_string('pictureofuser');
				
				// Fullname
				$columns[] = 'studentfullname';
				$headers[] = get_string('student', 'newassignment');
				
				$columns[] = 'version';
				$headers[] = get_string('submissionversion', 'newassignment');
			}
			$columns[] = 'submission';
			$headers[] = get_string('submission', 'newassignment');
		}
	
		if($assignment->get_instance()->publishfeedbacks != NEWASSIGN_PUBLISH_FEEDBACKS_NO) {
			if($assignment->get_instance()->publishfeedbacksanonymously != NEWASSIGN_PUBLISH_FEEDBACKS_ANONYMOUSLY) {
				// grader informations
				$columns[] = 'graderpicture';
				$headers[] = '';
				
				$columns[] = 'graderfullname';
				$headers[] = get_string('gradedby', 'newassignment');
			}
		
			// Feedback plugins
			$columns[] = 'feedback';
			$headers[] = get_string('feedback', 'newassignment');
		}
	
	
		// set the columns
		$this->define_columns($columns);
		$this->define_headers($headers);
		$this->no_sorting('submission');
		$this->no_sorting('feedback');
		
		$this->pageable(false);
	}
	
	/**
	 * Add the userid to the row class so it can be updated via ajax
	 *
	 * @param stdClass $row The row of data
	 * @return string The row class
	 */
	function get_row_class($row) {
		return 'user' . $row->studentid;
	}
	
	/**
	 * Return the number of rows to display on a single page
	 *
	 * @return int The number of rows per page
	 */
	function get_rows_per_page() {
		return 100;
	}
	
	/**
	 * Format a user picture for display (and update rownum as a sideeffect)
	 *
	 * @param stdClass $row
	 * @return string
	 */
	function col_studentpicture(stdClass $row) {
		if ($row->studentpicture) {
			return $this->output->user_picture(user_picture::unalias($row,array(),'studentid','student'));
		}
		return '';
	}
	
	/**
	 * Format a user picture for display (and update rownum as a sideeffect)
	 *
	 * @param stdClass $row
	 * @return string
	 */
	function col_graderpicture(stdClass $row) {
		if ($row->graderpicture) {
			return $this->output->user_picture(user_picture::unalias($row,array(),'graderid','grader'));
		}
		return '';
	}
	
	function col_version($row) {
		return $row->submissionversion;
	}
	
	/**
	 * Format a user record for display (don't link to profile)
	 *
	 * @param stdClass $row
	 * @return string
	 */
	function col_studentfullname($row) {
		$extrauserfields = get_extra_user_fields($this->assignment->get_context());
		$student = user_picture::unalias($row, $extrauserfields, 'studentid', 'student');
		return fullname($student);
	}
	
	/**
	 * Format a user record for display (don't link to profile)
	 *
	 * @param stdClass $row
	 * @return string
	 */
	function col_graderfullname($row) {
            $extrauserfields = get_extra_user_fields($this->assignment->get_context());
            $grader = user_picture::unalias($row, $extrauserfields, 'graderid', 'grader');
            return fullname($grader);
	}
	
	function col_submission($row) {
            $plugin = $this->assignment->get_submission_plugin();
            $submission = new stdClass();
            $submission->id = $row->submissionid;
            return $plugin->view_summary($submission);
	}
	
	function col_feedback($row) {
		global $CFG;
		$feedback = new stdClass();
		$feedback->id = $row->feedbackid;
		$o = '';
		
		if($this->assignment->get_instance()->publishfeedbacks == NEWASSIGN_PUBLISH_FEEDBACKS_FILES_COMMENTS) {
			require_once($CFG->dirroot.'/mod/newassignment/feedbacks/comment.php');
			$fcomment = new mod_newassignment_feedback_comment($this->assignment);
			$o .= $fcomment->view_summary($feedback);
			require_once($CFG->dirroot.'/mod/newassignment/feedbacks/file.php');
			$ffile = new mod_newassignment_feedback_file($this->assignment);
			$o .= $ffile->view_summary($feedback);
		}
		
		if($this->assignment->get_instance()->publishfeedbacks == NEWASSIGN_PUBLISH_FEEDBACKS_FILES) {
			require_once($CFG->dirroot.'/mod/newassignment/feedbacks/file.php');
			$ffile = new mod_newassignment_feedback_file($this->assignment);
			$o .= $ffile->view_summary($feedback);
		} 
		
		return $o;
	}
	
	function other_cols($colname, $row){
		return '';
	}
}