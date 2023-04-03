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
 * Library of interface functions and constants for module newmodule
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the newmodule specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod
 * @subpackage newmodule
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/** example constant */
//define('NEWMODULE_ULTIMATE_ANSWER', 42);
////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function newassignment_supports($feature) {
    switch ($feature) {
        case FEATURE_GROUPS: return true;
        case FEATURE_GROUPINGS: return true;
        case FEATURE_GROUPMEMBERSONLY: return true;
        case FEATURE_MOD_INTRO: return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_COMPLETION_HAS_RULES: return true;
        case FEATURE_GRADE_HAS_GRADE: return true;
        case FEATURE_GRADE_OUTCOMES: return true;
        case FEATURE_BACKUP_MOODLE2: return true;
        case FEATURE_SHOW_DESCRIPTION: return true;
        case FEATURE_ADVANCED_GRADING: return true;

        default: return null;
    }
}

/**
 * Lists all gradable areas for the advanced grading methods gramework
 *
 * @return array('string'=>'string') An array with area names as keys and descriptions as values
 */
function newassignment_grading_areas_list() {
    return array('submissions' => get_string('submissions', 'newassignment'));
}

/**
 * Saves a new instance of the newmodule into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $newmodule An object from the form in mod_form.php
 * @param mod_newmodule_mod_form $mform
 * @return int The id of the newly inserted newmodule record
 */
function newassignment_add_instance(stdClass $formdata, mod_newassignment_mod_form $mform = null) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/newassignment/locallib.php');

    $assignment = new NewAssignment();
    return $assignment->add_instance($formdata);
}

/**
 * Updates an instance of the newmodule in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $newmodule An object from the form in mod_form.php
 * @param mod_newmodule_mod_form $mform
 * @return boolean Success/Fail
 */
function newassignment_update_instance(stdClass $formdata, mod_newassignment_mod_form $mform = null) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/newassignment/locallib.php');

    $assignment = new NewAssignment(context_module::instance($formdata->coursemodule));
    return $assignment->update_instance($formdata);
}

/**
 * Removes an instance of the newmodule from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function newassignment_delete_instance($id) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/newassignment/locallib.php');
    $cm = get_coursemodule_from_instance('newassignment', $id, 0, false, MUST_EXIST);

    $assignment = new NewAssignment(context_module::instance($cm->id));
    return $assignment->delete_instance();
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function newassignment_user_outline($course, $user, $mod, $newmodule) {
    global $CFG;

    require_once($CFG->dirroot . '/mod/newassignment/locallib.php');
    require_once($CFG->libdir . '/gradelib.php');
    require_once($CFG->dirroot . '/grade/grading/lib.php');
    
    $context = context_module::instance($mod->id);
    $assignment = new NewAssignment($context, $mod, $course);
    $gradinginfo = grade_get_grades($course->id, 'mod', 'newassignment', $assignment->get_instance()->id, $user->id);

    $gradingitem = $gradinginfo->items[0];
    $gradebookgrade = $gradingitem->grades[$user->id];

    if (!$gradebookgrade) {
        return null;
    }
    $result = new stdClass();
    $result->info = get_string('outlinegrade', 'newassignment', $gradebookgrade->grade);
    $result->time = $gradebookgrade->dategraded;

    return $result;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $newmodule the module instance record
 * @return void, is supposed to echp directly
 */
