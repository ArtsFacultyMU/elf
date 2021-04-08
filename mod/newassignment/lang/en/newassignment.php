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
 * English strings for newmodule
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage newmodule
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actualversion'] = 'Recent version';
$string['addsubmission'] = 'Add submission';
$string['addnextsubmission'] = 'Add next submission';
$string['afterachievement'] = 'After acceptance';
$string['aftersubmission'] = 'After submission';
$string['allowsubmissionsfromdate'] = 'Allow submissions from';
$string['allowsubmissionsfromdate_help'] = 'If enabled, students will not be able to submit before this date. If disabled, students will be able to start submitting right away.';
$string['allowsubmissionsfromdatesummary'] = 'This assignment will accept submissions from <strong>{$a}</strong>';
$string['allowsubmissionsshort'] = 'Allow submission changes';
$string['alwaysshowdescription'] = 'Always show description';
$string['alwaysshowdescription_help'] = 'If disabled, the Assignment Description above will only become visible to students at the "Allow submissions from" date.';
$string['assignmentisdue'] = 'Assignment is due';
$string['assignmentname'] = "Assignment name";
$string['assignmentsperpage'] = 'Assignments per page';
$string['batchoperationsdescription'] = 'With selected...';
$string['batchoperationconfirmlock'] = 'Lock all selected submissions?';
$string['batchoperationconfirmunlock'] = 'Unlock all selected submissions?';
$string['batchoperationconfirmreverttodraft'] = 'Revert selected submissions to draft?';
$string['batchoperationlock'] = 'lock submissions';
$string['batchoperationunlock'] = 'unlock submissions';
$string['batchoperationreverttodraft'] = 'revert submissions to draft';
$string['completition'] = 'ELF completition';
$string['completition_help'] = 'ELF completition help';
$string['completition_desc'] = 'ELF completition description';
$string['confirmsubmission'] = 'Are you absolutely sure you want to submit your work for grading? After submission, you will not be able to make any more changes to the current version.';
$string['confirmsubmissionok'] = 'Confirm submission';
$string['confirmsubmissioncancel'] = 'Back';
$string['configshowrecentsubmissions'] = 'Everyone can see notifications of submissions in recent activity reports.';
$string['currentgrade'] = 'Current grade in gradebook';
$string['deleteallsubmissions'] = 'Delete all submissions';
$string['description'] = "Description";
$string['downloadactualversions'] = 'Download recent submissions';
$string['downloadall'] = 'Download all submissions';
$string['downloadnotgraded'] = 'Download ungraded submissions';
$string['duedate'] = 'Due date';
$string['duedateno'] = 'No due date';
$string['duedate_help'] = 'This is when the assignment is due. If late submissions are allowed, any assignments submitted after this date are marked as late.';
$string['duedatereached'] = 'The due date for this assignment has now passed';
$string['finalgrade'] = 'Final grade';
$string['gradeaverage'] = 'Average grade';
$string['gradehighest'] = 'Highest grade';
$string['gradefirst'] = 'First submission';
$string['gradelast'] = 'Last submission';
$string['gradedby'] = 'Graded by';
$string['graded'] = 'Graded';
$string['gradedon'] = 'Graded on';
$string['grademethod'] = 'Grading method';
$string['grademethod_help'] = 'When multiple attempts are allowed, the following methods are available for calculating the final quiz grade:

* Highest grade of all attempts
* Average (mean) grade of all attempts
* First attempt (all other attempts are ignored)
* Last attempt (all other attempts are ignored)';
$string['gradersubmissionupdatedtext'] = '{$a->username} has updated their assignment submission
for \'{$a->assignment}\' at {$a->timeupdated}

It is available here:

    {$a->url}';
