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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 * Based off of a template @ http://docs.moodle.org/dev/Backup_1.9_conversion_for_developers
 *
 * @package    mod
 * @subpackage label
 * @copyright  2011 Aparup Banerjee <aparup@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/newassignment/locallib.php');

/**
 * Label conversion handler
 */
class moodle1_mod_newassignment_handler extends moodle1_mod_handler {

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances.
     * For each path returned, the corresponding conversion method must be
     * defined.
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/LABEL does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'newassignment', '/MOODLE_BACKUP/COURSE/MODULES/MOD/NEWASSIGNMENT',
                array(
                    'renamefields' => array(
                        'description' => 'intro',
                    	'preventlate' => 'preventlatesubmissions',
                    	'emailteachers' => 'sendnotifications',
                    	'timedue' => 'duedate',
                    	'maxbytes' => 'submissionmaxfilesize',
                    ),
                    'newfields' => array(
                        'introformat' => FORMAT_HTML,
                    	'alwaysshowdescription' => 1,
                    	'submissiondrafts' => 0,
                    	'sendlatenotifications' => 0,
                    	'allowsubmissionsfromdate' => 0,
                    	'submissionmaxfilecount' => 1,
                    	'submissioncomments' => 0,
                    	'publishtime' => NEWASSIGN_PUBLISH_AFTER_SUBMISSION,
                    	'publishsubmissionsanonymously' => 0,
                    	'publishfeedbacksanonymously' => 0,
                    	'publishsubmissions' => NEWASSIGN_PUBLISH_SUBMISSIONS_NO,
                    	'publishfeedbacks' => NEWASSIGN_PUBLISH_FEEDBACKS_NO,
                    	'grademethod' => NEWASSIGN_ATTEMPTLAST
                    ),
                	'dropfields' => array(
                		'format',
                	)
                )
            )
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/LABEL
     * data available
     */
    public function process_newassignment($data) {
        // get the course module id and context id
        $instanceid = $data['id'];
        $cminfo     = $this->get_cminfo($instanceid);
        $moduleid   = $cminfo['id'];
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // get a fresh new file manager for this instance
        $fileman = $this->converter->get_file_manager($contextid, 'mod_newassignment');

        if($data['type'] == 1)
        	$data['submissiontype'] = NEWASSIGN_SUBMISSION_FILE;
        else 
        	$data['submissiontype'] = NEWASSIGN_SUBMISSION_ONLINE_TEXT;
        unset($data['type']);
        
        if($data['viewall'] == 1) {
        	$data['publishsubmissions'] = NEWASSIGN_PUBLISH_SUBMISSIONS_AFTER_SUBMISSION;
        }
        if($data['viewall'] == 2) {
        	$data['publishsubmissions'] = NEWASSIGN_PUBLISH_SUBMISSIONS_AFTER_SUBMISSION;
        	$data['publishsubmissionsanonymously'] = NEWASSIGN_PUBLISH_SUBMISSIONS_ANONYMOUSLY;
        }
        if($data['viewresponses'] == 1) {
        	$data['publishfeedbacks'] = NEWASSIGN_PUBLISH_FEEDBACKS_FILES_COMMENTS;
        }
        unset($data['viewall']);
        unset($data['viewresponses']);
	    
        switch($data['grade']) {
        	case -3:
        	case -54:
        		$data['grade'] = -2;
        		break;
        	case -4:
        	case -55:
        		$data['grade'] = -3;
        		break;
        }
        
        if($data['sentnotifications'] == 1) {
        	$data['sendlatenotifications'] = 1;
        }
        
        if($data['preventlatesubmissions'] == 1) {
        	$data['sendlatenotifications'] = 0;
        }
        
        // convert course files embedded into the intro
        $fileman->filearea = 'intro';
        $fileman->itemid   = 0;
        $data['intro'] = moodle1_converter::migrate_referenced_files($data['intro'], $fileman);

        // write inforef.xml
        $this->open_xml_writer("activities/newassignment_{$moduleid}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        foreach ($fileman->get_fileids() as $fileid) {
            $this->write_xml('file', array('id' => $fileid));
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();

        // write label.xml
        $this->open_xml_writer("activities/newassignment_{$moduleid}/newassignment.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'newassignment', 'contextid' => $contextid));
        $this->write_xml('newassignment', $data, array('/newassignment/id'));
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        return $data;
    }
}