function newassignment_user_complete($course, $user, $mod, $newmodule) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/newassignment/locallib.php');
    $context = context_module::instance($mod->id);

    $assignment = new NewAssignment($context, $mod, $course);

    echo $assignment->view_student_summary($user, false);
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in newmodule activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function newassignment_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG, $USER, $DB, $OUTPUT;

    // do not use log table if possible, it may be huge
	$namefields = user_picture::fields('u', null, 'userid');
    if (!$submissions = $DB->get_records_sql("SELECT asb.id, asb.timemodified, cm.id AS cmid, asb.userid,
                                                     $namefields
                                                FROM {newassign_submissions} asb
                                                     JOIN {newassignment} a      ON a.id = asb.assignment
                                                     JOIN {course_modules} cm ON cm.instance = a.id
                                                     JOIN {modules} md        ON md.id = cm.module
                                                     JOIN {user} u            ON u.id = asb.userid
                                               WHERE asb.timemodified > ? AND
                                                     a.course = ? AND
                                                     md.name = 'newassignment'
                                            ORDER BY asb.timemodified ASC", array($timestart, $course->id))) {
        return false;
    }

    $modinfo = get_fast_modinfo($course); // no need pass this by reference as the return object already being cached
    $show = array();
    $grader = array();

    $showrecentsubmissions = get_config('mod_newassignment', 'showrecentsubmissions');

    foreach ($submissions as $submission) {
        if (!array_key_exists($submission->cmid, $modinfo->get_cms())) {
            continue;
        }
        $cm = $modinfo->get_cm($submission->cmid);
        if (!$cm->uservisible) {
            continue;
        }
        if ($submission->userid == $USER->id) {
            $show[] = $submission;
            continue;
        }

        $context = context_module::instance($submission->cmid);
        // the act of sumbitting of assignment may be considered private - only graders will see it if specified
        if (empty($showrecentsubmissions)) {
            if (!array_key_exists($cm->id, $grader)) {
                $grader[$cm->id] = has_capability('moodle/grade:viewall', $context);
            }
            if (!$grader[$cm->id]) {
                continue;
            }
        }

        $groupmode = groups_get_activity_groupmode($cm, $course);

        if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
            if (isguestuser()) {
                // shortcut - guest user does not belong into any group
                continue;
            }

            if (is_null($modinfo->get_groups())) {
                $modinfo->groups = groups_get_user_groups($course->id); // load all my groups and cache it in modinfo
            }

            // this will be slow - show only users that share group with me in this cm
            if (empty($modinfo->groups[$cm->id])) {
                continue;
            }
            $usersgroups = groups_get_all_groups($course->id, $submission->userid, $cm->groupingid);
            if (is_array($usersgroups)) {
                $usersgroups = array_keys($usersgroups);
                $intersect = array_intersect($usersgroups, $modinfo->groups[$cm->id]);
                if (empty($intersect)) {
                    continue;
                }
            }
        }
        $show[] = $submission;
    }

    if (empty($show)) {
        return false;
    }

    echo $OUTPUT->heading(get_string('newsubmissions', 'newassignment') . ':', 3);

    foreach ($show as $submission) {
        $cm = $modinfo->get_cm($submission->cmid);
        $link = $CFG->wwwroot . '/mod/newassignment/view.php?id=' . $cm->id;
        print_recent_activity_note($submission->timemodified, $submission, $cm->name, $link, false, $viewfullnames);
    }

    return true;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link newmodule_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function newassignment_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid = 0, $groupid = 0) {
    global $CFG, $COURSE, $USER, $DB;

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = $DB->get_record('course', array('id' => $courseid));
    }

    $modinfo = get_fast_modinfo($course); // no need pass this by reference as the return object already being cached

    $cm = $modinfo->get_cm($cmid);
    $params = array();
    if ($userid) {
        $userselect = "AND u.id = :userid";
        $params['userid'] = $userid;
    } else {
        $userselect = "";
    }

    if ($groupid) {
        $groupselect = "AND gm.groupid = :groupid";
        $groupjoin = "JOIN {groups_members} gm ON  gm.userid=u.id";
        $params['groupid'] = $groupid;
    } else {
        $groupselect = "";
        $groupjoin = "";
    }

    $params['cminstance'] = $cm->instance;
    $params['timestart'] = $timestart;

    $userfields = user_picture::fields('u', null, 'userid');

    if (!$submissions = $DB->get_records_sql("SELECT asb.id, asb.timemodified,
			$userfields
			FROM {newassign_submissions} asb
			JOIN {newassignment} a      ON a.id = asb.assignment
			JOIN {user} u            ON u.id = asb.userid
			$groupjoin
			WHERE asb.timemodified > :timestart AND a.id = :cminstance
			$userselect $groupselect
			ORDER BY asb.timemodified ASC", $params)) {
        return;
    }

    $groupmode = groups_get_activity_groupmode($cm, $course);
    $cm_context = context_module::instance($cm->id);
    $grader = has_capability('moodle/grade:viewall', $cm_context);
    $accessallgroups = has_capability('moodle/site:accessallgroups', $cm_context);
    $viewfullnames = has_capability('moodle/site:viewfullnames', $cm_context);

    if (is_null($modinfo->get_groups())) {
        $modinfo->groups = groups_get_user_groups($course->id); // load all my groups and cache it in modinfo
    }

    $showrecentsubmissions = get_config('mod_newassignment', 'showrecentsubmissions');
    $show = array();
    $usersgroups = groups_get_all_groups($course->id, $USER->id, $cm->groupingid);
    if (is_array($usersgroups)) {
        $usersgroups = array_keys($usersgroups);
    }
    foreach ($submissions as $submission) {
        if ($submission->userid == $USER->id) {
            $show[] = $submission;
            continue;
        }
        // the act of submitting of assignment may be considered private - only graders will see it if specified
        if (empty($showrecentsubmissions)) {
            if (!$grader) {
                continue;
            }
        }

        if ($groupmode == SEPARATEGROUPS and !$accessallgroups) {
            if (isguestuser()) {
                // shortcut - guest user does not belong into any group
                continue;
            }

            // this will be slow - show only users that share group with me in this cm
            if (empty($modinfo->groups[$cm->id])) {
                continue;
            }
            if (is_array($usersgroups)) {
                $intersect = array_intersect($usersgroups, $modinfo->groups[$cm->id]);
                if (empty($intersect)) {
                    continue;
                }
            }
        }
        $show[] = $submission;
    }

    if (empty($show)) {
        return;
    }

    if ($grader) {
        require_once($CFG->libdir . '/gradelib.php');
        $userids = array();
        foreach ($show as $submission) {
            $userids[] = $submission->userid;
        }
        $grades = grade_get_grades($courseid, 'mod', 'newassignment', $cm->instance, $userids);
    }

    $aname = format_string($cm->name, true);
    foreach ($show as $submission) {
        $activity = new stdClass();

        $activity->type = 'newassignment';
        $activity->cmid = $cm->id;
        $activity->name = $aname;
        $activity->sectionnum = $cm->sectionnum;
        $activity->timestamp = $submission->timemodified;
        $activity->user = new stdClass();
        if ($grader) {
            $activity->grade = $grades->items[0]->grades[$submission->userid]->str_long_grade;
        }

        $userfields = explode(',', user_picture::fields());
        foreach ($userfields as $userfield) {
            if ($userfield == 'id') {
                $activity->user->{$userfield} = $submission->userid; // aliased in SQL above
            } else {
                $activity->user->{$userfield} = $submission->{$userfield};
            }
        }
        $activity->user->fullname = fullname($submission, $viewfullnames);

        $activities[$index++] = $activity;
    }

    return;
}

