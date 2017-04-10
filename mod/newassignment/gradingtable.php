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
 * This file contains the definition for the grading table which subclassses easy_table
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/mod/newassignment/locallib.php');

/**
 * Extends table_sql to provide a table of assignment submissions
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class newassignment_grading_table extends table_sql implements renderable {
    /** @var NewAssignment $assignment */
    private $assignment = null;
    /** @var int $perpage */
    private $perpage = 10;
    /** @var int $rownum (global index of current row in table) */
    private $rownum = -1;
    /** @var renderer_base for getting output */
    private $output = null;
    /** @var stdClass gradinginfo */
    private $gradinginfo = null;
    /** @var int $tablemaxrows */
    private $tablemaxrows = 10000;
    /** @var boolean $quickgrading */
    private $quickgrading = false;

    /**
     * overridden constructor keeps a reference to the assignment class that is displaying this table
     *
     * @param assign $assignment The assignment class
     * @param int $perpage how many per page
     * @param string $filter The current filter
     * @param int $rowoffset For showing a subsequent page of results
     * @param bool $quickgrading Is this table wrapped in a quickgrading form?
     */
    function __construct(NewAssignment $assignment, $perpage, $filter, $rowoffset, $quickgrading) {
        global $CFG, $PAGE, $DB;
        parent::__construct('mod_newassignment_grading');
        $this->assignment = $assignment;
        $this->perpage = $perpage;
        $this->quickgrading = $quickgrading;
        $this->output = $PAGE->get_renderer('mod_newassignment');
        
        $this->define_baseurl(new moodle_url($CFG->wwwroot . '/mod/newassignment/view.php', array('action'=>'grading', 'id'=>$assignment->get_course_module()->id)));

        // do some business - then set the sql

        $currentgroup = groups_get_activity_group($assignment->get_course_module(), true);

        if ($rowoffset) {
            $this->rownum = $rowoffset - 1;
        }

        $users = array_keys( $assignment->list_participants($currentgroup, true));
        if (count($users) == 0) {
            // insert a record that will never match to the sql is still valid.
            $users[] = -1;
        }

        $params = array();
        $params['assignmentid1'] = $params['assignmentid2'] = 
		$params['assignmentid3'] = $params['assignmentid4'] = (int)$this->assignment->get_instance()->id;
		//var_dump($params); die;
		$extrauserfields = get_extra_user_fields($this->assignment->get_context());
		$fields = user_picture::fields('u',$extrauserfields) . ',  u.id AS userid, ';
        $fields .= 's.id as submissionid, s.version as version, s.timecreated as firstsubmission, s.timemodified as timesubmitted, ';
        $fields .= 'g.id as gradeid, g.grade as grade, g.timemodified as timemarked, g.timecreated as firstmarked,';
        $fields .= 'f.id as feedbackid, f.status as feedbackstatus';
        $from = '{user} u LEFT JOIN (SELECT ss.id, ss.userid, ss.version, ss.assignment, ss.timecreated, ss.timemodified FROM (SELECT userid, MAX(version) AS maxversion FROM {newassign_submissions} WHERE assignment = :assignmentid1 GROUP BY userid) x INNER JOIN {newassign_submissions} ss ON x.userid = ss.userid AND ss.version = x.maxversion WHERE assignment = :assignmentid2) s ON s.userid = u.id' .
                        ' LEFT JOIN (SELECT * FROM {newassign_grades} WHERE assignment = :assignmentid3) g ON s.id = g.submission LEFT JOIN (SELECT * FROM {newassign_feedbacks} WHERE assignment = :assignmentid4) f ON s.id = f.submission';

        $userparams = array();
        $userindex = 0;

        list($userwhere, $userparams) = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED, 'user');
        $where = 'u.id ' . $userwhere;
        $params = array_merge($params, $userparams);

        if ($filter == NEWASSIGN_FILTER_SUBMITTED) {
            $where .= ' AND s.timecreated > 0 ';
        }
        if ($filter == NEWASSIGN_FILTER_REQUIRE_GRADING) {
            $where .= ' AND (s.id IS NOT NULL AND g.timemodified IS NULL)';
        }
        $this->set_sql($fields, $from, $where, $params);

        $columns = array();
        $headers = array();

        // User picture
        $columns[] = 'picture';
        $headers[] = get_string('pictureofuser');

        // Fullname
        $columns[] = 'fullname';
        $headers[] = get_string('fullname');

        // Submission status
        $columns[] = 'status';
        $headers[] = get_string('status');


        // Grade
        $columns[] = 'grade';
        $headers[] = get_string('grade');

        // Submission plugins
        $columns[] = 'timesubmitted';
        $headers[] = get_string('lastmodifiedsubmission', 'newassignment');

        $columns[] = 'version';
        $headers[] = get_string('submissionversion', 'newassignment');
        
        $columns[] = 'submission';
        $headers[] = get_string('submission', 'newassignment');

        if($this->assignment->get_instance()->submissioncomments) {
        	$columns[] = 'submissioncomments';
        	$headers[] = get_string('submissioncomments', 'newassignment');
        }
        
        // time marked
        $columns[] = 'timemarked';
        $headers[] = get_string('lastmodifiedgrade', 'newassignment');

        // Feedback plugins
        $columns[] = 'feedback';
        $headers[] = get_string('feedback', 'newassignment');
		

        // final grade
        $columns[] = 'finalgrade';
        $headers[] = get_string('finalgrade', 'grades');

        // load the grading info for all users
        $this->gradinginfo = grade_get_grades($this->assignment->get_course()->id, 'mod', 'newassignment', $this->assignment->get_instance()->id, $users);

        if (!empty($CFG->enableoutcomes) && !empty($this->gradinginfo->outcomes)) {
            $columns[] = 'outcomes';
            $headers[] = get_string('outcomes', 'grades');
        }


        // set the columns
        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->no_sorting('finalgrade');
        $this->no_sorting('outcomes');
        $this->no_sorting('submission');
        $this->no_sorting('feedback');
    }

    /**
     * Add the userid to the row class so it can be updated via ajax
     *
     * @param stdClass $row The row of data
     * @return string The row class
     */
    function get_row_class($row) {
        return 'user' . $row->userid;
    }

    /**
     * Return the number of rows to display on a single page
     *
     * @return int The number of rows per page
     */
    function get_rows_per_page() {
        return $this->perpage;
    }

    /**
     * Display a grade with scales etc.
     *
     * @param string $grade
     * @param boolean $editable
     * @param int $userid The user id of the user this grade belongs to
     * @param int $modified Timestamp showing when the grade was last modified
     * @return string The formatted grade
     */
    function display_grade($grade, $editable, $userid, $modified) {
        if ($this->is_downloading()) {
            return $grade;
        }
        $o = $this->assignment->display_grade($grade, $editable, $userid, $modified);
        return $o;
    }

    /**
     * Format a list of outcomes
     *
     * @param stdClass $row
     * @return string
     */
    function col_outcomes(stdClass $row) {
        $outcomes = '';
        foreach($this->gradinginfo->outcomes as $index=>$outcome) {
            $options = make_grades_menu(-$outcome->scaleid);

            $options[0] = get_string('nooutcome', 'grades');
            if ($this->quickgrading && !($outcome->grades[$row->userid]->locked)) {
                $select = '<select name="outcome_' . $index . '_' . $row->userid . '" class="quickgrade">';
                foreach ($options as $optionindex => $optionvalue) {
                    $selected = '';
                    if ($outcome->grades[$row->userid]->grade == $optionindex) {
                        $selected = 'selected="selected"';
                    }
                    $select .= '<option value="' . $optionindex . '"' . $selected . '>' . $optionvalue . '</option>';
                }
                $select .= '</select>';
                $outcomes .= $this->output->container($outcome->name . ': ' . $select, 'outcome');
            } else {
                $outcomes .= $this->output->container($outcome->name . ': ' . $options[$outcome->grades[$row->userid]->grade], 'outcome');
            }
        }

        return $outcomes;
    }


    /**
     * Format a user picture for display (and update rownum as a sideeffect)
     *
     * @param stdClass $row
     * @return string
     */
    function col_picture(stdClass $row) {
        if ($row->picture) {
            return $this->output->user_picture(user_picture::unalias($row));
        }
        return '';
    }

    /**
     * Format a user record for display (don't link to profile)
     *
     * @param stdClass $row
     * @return string
     */
    function col_fullname($row) {
    	if ($this->rownum < 0) {
    		$this->rownum = $this->currpage * $this->pagesize;
    	} else {
    		$this->rownum += 1;
    	}
    	
		$extrauserfields = get_extra_user_fields($this->assignment->get_context());
		$student = user_picture::unalias($row, $extrauserfields);
		return fullname($student);
    }

    /**
     * Return a users grades from the listing of all grade data for this assignment
     *
     * @param int $userid
     * @return mixed stdClass or false
     */
    private function get_gradebook_data_for_user($userid) {
        if (isset($this->gradinginfo->items[0]) && $this->gradinginfo->items[0]->grades[$userid]) {
            return $this->gradinginfo->items[0]->grades[$userid];
        }
        return false;
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    function col_grade(stdClass $row) {
        $link = '';
        $separator = '';

        if (!$this->is_downloading()) {
            $icon = $this->output->pix_icon('gradefeedback', get_string('grade'), 'mod_newassignment');
            $url = new moodle_url('/mod/newassignment/view.php',
                                            array('id' => $this->assignment->get_course_module()->id,
                                                  'rownum'=>$this->rownum,'action'=>'grade'));
            $link = $this->output->action_link($url, $icon);
            $separator = $this->output->spacer(array(), true);
        }
        $gradingdisabled = $this->assignment->grading_disabled($row->id);
        $grade = $this->display_grade($row->grade, $this->quickgrading && !$gradingdisabled, $row->userid, $row->timemarked);
        
        //return $grade . $separator . $link;
        return $link . $separator . $grade;
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    function col_finalgrade(stdClass $row) {
        $o = '';

        $grade = $this->get_gradebook_data_for_user($row->userid);
        if ($grade) {
            $o = $this->display_grade($grade->grade, false, $row->userid, $row->timemarked);
        }

        return $o;
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    function col_timemarked(stdClass $row) {
        $o = '-';

        if ($row->timemarked && $row->grade !== NULL && $row->grade >= 0) {
            $o = userdate($row->timemarked);
        }

        return $o;
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    function col_timesubmitted(stdClass $row) {
        $o = '-';

        if ($row->timesubmitted) {
            $o = userdate($row->timesubmitted);
        }

        return $o;
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    function col_status(stdClass $row) {
        $o = '';

        if(isset($row->submissionid)) {
        	$o .= $this->output->container(get_string('submissionstatus_submitted', 'newassignment'), array('class'=>'submissionstatussubmitted'));
        }
        if ($this->assignment->get_instance()->duedate && $row->timesubmitted > $this->assignment->get_instance()->duedate) {
            $o .= $this->output->container(get_string('submittedlateshort', 'newassignment', format_time($row->timesubmitted - $this->assignment->get_instance()->duedate)), 'latesubmissiontable');
        }
        if ($row->grade !== NULL && $row->grade >= 0) {
            $o .= $this->output->container(get_string('graded', 'newassignment'), 'submissiongraded');
        }
        
        if($this->quickgrading) {
    		$accsel = '';
    		$decsel = '';
                $nonesel = '';
    		if($row->feedbackstatus !== NULL && $row->feedbackstatus == NEWASSIGN_FEEDBACK_STATUS_DECLINED) {
    			$decsel = 'selected="selected"';
    		} elseif($row->feedbackstatus !== NULL && $row->feedbackstatus == NEWASSIGN_FEEDBACK_STATUS_ACCEPTED) {
    			$accsel = 'selected="selected"';
    		} else {
                    $nonesel = 'selected="selected"';
                }
        	$o .= '<select name="feedbackstatus_quick_'.$row->userid.'" class="quickgrade">';
                //$gradingdisabled = $this->assignment->grading_disabled($row->id);
                //if($this->display_grade($row->grade, $this->quickgrading && !$gradingdisabled, $row->userid, $row->timemarked) == '-')
                    $o .= '<option value="none" '.$nonesel.'></option>';
        	$o .= '<option value="'.NEWASSIGN_FEEDBACK_STATUS_ACCEPTED.'" '.$accsel.'>'.get_string('feedbackstatus_accepted','newassignment').'</option>';
        	$o .= '<option value="'.NEWASSIGN_FEEDBACK_STATUS_DECLINED.'" '.$decsel.'>'.get_string('feedbackstatus_declined','newassignment').'</option>';
        	$o .= '</select>';
        } else {
        	if($row->feedbackstatus !== NULL) {
        		$o .= $this->output->container(get_string('feedbackstatus_'.$row->feedbackstatus,'newassignment'),array('class'=>'feedbackstatus' .$row->feedbackstatus));
        	}
        }
        
        return $o;
    }
    
    function col_version(stdClass $row) {
    	return $row->version;
    }
    
    function col_submission(stdClass $row) {
    	if ($row->submissionid) {
            $submission = new stdClass();
            $submission->id = $row->submissionid;
            $submission->timecreated = $row->firstsubmission;
            $submission->timemodified = $row->timesubmitted;
                    $submission->assignment = $this->assignment->get_instance()->id;
            $submission->userid = $row->userid;
            $return = $this->assignment->get_submission_plugin()->view_summary($submission,'grading');

            if(isset($row->submissionid) && $row->version != 1) {
                $return .= ' ('.$this->output->action_link(new moodle_url('/mod/newassignment/submissions.php',array('id' => $this->assignment->get_course_module()->id,'user'=>$row->id)),
                                get_string('showallversions','newassignment'), null, array('target'=>'_blank')).')';
            }
            return $return;
    	}
    	return '';
    }
    
    function col_feedback(stdClass $row) {
    	global $CFG;
    	require_once($CFG->dirroot.'/mod/newassignment/feedbacks/comment.php');
    	$o = '';
    	
    	if($row->feedbackid) {
    		$feedback = new stdClass();
    		$feedback->id = $row->feedbackid;
    		
    		$fcomment = new mod_newassignment_feedback_comment($this->assignment);
    		if($this->quickgrading) {
    			$o .= $fcomment->get_quickgrading_html($row->userid, $feedback);
    		} else {
    			$o .= $fcomment->view_summary($feedback);
    		}

    		require_once($CFG->dirroot.'/mod/newassignment/feedbacks/file.php');
    		$ffile = new mod_newassignment_feedback_file($this->assignment);
    		$o .= $ffile->view_summary($feedback);
    	} else if($this->quickgrading) {
    		$fcomment = new mod_newassignment_feedback_comment($this->assignment);
    		$o .= $fcomment->get_quickgrading_html($row->userid, null);
    	}
    	return $o;
    }
    
    function col_submissioncomments(stdClass $row) {
    	global $CFG;
    	require_once($CFG->dirroot.'/mod/newassignment/submissions/comment.php');
    	$comment = new mod_newassignment_submission_comment($this->assignment);
    	$submission = new stdClass;
    	$submission->id = $row->submissionid;
    	return $comment->view_summary($submission);
    }

    /**
     * Using the current filtering and sorting - load all rows and return a single column from them
     *
     * @param string $columnname The name of the raw column data
     * @return array of data
     */
    function get_column_data($columnname) {
        $this->setup();
        $this->currpage = 0;
        $this->query_db($this->tablemaxrows);
        $result = array();
        foreach ($this->rawdata as $row) {
            $result[] = $row->$columnname;
        }
        return $result;
    }
    /**
     * Using the current filtering and sorting - load a single row and return a single column from it
     *
     * @param int $rownumber The rownumber to load
     * @param string $columnname The name of the raw column data
     * @param bool $lastrow Set to true if this is the last row in the table
     * @return mixed string or false
     */
    function get_cell_data($rownumber, $columnname, $lastrow) {
        $this->setup();
        $this->currpage = $rownumber;
        $this->query_db(1);
        if ($rownumber == $this->totalrows-1) {
            $lastrow = true;
        }
        foreach ($this->rawdata as $row) {
            return $row->$columnname;
        }
        return false;
    }

    /**
     * Return things to the renderer
     *
     * @return string the assignment name
     */
    function get_assignment_name() {
        return $this->assignment->get_instance()->name;
    }

    /**
     * Return things to the renderer
     *
     * @return int the course module id
     */
    function get_course_module_id() {
        return $this->assignment->get_course_module()->id;
    }

    /**
     * Return things to the renderer
     *
     * @return int the course id
     */
    function get_course_id() {
        return $this->assignment->get_course()->id;
    }

    /**
     * Return things to the renderer
     *
     * @return stdClass The course context
     */
    function get_course_context() {
        return $this->assignment->get_course_context();
    }

    /**
     * Return things to the renderer
     *
     * @return bool Does this assignment accept submissions
     */
    function submissions_enabled() {
        return $this->assignment->is_any_submission_plugin_enabled();
    }

    /**
     * Return things to the renderer
     *
     * @return bool Can this user view all grades (the gradebook)
     */
    function can_view_all_grades() {
        return has_capability('gradereport/grader:view', $this->assignment->get_course_context()) && has_capability('moodle/grade:viewall', $this->assignment->get_course_context());
    }

    /**
     * Override the table show_hide_link to not show for select column
     *
     * @param string $column the column name, index into various names.
     * @param int $index numerical index of the column.
     * @return string HTML fragment.
     */
    protected function show_hide_link($column, $index) {
        if ($index > 0) {
            return parent::show_hide_link($column, $index);
        }
        return '';
    }
}