$string['gradersubmissionupdatedhtml'] = '{$a->username} has updated their assignment submission
for <i>\'{$a->assignment}\'  at {$a->timeupdated}</i><br /><br />
It is <a href="{$a->url}">available on the web site</a>.';
$string['gradersubmissionupdatedsmall'] = '{$a->username} has updated their submission for assignment {$a->assignment}.';
$string['gradestudent'] = 'Grade student: (id={$a->id}, fullname={$a->fullname}). ';
$string['grading'] = 'Grading';
$string['gradingoptions'] = 'Options';
$string['gradingstatus'] = 'Grading status';
$string['maxfilessubmission'] = 'Maximum number of uploaded files';
$string['maxfilessubmission_help'] = 'If file submissions are enabled, each student will be able to upload up to this number of files for their submission.';
$string['modulename'] = 'New Assignment';
$string['modulenameplural'] = "New Assignments";
$string['modulename_help'] = "Newassignment description ;)";
$string['mysubmission'] = 'My submission: ';
$string['nosavebutnext'] = 'Next';
$string['nosavebutprevious'] = 'Previous';
$string['nosubmission'] = 'Nothing has been submitted for this assignment';
$string['notgraded'] = 'Not graded';
$string['numberofdraftsubmissions'] = 'Drafts';
$string['numberofparticipants'] = 'Number of participants';
$string['numberofsubmittedassignments'] = 'Submitted';
$string['onlinetextsubmission'] = 'Online Text';
$string['onlinetextfilename'] = 'OnlineText';
$string['onlyfiles'] = 'Only files';
$string['editaction'] = 'Actions...';
$string['editsubmission'] = 'Edit my submission';
$string['error_feedbackstatus'] = 'You need to select an assignment STATUS for the students highlighted in red; otherwise no changes can be saved in the grading table.';
$string['feedback'] = 'Feedback';
$string['feedbackcomment'] = 'Feedback comment';
$string['feedbackfile'] = 'Feedback file';
$string['feedbackstatus'] = 'Assignment status';
$string['feedbackstatus_accepted'] = 'Accepted';
$string['feedbackstatus_declined'] = 'Rejected';
$string['filesandcomments'] = 'Files and comments';
$string['filesubmission'] = 'File';
$string['filter'] = 'Filter';
$string['filternone'] = 'No filter';
$string['filterrequiregrading'] = 'Requires grading';
$string['filtersubmitted'] = 'Submitted';
$string['gradeoutofhelp'] = 'Grade';
$string['gradeoutofhelp_help'] = 'Enter the grade for the student\'s submission here. You may include decimals.';
$string['gradingstudentprogress'] = 'Grading student {$a->index} of {$a->count}';
$string['gradingsummary'] = 'Grading summary';
$string['lastmodifiedsubmission'] = 'Last modified (submission)';
$string['lastmodifiedgrade'] = 'Last modified (grade)';
$string['locksubmissions'] = 'Lock submissions';
$string['messageprovider:newassignment_notification'] = 'New Assignment notifications';
$string['newassignment:addinstance'] = 'Add a new assignment';
$string['newassignment:exportownsubmission'] = 'Export own submission';
$string['newassignment:grade'] = 'Grade assignment';
$string['newassignment:submit'] = 'Submit assignment';
$string['newassignment:view'] = 'View assignment';
$string['newsubmissions'] = 'New assignment submissions';
$string['nousersselected'] = 'No users selected';
$string['nograde'] = 'No grade. ';
$string['numwords'] = '({$a} words)';
$string['notgradedyet'] = 'Not graded yet';
$string['notsubmitted'] = 'Submission haven\'t been submitted';
$string['notsubmittedyet'] = 'Not submitted yet';
$string['overdue'] = '<font color="red">Assignment is overdue by: {$a}</font>';
$string['pluginadministration'] = 'New Assignment settings';
$string['pluginname'] = 'New Assignment';
$string['preventlatesubmissions'] = 'Prevent late submissions';
$string['preventlatesubmissions_help'] = 'If enabled, students will not be able submit after the Due Date. If disabled, students will be able to submit assignments after the due date.';
$string['preventsubmissionsshort'] = 'Prevent submission changes';
$string['previous'] = 'Previous';
$string['publish'] = 'Publication settings';
$string['publishsubpage'] = 'Overview of all submissions';
$string['publishaftersubmission'] = 'After submission';
$string['publishafterduedate'] = 'After due date';
$string['publishtime'] = 'Publication time';
$string['publishtime_help'] = 'Help';
$string['publishnow'] = 'Anytime';
$string['publishfeedbacks'] = 'Publish feedbacks';
$string['publishfeedbacks_help'] = 'Publish feedbacks';
$string['publishfeedbacksanonymously'] = 'Publish feedbacks anonymously';
$string['publishfeedbacksanonymously_help'] = 'Publish feedbacks anonymously help';
$string['publishsubmissions'] = 'Publish submissions';
$string['publishsubmissions_help'] = 'Publish submissions';
$string['publishsubmissionsanonymously'] = 'Publish submissions anonymously';
$string['publishsubmissionsanonymously_help'] = 'Publish submissions anonymously help';
$string['quickgrading'] = 'Quick grading';
$string['quickgradingresult'] = 'Quick grading';
$string['quickgradingchangessaved'] = 'The grade changes were saved';
$string['quickgrading_help'] = 'Quick grading allows you to assign grades (and outcomes) directly in the submissions table. Quick grading is not compatible with advanced grading and is not recommended when there are multiple markers.';
$string['reviewed'] = 'Reviewed';
$string['recentgrade'] = 'Recent grade';
$string['saveallquickgradingchanges'] = 'Save all quick grading changes';
$string['savechanges'] = 'Save changes';
$string['savenext'] = 'Save and show next';
$string['saveprevious'] = 'Save and show previous';
$string['sendnotifications'] = 'Notify graders about submissions';
$string['sendnotifications_help'] = 'If enabled, graders (usually teachers) receive a message whenever a student submits an assignment, early, on time and late. Message methods are configurable.';
$string['sendlatenotifications'] = 'Notify graders about late submissions';
$string['sendlatenotifications_help'] = 'If enabled, graders (usually teachers) receive a message whenever a student submits an assignment late. Message methods are configurable.';
$string['sendsubmissionreceipts'] = 'Send submission receipt to students';
$string['sendsubmissionreceipts_help'] = 'This switch will enable submission receipts for students. Students will receive a notification every time they successfully submit an assignment';
$string['settings'] = 'Assignment settings';
$string['showallversions'] = 'Show all versions';
$string['showfull'] = 'Show full';
$string['showotherstudentssubmissions'] = 'Show other students\' submissions';
$string['showrecentsubmissions'] = 'Show recent submissions';
$string['student'] = 'Student';
$string['studentsallversions'] = 'Student\'s submission\'s versions';
$string['submission'] = 'Submission';
$string['submissionsclosed'] = 'Submission are closed';
$string['submissioncomments'] = 'Submission comments';
$string['submissioncomments_help'] = 'Submission comments help';
$string['submissiondrafts'] = 'Require students click submit button';
$string['submissiondrafts_help'] = 'If enabled, students will have to click a Submit button to declare their submission as final. This allows students to keep a draft version of the submission on the system.';
$string['submissionfile'] = 'File submission';
$string['submissionmaxfilesize'] = 'Maximum submission size';
$string['submissionmaxfilesize_help'] = 'Files uploaded by students may be up to this size.';
$string['submissiononlinetext'] = 'Online text submission';
$string['submissionsnotgraded'] = 'Submissions not graded: {$a}';
$string['submissionreceipttext'] = 'You have submitted an
assignment submission for \'{$a->assignment}\'