function newassignment_get_view_actions() {
    return array('view submission', 'view feedback');
}

/**
 * Prints single activity item prepared by {@see newmodule_get_recent_mod_activity()}

 * @return void
 */
function newassignment_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
    global $CFG, $OUTPUT;

    echo '<table border="0" cellpadding="3" cellspacing="0" class="assignment-recent">';

    echo "<tr><td class=\"userpicture\" valign=\"top\">";
    echo $OUTPUT->user_picture($activity->user);
    echo "</td><td>";

    if ($detail) {
        $modname = $modnames[$activity->type];
        echo '<div class="title">';
        echo "<img src=\"" . $OUTPUT->pix_url('icon', 'newassignment') . "\" " .
        "class=\"icon\" alt=\"$modname\">";
        echo "<a href=\"$CFG->wwwroot/mod/newassignment/view.php?id={$activity->cmid}\">{$activity->name}</a>";
        echo '</div>';
    }

    if (isset($activity->grade)) {
        echo '<div class="grade">';
        echo get_string('grade') . ': ';
        echo $activity->grade;
        echo '</div>';
    }

    echo '<div class="user">';
    echo "<a href=\"$CFG->wwwroot/user/view.php?id={$activity->user->id}&amp;course=$courseid\">"
    . "{$activity->user->fullname}</a>  - " . userdate($activity->timestamp);
    echo '</div>';

    echo "</td></tr></table>";
}

/**
 * Add a get_coursemodule_info function in case any assignment type wants to add 'extra' information
 * for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info An object on information that the courses will know about (most noticeably, an icon).
 */
