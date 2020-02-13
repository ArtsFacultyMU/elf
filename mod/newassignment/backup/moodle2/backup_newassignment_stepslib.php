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
 * Define all the backup steps that will be used by the backup_assign_activity_task
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete choice structure for backup, with file and id annotations
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_newassignment_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the structure for the assign activity
     * @return void
     */
    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $newassignment = new backup_nested_element('newassignment', array('id'),
                                            array('name',
                                                  'intro',
                                                  'introformat',
                                                  'alwaysshowdescription',
                                                  'preventlatesubmissions',
                                                  'sendnotifications',
                                                  'submissiondrafts',
                                                  'sendlatenotifications',
                                                  'duedate',
                                                  'allowsubmissionsfromdate',
                                                  'grade',
                                            	  'grademethod',
                                            	  'submissiontype',
                                            	  'submissionmaxfilesize',
                                            	  'submissionmaxfilecount',
                                            	  'submissioncomments',
                                            	  'publishtime',
                                            	  'publishsubmissions',
                                            	  'publishsubmissionsanonymously',
                                            	  'publishfeedbacks',
                                            	  'publishfeedbacksanonymously',
                                            	  'newassigncompletition',
                                                  'timemodified'));

        $submissions = new backup_nested_element('submissions');
        
        $submission = new backup_nested_element('submission', array('id'),
                                                array('userid',
                                                    'version',
                                                    'timecreated',
                                                    'timemodified',
                                                    'status'));
        
        $feedbacks = new backup_nested_element('feedbacks');
        
        $feedback = new backup_nested_element('feedback', array('id','submission'),
        		array('userid',
        				'timecreated',
        				'timemodified',
        				'status'));

        $grades = new backup_nested_element('grades');

        $grade = new backup_nested_element('grade', array('id','submission'),
                                           array('userid',
                                                'timecreated',
                                                'timemodified',
                                                'grader',
                                                'grade'));

        
        $subonlines = new backup_nested_element('onlinetexts');
        $subonline = new backup_nested_element('onlinetext', array('submission','id'), array('text', 'format'));
        
        $feedcomments = new backup_nested_element('feedcomments');
        $feedcomment = new backup_nested_element('feedcomment', array('feedback','id'), array('text', 'format'));
        
        $gradeguidefillings = new backup_nested_element('gradeguidefillings');
        $gradeguidefilling = new backup_nested_element('gradeguidefilling', array('gradeid','criterionid','id'), array('remark', 'remarkformat','score'));
        
        $graderubricfillings = new backup_nested_element('graderubicfillings');
        $graderubricfilling = new backup_nested_element('graderubicfilling', array('gradeid','criterionid','levelid','id'), array('remark', 'remarkformat'));
        
         // Build the tree
        $newassignment->add_child($submissions);
        
        $submissions->add_child($submission);
        
        $submission->add_child($feedbacks);
        $feedbacks->add_child($feedback);
        
        $submission->add_child($grades);
        $grades->add_child($grade);
        
        $grade->add_child($gradeguidefillings);
        $gradeguidefillings->add_child($gradeguidefilling);
        
        $grade->add_child($graderubricfillings);
        $graderubricfillings->add_child($graderubricfilling);
        
        $submission->add_child($subonlines);
        $subonlines->add_child($subonline);
        
        $feedback->add_child($feedcomments); 
        $feedcomments->add_child($feedcomment);
        
        
        // Define sources
        $newassignment->set_source_table('newassignment', array('id' => backup::VAR_ACTIVITYID));

        if ($userinfo) {
            $submission->set_source_table('newassign_submissions',
                                     array('assignment' => backup::VAR_PARENTID));

            $grade->set_source_table('newassign_grades',
                                     array('assignment' => '../../../../id',
                                            'submission' => backup::VAR_PARENTID));
            
            $feedback->set_source_table('newassign_feedbacks',
                                     array('assignment' => '../../../../id',
                                            'submission' => backup::VAR_PARENTID));
            
            $subonline->set_source_table('newassign_sub_onlinetext',
            		array('submission' => backup::VAR_PARENTID));
            
            $feedcomment->set_source_table('newassign_feed_comment',
            		array('feedback' => backup::VAR_PARENTID));
            
            $gradeguidefilling->set_source_table('newassign_guide_fillings',
            		array('gradeid' => backup::VAR_PARENTID));
            
            $graderubricfilling->set_source_table('newassign_rubric_fillings',
            		array('gradeid' => backup::VAR_PARENTID));
        }


        // Define id annotations
        $submission->annotate_ids('user', 'userid');
        $grade->annotate_ids('user', 'userid');
        $grade->annotate_ids('user', 'grader');
        $feedback->annotate_ids('user', 'userid');

        // Define file annotations
        $newassignment->annotate_files('mod_newassignment', 'intro', null); // This file area hasn't itemid
        $subonline->annotate_files('newassignsubmission_onlinetext', 'submissions_onlinetext', 'submission');
        $feedcomment->annotate_files('newassignfeedback_comment', 'feedbacks_comment', 'feedback');
		
        // Return the root element (choice), wrapped into standard activity structure              
        return $this->prepare_activity_structure($newassignment);
    }
}