You can see the status of your assignment submission:

    {$a->url}';
$string['submissionreceipthtml'] = 'You have submitted an
assignment submission for \'<i>{$a->assignment}</i>\'<br /><br />
You can see the status of your <a href="{$a->url}">assignment submission</a>.';
$string['submissionreceiptsmall'] = 'You have submitted your assignment submission for {$a->assignment}';
$string['submissions'] = 'Submissions';
$string['submissionsettings'] = "Submission settings";
$string['submissionslocked'] = 'This assignment is not accepting submissions';
$string['submissionslockedshort'] = 'Submission changes not allowed';
$string['submissionstatusheading'] = 'Submission status';
$string['submissionstatus_marked'] = 'Graded';
$string['submissionstatus_new'] = 'New submission';
$string['submissionstatus_submitted'] = 'Submitted for grading';
$string['submissionstatus_'] = 'No submission';
$string['submissionstatus'] = 'Submission status';
$string['submissiontype'] = "Submission type";
$string['submissiontype_help'] = "Submission type help";
$string['submissionthanks'] = 'Thank you for your submission.';
$string['submissionversion'] = 'Version';
$string['submitassignment_help'] = 'Once this assignment is submitted you will not be able to make any more changes';
$string['submitassignment'] = 'Submit assignment';
$string['submitted'] = 'Submitted';
$string['submittedlateshort'] = '{$a} late';
$string['submittedearly'] = 'Assignment was submitted {$a} early';
$string['submittedlate'] = 'Assignment was submitted {$a} late';
$string['timemodified'] = 'Last modified';
$string['timeremaining'] = 'Time remaining';
$string['timesubmitted'] = 'Submitted';
$string['unlocksubmissions'] = 'Unlock submissions';
$string['updatetable'] = 'Update table';
$string['viewgradebook'] = 'View gradebook';
$string['viewgrading'] = 'View grading';
$string['viewgradingformforstudent'] = 'View grading page for student: (id={$a->id}, fullname={$a->fullname}).';
$string['viewownsubmissionform'] = 'View own submit assignment page.';
$string['viewownsubmissionstatus'] = 'View own submission status page.';
$string['viewsubmissionforuser'] = 'View submission for user: {$a}';
$string['viewsubmissiongradingtable'] = 'View submission grading table.';