function newassignment_get_coursemodule_info($coursemodule) {
    global $DB;

    if (!$assignment = $DB->get_record('newassignment', array('id' => $coursemodule->instance), 'id, name, alwaysshowdescription, allowsubmissionsfromdate, intro, introformat')) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $assignment->name;
    if ($coursemodule->showdescription) {
        if ($assignment->alwaysshowdescription || time() > $assignment->allowsubmissionsfromdate) {
            // Convert intro to html. Do not filter cached version, filters run at display time.
            $result->content = format_module_intro('newassignment', $assignment, $coursemodule->id, false);
        }
    }
    return $result;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function newassignment_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array(
        'mod-newassignment-*' => get_string('page-mod-newassignment-x', 'newassignment'),
        'mod-newassignment-view' => get_string('page-mod-newassignment-view', 'newassignment'),
    );
    return $module_pagetype;
}

/**
 * Print an overview of all assignments
 * for the courses.
 *
 * @param mixed $courses The list of courses to print the overview for
 * @param array $htmlarray The array of html to return
 */
function newassignment_print_overview($courses, &$htmlarray) {
    global $USER, $CFG, $DB;

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }

    if (!$assignments = get_all_instances_in_courses('newassignment', $courses)) {
        return;
    }

    $assignmentids = array();

    // Do assignment_base::isopen() here without loading the whole thing for speed
    foreach ($assignments as $assignment) {
        $time = time();
        $isopen = false;
        if ($assignment->duedate) {
            $isopen = $assignment->allowsubmissionsfromdate <= $time;
            if ($assignment->preventlatesubmissions) {
                $isopen = ($isopen && $time <= $assignment->duedate);
            }
        }
        if ($isopen) {
            $assignmentids[] = $assignment->id;
        }
    }

    if (empty($assignmentids)) {
        // no assignments to look at - we're done
        return true;
    }

    $strduedate = get_string('duedate', 'newassignment');
    $strduedateno = get_string('duedateno', 'newassignment');
    $strnotsubmittedyet = get_string('notsubmittedyet', 'newassignment');
    $strassignment = get_string('modulename', 'newassignment');


    // NOTE: we do all possible database work here *outside* of the loop to ensure this scales
    //
	list($sqlassignmentids, $assignmentidparams) = $DB->get_in_or_equal($assignmentids);

    // build up and array of unmarked submissions indexed by assignment id/ userid
    // for use where the user has grading rights on assignment
    $rs = $DB->get_recordset_sql("SELECT s.assignment as assignment, s.userid as userid, s.id as id, g.timemodified as timegraded
			FROM (SELECT ss.id, ss.userid, ss.assignment, ss.timecreated, ss.timemodified FROM (SELECT userid, MAX(version) AS maxversion, assignment FROM {newassign_submissions} GROUP BY assignment, userid) x INNER JOIN {newassign_submissions} ss ON x.userid = ss.userid AND ss.version = x.maxversion AND ss.assignment = x.assignment) s 
			LEFT JOIN {newassign_grades} g ON s.userid = g.userid and s.assignment = g.assignment
			WHERE g.timemodified = 0 OR s.timemodified > g.timemodified
			AND s.assignment $sqlassignmentids", $assignmentidparams);

    $unmarkedsubmissions = array();
    foreach ($rs as $rd) {
        $unmarkedsubmissions[$rd->assignment][$rd->userid] = $rd->id;
    }
    $rs->close();

    // get all user submissions, indexed by assignment id
    $mysubmissions = $DB->get_records_sql("SELECT a.id AS assignment, g.timemodified AS timemarked, g.grader AS grader, g.grade AS grade
			FROM {newassignment} a LEFT JOIN (SELECT ss.id, ss.userid, ss.assignment, ss.timecreated, ss.timemodified FROM (SELECT userid, MAX(version) AS maxversion, assignment FROM {newassign_submissions} GROUP BY assignment, userid) x INNER JOIN {newassign_submissions} ss ON x.userid = ss.userid AND ss.version = x.maxversion AND ss.assignment = x.assignment) s ON s.assignment = a.id AND s.userid = ? LEFT JOIN {newassign_grades} g ON g.submission = s.id
			AND a.id $sqlassignmentids", array_merge(array($USER->id), $assignmentidparams));

    foreach ($assignments as $assignment) {
        // Do not show assignments that are not open
        if (!in_array($assignment->id, $assignmentids)) {
            continue;
        }
        $str = '<div class="newassignment overview"><div class="name">' . $strassignment . ': ' .
                '<a ' . ($assignment->visible ? '' : ' class="dimmed"') .
                'title="' . $strassignment . '" href="' . $CFG->wwwroot .
                '/mod/newassignment/view.php?id=' . $assignment->coursemodule . '">' .
                format_string($assignment->name) . '</a></div>';
        if ($assignment->duedate) {
            $str .= '<div class="info">' . $strduedate . ': ' . userdate($assignment->duedate) . '</div>';
        } else {
            $str .= '<div class="info">' . $strduedateno . '</div>';
        }
        $context = context_module::instance($assignment->coursemodule);
        if (has_capability('mod/newassignment:grade', $context)) {

            // count how many people can submit
            $submissions = 0; // init
            if ($students = get_enrolled_users($context, 'mod/newassignment:view', 0, 'u.id')) {
                foreach ($students as $student) {
                    if (isset($unmarkedsubmissions[$assignment->id][$student->id])) {
                        $submissions++;
                    }
                }
            }

            if ($submissions) {
                $link = new moodle_url('/mod/newassignment/view.php', array('id' => $assignment->coursemodule, 'action' => 'grading'));
                $str .= '<div class="details"><a href="' . $link . '">' . get_string('submissionsnotgraded', 'newassignment', $submissions) . '</a></div>';
            }
        } if (has_capability('mod/newassignment:submit', $context)) {
            $str .= '<div class="details">';
            $str .= get_string('mysubmission', 'newassignment');
            $submission = $mysubmissions[$assignment->id];
            if (!isset($submission->id)) {
                $str .= $strnotsubmittedyet;
            } else {
                $str .= get_string('submissionstatus_submitted', 'newassignment');
            }
            if (!$submission->grade || $submission->grade < 0) {
                $str .= ', ' . get_string('notgraded', 'newassignment');
            } else {
                $str .= ', ' . get_string('graded', 'newassignment');
            }
            $str .= '</div>';
        }
        $str .= '</div>';
        if (empty($htmlarray[$assignment->course]['newassignment'])) {
            $htmlarray[$assignment->course]['newassignment'] = $str;
        } else {
            $htmlarray[$assignment->course]['newassignment'] .= $str;
        }
    }
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 * */
function newassignment_cron() {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function newassignment_get_extra_capabilities() {
    return array('gradereport/grader:view', 'moodle/grade:viewall', 'moodle/site:viewfullnames', 'moodle/site:config');
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of newmodule?
 *
 * This function returns if a scale is being used by one newmodule
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $newmoduleid ID of an instance of this module
 * @return bool true if the scale is used by the given newmodule instance
 */
function newassignment_scale_used($newmoduleid, $scaleid) {
    global $DB;

    $return = false;
    $rec = $DB->get_record('newassignment', array('id' => $assignmentid, 'grade' => -$scaleid));

    if (!empty($rec) && !empty($scaleid)) {
        $return = true;
    }

    return $return;
}

/**
 * Checks if scale is being used by any instance of newmodule.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any newmodule instance
 */
function newassignment_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('newassignment', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the give newmodule instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $newmodule instance object with extra cmidnumber and modname property
 * @return void
 */
function newassignment_grade_item_update($assignment, $grades = NULL) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    $params = array('itemname' => $assignment->name, 'idnumber' => $assignment->cmidnumber);

    if ($assignment->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax'] = $assignment->grade;
        $params['grademin'] = 0;
    } else if ($assignment->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid'] = -$assignment->grade;
    } else {
        $params['gradetype'] = GRADE_TYPE_TEXT; // allow text comments only
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }

    return grade_update('mod/newassignment', $assignment->course, 'mod', 'newassignment', $assignment->id, 0, $grades, $params);
}

/**
 * Update newmodule grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $newmodule instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function newassignment_update_grades(stdClass $assignment, $userid = 0) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/newassignment', $assignment->course, 'mod', 'newassignment', $assignment->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function newassignment_get_file_areas($course, $cm, $context) {
    return array(
        'newassignsubmission_files' => get_string('submissionfile', 'newassignment'),
        'newassignfeedback_files' => get_string('feedbackfile', 'newassignment'));
}

/**
 * File browsing support for newmodule file areas
 *
 * @package mod_newmodule
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function newassignment_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/newassignment/locallib.php');

    if ($context->contextlevel != CONTEXT_MODULE) {
        return null;
    }

    $filepath = is_null($filepath) ? '/' : $filepath;
    $filename = is_null($filename) ? '.' : $filename;

    // need to find the plugin this belongs to
    $assignment = new NewAssignment($context, $cm, $course);
    $plugin = null;
    if ($filearea == 'newassignsubmission_files') {
        require_once($CFG->dirroot . '/mod/newassignment/submissions/file.php');
        $plugin = new mod_newassignment_submission_file($assignment);
    } elseif ($filearea == 'newassignfeedback_files') {
        require_once($CFG->dirroot . '/mod/newassignment/feedbacks/file.php');
        $plugin = new mod_newassignment_feedback_file($assignment);
    } else
        return null;

    return $plugin->get_file_info($browser, $filearea, $itemid, $filepath, $filename);
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the settings navigation with the newmodule settings
 *
 * This function is called when the context for the page is a newmodule module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $newmodulenode {@link navigation_node}
 */
function newassignment_extend_settings_navigation(settings_navigation $settings, navigation_node $navref = null) {

    global $PAGE;

    $cm = $PAGE->cm;
    if (!$cm) {
        return;
    }

    $context = $cm->context;
    $course = $PAGE->course;


    if (!$course) {
        return;
    }


    // Link to gradebook
    if (has_capability('gradereport/grader:view', $cm->context) && has_capability('moodle/grade:viewall', $cm->context)) {
        $link = new moodle_url('/grade/report/grader/index.php', array('id' => $course->id));
        $navref->add(get_string('viewgradebook', 'newassignment'), $link, navigation_node::TYPE_SETTING);
    }

    // Link to download all submissions
    if (has_capability('mod/newassignment:grade', $context)) {
        $link = new moodle_url('/mod/newassignment/view.php', array('id' => $cm->id, 'action' => 'grading'));
        $navref->add(get_string('viewgrading', 'newassignment'), $link, navigation_node::TYPE_SETTING);

        $link = new moodle_url('/mod/newassignment/view.php', array('id' => $cm->id, 'action' => 'downloadnotgraded'));
        $navref->add(get_string('downloadnotgraded', 'newassignment'), $link, navigation_node::TYPE_SETTING);

        $link = new moodle_url('/mod/newassignment/view.php', array('id' => $cm->id, 'action' => 'downloadactualversions'));
        $navref->add(get_string('downloadactualversions', 'newassignment'), $link, navigation_node::TYPE_SETTING);

        $link = new moodle_url('/mod/newassignment/view.php', array('id' => $cm->id, 'action' => 'downloadall'));
        $navref->add(get_string('downloadall', 'newassignment'), $link, navigation_node::TYPE_SETTING);
    }
}

//COMMENTS

/**
 *
 * Callback method for data validation---- required method for AJAXmoodle based comment API
 *
 * @param stdClass $options
 * @return bool
 */
function newassignment_comment_validate(stdClass $options) {

    return true;
}

/**
 * Permission control method for submission plugin ---- required method for AJAXmoodle based comment API
 *
 * @param stdClass $options
 * @return array
 */
function newassignment_comment_permissions(stdClass $options) {

    return array('post' => true, 'view' => true);
}

/**
 * Callback to force the userid for all comments to be the userid of the submission and NOT the global $USER->id. This
 * is required by the upgrade code. Note the comment area is used to identify upgrades.
 *
 * @param stdClass $comment
 * @param stdClass $param
 */
function newassignment_comment_add(stdClass $comment, stdClass $param) {

    global $DB;
    if ($comment->commentarea == 'submission_comments_upgrade') {
        $submissionid = $comment->itemid;
        $submission = $DB->get_record('newassign_submissions', array('id' => $submissionid));

        $comment->userid = $submission->userid;
        $comment->commentarea = 'submission_comments';
    }
}

/**
 * Serves assignment submissions and other files.
 *
 * @param mixed $course course or id of the course
 * @param mixed $cm course module or id of the course module
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - just send the file
 */
function mod_newassignment_pluginfile($course, $cm, context $context, $filearea, $args, $forcedownload) {
    global $USER, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, false, $cm);
    $itemid = (int) array_shift($args);
    $record = null;
    if ($filearea == 'newassignsubmission_files' || $filearea == 'newassignsubmission_onlinetext')
        $record = $DB->get_record('newassign_submissions', array('id' => $itemid), 'userid, assignment', MUST_EXIST);
    if ($filearea == 'newassignfeedback_files')
        $record = $DB->get_record('newassign_feedbacks', array('id' => $itemid), 'userid, assignment', MUST_EXIST);
    $userid = $record->userid;

    if (!$assignment = $DB->get_record('newassignment', array('id' => $cm->instance))) {
        return false;
    }

    if ($assignment->id != $record->assignment) {
        return false;
    }

    // check is users submission or has grading permission
    if ($USER->id != $userid && !(has_capability('mod/newassignment:grade', $context) || has_capability('mod/newassignment:submit', $context))) {
        return false;
    }



    $relativepath = implode('/', $args);

    $fullpath = "/{$context->id}/mod_newassignment/$filearea/$itemid/$relativepath";

    $fs = get_file_storage();

    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }
    send_stored_file($file, 0, 0, true); // download MUST be forced - security!
}

/**
 * Obtains the automatic completion state for this forum based on any conditions
 * in forum settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function newassignment_get_completion_state($course, $cm, $userid, $type) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/newassignment/locallib.php');
    // Get forum details
    $assignment = new NewAssignment(null, $cm);
    if (!($assignment->get_instance())) {
        throw new Exception("Can't find new assignment {$cm->instance}");
    }

    // If completion option is enabled, evaluate it and return true/false
    if ($assignment->get_instance()->newassigncompletition == 1) {
        $return = $assignment->check_completition($userid);
        return $return;
    } else {
        // Completion option is not enabled so just return $type
        return $type;
    }
}


////////////////////////////////////////////////////////////////////////////////
// Reset API                                                                  //
////////////////////////////////////////////////////////////////////////////////

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all assignment submissions and feedbacks in the database
 * and clean up any related data.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function newassignment_reset_userdata($data) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/newassignment/locallib.php');

    $status = array();
    $params = array('courseid'=>$data->courseid);
    $sql = "SELECT a.id FROM {newassignment} a WHERE a.course=:courseid";
    $course = $DB->get_record('course', array('id'=> $data->courseid), '*', MUST_EXIST);
    if ($assigns = $DB->get_records_sql($sql,$params)) {
        foreach ($assigns as $assign) {
            $cm = get_coursemodule_from_instance('newassignment', $assign->id, $data->courseid, false, MUST_EXIST);
            $context = context_module::instance($cm->id);
            $assignment = new NewAssignment($context, $cm, $course);
            $status = array_merge($status, $assignment->reset_userdata($data));
        }
    }
    return $status;
}

/**
 * Removes all grades from gradebook
 *
 * @param int $courseid The ID of the course to reset
 * @param string $type Optional type of assignment to limit the reset to a particular assignment type
 */
function newassignment_reset_gradebook($courseid, $type='') {
    global $DB;

    $params = array('moduletype'=>'newassignment','courseid'=>$courseid);
    $sql = 'SELECT a.*, cm.idnumber as cmidnumber, a.course as courseid
            FROM {newassignment} a, {course_modules} cm, {modules} m
            WHERE m.name=:moduletype AND m.id=cm.module AND cm.instance=a.id AND a.course=:courseid';

    if ($assignments = $DB->get_records_sql($sql,$params)) {
        foreach ($assignments as $assignment) {
            newassignment_grade_item_update($assignment, 'reset');
        }
    }
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the assignment.
 * @param $mform form passed by reference
 */
function newassignment_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'newassignmentheader', get_string('modulenameplural', 'newassignment'));
    $mform->addElement('advcheckbox', 'reset_newassignment_submissions', get_string('deleteallsubmissions','newassignment'));
}

/**
 * Course reset form defaults.
 * @param  object $course
 * @return array
 */
function newassignment_reset_course_form_defaults($course) {
    return array('reset_newassignment_submissions'=>1);
}

