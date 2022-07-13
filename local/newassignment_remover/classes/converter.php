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

namespace local_newassignment_remover;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/mod/assign/mod_form.php');

/**
 * Creation of the Assignment instances based on the New Assignment ones.
 *
 * @package    local_newassignment_remover
 * @copyright  2022 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 */
class converter {
    public static function convert($course, $newassignment) {
        // Prepare addition of a new mod_assign instance in the corresponding course section.
        list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, 'assign', (int)$newassignment->section);

        // Convert original mod_newassignment data as close as possible,
        $data = self::_mapDataToAssign($course, $newassignment, $module, $data, $context);
        
        // Create new mod_assign instance with given data.
        $assign = self::_createAssign($course, $data, $cw, $cm);

        // If the advanced grading is used, use the same values as the original instance.
        if (in_array($data->advancedgradingmethod_submissions, ['guide', 'rubric'])) {
            self::_mapAdvancedGrading($newassignment, $assign, $data->advancedgradingmethod_submissions);
        }

        // Move new mod_assign instance to the correct position in the course.
        self::_positionAssign($newassignment, $assign);
    }

    protected static function _mapDataToAssign($course, $newassignment, $module, $data, $context) {
        global $DB;
        // Get additional data about the original mod_newassignment instance from the other database tables.
        $course_module = $DB->get_record('course_modules', ['id' => $newassignment->coursemodule], '*', MUST_EXIST);
        $grade_items = $DB->get_record('grade_items', [
            'itemtype' => 'mod',
            'itemmodule' => 'newassignment',
            'iteminstance' => $course_module->instance,
        ], '*', MUST_EXIST);
        $context = $DB->get_record('context', [
            'contextlevel' => '70', // Course module context
            'instanceid' => $newassignment->coursemodule
        ], '*', MUST_EXIST);
        $grading_areas = $DB->get_record('grading_areas', [
            'contextid' => $context->id,
        ]);
        // Set the corresponding data.
        /// General.
        $data->name = $newassignment->name;
        $data->introeditor = [
            'text' => $newassignment->intro,
            'format' => $newassignment->introformat,
            'itemid' => -1,
        ];
        $data->showdescription = $course_module->showdescription; // Display description on course page.
        
        /// Availability.
        $data->allowsubmissionsfromdate = $newassignment->allowsubmissionsfromdate; // Allow submissions from.
        $data->duedate = (int)$newassignment->duedate; // Due date
        //// In mod_newassignment is only boolean for preventing late submitions, if true, sets cut-off date to due date.
        $data->cutoffdate = (int)$newassignment->preventlatesubmissions==0 ? 0 : (int)$newassignment->duedate; // Cut-off date.
        $data->gradingduedate = 0; //Upozornění na známkování v kalendáři - v ÚsO není
        $data->alwaysshowdescription = $newassignment->alwaysshowdescription; // Zobrazit popis před časem zadání úkolu

        /// Submission types.
        $data->assignsubmission_file_enabled = (int)($newassignment->submissiontype=='file'); // File submissions.
        $data->assignsubmission_file_maxfiles = $newassignment->submissionmaxfilecount; // Maximum number of uploaded files.
        $data->assignsubmission_file_maxsizebytes = $newassignment->submissionmaxfilesize; // Maximum submission size.
        $data->assignsubmission_file_filetypes = ''; // Accepted file types. The mod_newassignment lacked this option.
        $data->assignsubmission_comments_enabled = 1; // Fixed 1 in the form (i don't know why).
        $data->assignsubmission_onlinetext_enabled = (int)($newassignment->submissiontype=='onlinetext'); // Online text.
        $data->assignsubmission_onlinetext_wordlimit = ''; // Word limit. The mod_newassignment lacked this option.
        $data->assignsubmission_helixassign_enabled = 0;

        /// Feedback types. The mod_newassignment lacked these options so I chose all of them.
        $data->assignfeedback_comments_enabled = '1'; // Feedback comments.
        $data->assignfeedback_offline_enabled = '1'; // Offline grading worksheet.
        $data->assignfeedback_file_enabled = '1'; // Feedback files.
        $data->assignfeedback_comments_commentinline = '0'; // Comment inline. The mod_newassignment lacked this option.
        
        /// Submission settings. IMO there are not these settings in mod_newassignment, set to default.
        $data->submissiondrafts = '0'; // Require students to click the submit button.
        $data->requiresubmissionstatement = '0'; // Require that students accept the submission statement.
        $data->attemptreopenmethod = 'manual'; // Attempts reopened.
        $data->maxattempts = '-1'; // Maximum attempts.

        /// Group submission settings. There is no group submission in mod_newassignment.
        $data->teamsubmission = '0'; // Students submit in groups.
        $data->preventsubmissionnotingroup = '0'; // Require group to make submission.
        $data->requireallteammemberssubmit = '0'; // Require all group members submit.

        /// Notifications.
        $data->sendnotifications = $newassignment->sendnotifications; // Notify graders about submissions.
        $data->sendlatenotifications = $newassignment->sendlatenotifications; // Notify graders about late submissions.
        $data->sendstudentnotifications = '1'; // Default setting for "Notify students". Probably not in mod_newassignment.
        
        /// Grade.
        //// Datamined correct setting in the moodle core.
        if (((int)$grade_items->gradetype === 1)) { // If type is "point" data->grade is a positive maximum value.
            $data->grade = (string)unformat_float($grade_items->grademax);
        } elseif (((int)$grade_items->gradetype === 2)) { // If type is "scale" data->grade is a negative scale ID.
            $data->grade = -1*(int)$grade_items->scaleid;
        } else { // If type is "none" data->grade is null.
            $data->grade = null;
        }
        $data->grade_rescalegrades = null; // ???
        $data->advancedgradingmethod_submissions = $grading_areas ? $grading_areas->activemethod : ''; // Grading method.
        $data->gradecat = $grade_items->categoryid; // Grade category.
        $data->gradepass = $grade_items->gradepass; // Grade category.
        $data->blindmarking = '0'; // Anonymous submissions. Probably not in mod_newassignment.
        $data->hidegrader = '0'; // Hide grader identity from students. Probably not in mod_newassignment.
        $data->markingworkflow = '0'; // Use marking workflow. Probably not in mod_newassignment.
        $data->markingallocation = '0'; //  Use marking allocation. Probably not in mod_newassignment.
        
        /// Common module settings.
        $data->visible = $course_module->visible; // Availability.
        $data->visibleoncoursepage = $course_module->visibleoncoursepage; // ???
        $data->cmidnumber = (string)$course_module->idnumber; // ID number.
        $data->groupmode = $newassignment->groupmode; // Group mode.
        $data->groupingid = $newassignment->groupingid; // Grouping.

        /// Restrict access.
        $data->availabilityconditionsjson = $course_module->availability;
        
        /// Activity completion.
        $data->completionunlocked = 1; // ???
        $data->completion = $course_module->completion; // Completion tracking.
        $data->completionview = $course_module->completionview; // Student must view this activity to complete it.
        $data->completionusegrade = $course_module->completiongradeitemnumber ? '1' : '0'; // Student must receive a grade to complete this activity.
        $data->completionsubmit = 1; // Student must submit to this activity to complete it. Used default value.
        $data->completionexpected = $course_module->completionexpected; // Expect completed on.
        
        /// Tags.
        $data->tags = []; // Tags. WIP

        // Competencies. 
        $data->competencies = []; // Course competencies. WIP
        $data->competency_rule = '0'; // Upon activity completion. WIP
        
        /// Other parameters.
        $data->course = $course->id; // Course ID.
        $data->coursemodule = 0; // Course module ID. Is used only for editation not addition.
        $data->section = (int)$newassignment->section; // Corresponding course section.
        $data->module = $module->id;  // ID of the module type (mod_assign).
        $data->modulename = $module->name; // Name of the module type (mod_assign).
        $data->instance = 0; // Instance ID in the module's table (mod_assign). Adding new instance therefore 0.
        $data->add = 'assign'; // URL parameter.
        $data->update = 0; // URL parameter.
        $data->return = 0; // URL parameter.
        $data->sr = 0; // URL parameter.

        return $data;
    }

    protected static function _createAssign($course, $data, $cw, $cm) {
        // Use form to validate, normalize and get data.
        // (There has to be extension for direct access to the form element.)
        $mform = new class($data, $cw->section, $cm, $course) extends \mod_assign_mod_form {
            protected $_modname = 'assign';
            public function getForm() {return $this->_form;}
        };
        $mform->set_data($data);
        $fromform = (object)$mform->getForm()->exportValues();
        // Create new mod_assign instance based on the data above & store its information for later use.
        return add_moduleinfo($fromform, $course, $mform);
    }

    protected static function _mapAdvancedGrading($newassignment, $assign, $gradingmethod) {
        global $DB;
        
        // Fetch base newassignment data.
        $na_context = $DB->get_record('context', [
            'contextlevel' => '70', // Course module context
            'instanceid' => $newassignment->coursemodule
        ], '*', MUST_EXIST);
        $na_grading_areas = $DB->get_record('grading_areas', [
            'contextid' => $na_context->id,
            'activemethod' => $gradingmethod,
        ], '*', MUST_EXIST);

        // Fetch base assign data.
        $a_context = $DB->get_record('context', [
            'contextlevel' => '70', // Course module context
            'instanceid' => $assign->coursemodule
        ], '*', MUST_EXIST);
        $a_grading_areas = $DB->get_record('grading_areas', [
            'contextid' => $a_context->id,
            'activemethod' => $gradingmethod,
        ], '*', MUST_EXIST);

        foreach ($DB->get_records('grading_definitions', [
                'areaid' => $na_grading_areas->id, 'method' => $gradingmethod]) as $na_definition) {
            $a_definition_id = $DB->insert_record('grading_definitions', (object)[
                'areaid' => $a_grading_areas->id,
                'method' => $gradingmethod,
                'name' => $na_definition->name,
                'description' => $na_definition->description,
                'descriptionformat' => $na_definition->descriptionformat,
                'status' => $na_definition->status,
                'copiedfromid' => $na_definition->copiedfromid,
                'timecreated' => $na_definition->timecreated,
                'usercreated' => $na_definition->usercreated,
                'timemodified' => $na_definition->timemodified,
                'usermodified' => $na_definition->usermodified,
                'timecopied' => $na_definition->timecopied,
                'options' => $na_definition->options,
            ]);

            if ($gradingmethod == 'guide') {
                foreach ($DB->get_records('gradingform_guide_comments', 
                        ['definitionid' => $na_definition->id]) as $na_guide_comment) {
                    $DB->insert_record('gradingform_guide_comments', (object)[
                        'definitionid' => $a_definition_id,
                        'sortorder' => $na_guide_comment->sortorder,
                        'description' => $na_guide_comment->description,
                        'descriptionformat' => $na_guide_comment->descriptionformat,
                    ]);
                }

                foreach ($DB->get_records('gradingform_guide_criteria', 
                        ['definitionid' => $na_definition->id]) as $na_guide_criterion) {
                    $DB->insert_record('gradingform_guide_criteria', (object)[
                        'definitionid' => $a_definition_id,
                        'sortorder' => $na_guide_criterion->sortorder,
                        'shortname' => $na_guide_criterion->shortname,
                        'description' => $na_guide_criterion->description,
                        'descriptionformat' => $na_guide_criterion->descriptionformat,
                        'descriptionmarkers' => $na_guide_criterion->descriptionmarkers,
                        'descriptionmarkersformat' => $na_guide_criterion->descriptionmarkersformat,
                        'maxscore' => $na_guide_criterion->maxscore,
                    ]);
                }
            }

            if ($gradingmethod == 'rubric') {
                foreach ($DB->get_records('gradingform_rubric_criteria', 
                        ['definitionid' => $na_definition->id]) as $na_rubric_criterion) {
                    $a_rubric_criterion_id = $DB->insert_record('gradingform_rubric_criteria', (object)[
                        'definitionid' => $a_definition_id,
                        'sortorder' => $na_rubric_criterion->sortorder,
                        'description' => $na_rubric_criterion->description,
                        'descriptionformat' => $na_rubric_criterion->descriptionformat,
                    ]);

                    foreach ($DB->get_records('gradingform_rubric_levels', 
                            ['criterionid' => $na_rubric_criterion->id]) as $na_rubric_level) {
                        $DB->insert_record('gradingform_rubric_levels', (object)[
                            'criterionid' => $a_rubric_criterion_id,
                            'score' => $na_rubric_level->score,
                            'definition' => $na_rubric_level->definition,
                            'definitionformat' => $na_rubric_level->definitionformat,
                        ]);
                    }
                }
            }
        }

    }

    protected static function _positionAssign($newassignment, $assign) {
        global $DB;

        $section = $DB->get_record('course_sections', 
                ['course' => $assign->course, 'section' => $assign->section], '*', MUST_EXIST);
        if (empty($section->sequence)) {return;}
        $sequence = explode(',', $section->sequence);
        $outputsequence = [];
        foreach ($sequence as $s) {
            $s = (int)trim($s);
            // Skip new instance position as it will be re-added at correct position.
            if ($s === (int)$assign->coursemodule) {continue;}
            // Set new mod_assign instance to be right after corresponding mod_newassignment.
            if ($s === (int)$newassignment->coursemodule) {
                $s .= ',' . (int)$assign->coursemodule; 
            }
            $outputsequence[] = $s;
        }
        $DB->update_record('course_sections', ['id' => $section->id, 'sequence' => implode(',', $outputsequence)]);
    }
}