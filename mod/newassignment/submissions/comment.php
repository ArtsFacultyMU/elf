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
 * This file contains the definition for the library class for online comment submission plugin
 *
 * @package assignsubmission_comments
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();

 /** Include comment core lib.php */
 require_once($CFG->dirroot . '/comment/lib.php');


/**
 * library class for comment submission plugin extending submission plugin base class
 *
 * @package assignsubmission_comments
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_newassignment_submission_comment {

	protected $_assignment;
	
	public function __construct(NewAssignment $assignment) {
		$this->_assignment = $assignment;
	}
	
   /**
    * get the name of the online comment submission plugin
    * @return string
    */
    public function get_name() {
        return get_string('submissioncomments', 'newassignment');
    }

   /**
    * display AJAX based comment in the submission status table
    *
    * @param stdClass $submission
    * @param bool $showviewlink - If the comments are long this is set to true so they can be shown in a separate page
    * @return string
    */
   public function view_summary(stdClass $submission, $returnAction = '') {

        // need to used this init() otherwise it shows up undefined !
        // require js for commenting
        comment::init();

        $options = new stdClass();
        $options->area    = 'submission_comments';
        $options->course  = $this->_assignment->get_course();
        $options->context = $this->_assignment->get_context();
        $options->itemid  = $submission->id;
        $options->component = 'newassignment';
        $options->showcount = true;
        $options->displaycancel = true;

        $comment = new comment($options);
        $comment->set_view_permission(true);

        return $comment->output(true);

    }

}
