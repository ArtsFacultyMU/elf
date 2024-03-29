<?php

class newassignment_header implements renderable {
	/** @var stdClass the assign record  */
	var $assignment = null;
	/** @var mixed context|null the context record  */
	var $context = null;
	/** @var bool $showintro - show or hide the intro */
	var $showintro = false;
	/** @var int coursemoduleid - The course module id */
	var $coursemoduleid = 0;
	/** @var string $subpage optional subpage (extra level in the breadcrumbs) */
	var $subpage = '';
	/** @var string $preface optional preface (text to show before the heading) */
	var $preface = '';
	
	var $publish = false;

	/**
	 * Constructor
	 *
	 * @param stdClass $assign  - the assign database record
	 * @param mixed $context context|null the course module context (or the course context if the coursemodule has not been created yet)
	 * @param bool $showintro  - show or hide the intro
	 * @param int $coursemoduleid  - the course module id
	 * @param string $subpage  - an optional sub page in the navigation
	 * @param string $preface  - an optional preface to show before the heading
	 */
	public function __construct(stdClass $assignment, $context, $showintro, $coursemoduleid, $subpage='', $preface='', $publish = false) {
		$this->assignment = $assignment;
		$this->context = $context;
		$this->showintro = $showintro;
		$this->coursemoduleid = $coursemoduleid;
		$this->subpage = $subpage;
		$this->preface = $preface;
		$this->publish = $publish;
	}
}

/**
 * Implements a renderable grading error notification
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class newassignment_quickgrading_result implements renderable {
	/** @var string $message is the message to display to the user */
	var $message = '';
	/** @var int $coursemoduleid */
	var $coursemoduleid = 0;

	/**
	 * Constructor
	 * @param string $message This is the message to display
	 */
	public function __construct($message, $coursemoduleid) {
		$this->message = $message;
		$this->coursemoduleid = $coursemoduleid;
	}

}

/**
 * Implements a renderable feedback plugin feedback
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class newassignment_feedback_plugin_feedback implements renderable {

	/** @var assign_submission_plugin $plugin */
	var $plugin = null;
	/** @var stdClass $grade */
	var $feedback = null;
	/** @var int $coursemoduleid */
	var $coursemoduleid = 0;
	/** @var string returnaction The action to take you back to the current page */
	var $returnaction = '';
	/** @var array returnparams The params to take you back to the current page */
	var $returnparams = array();

	/**
	 * feedback for a single plugin
	 *
	 * @param assign_feedback_plugin $plugin
	 * @param stdClass $grade
	 * @param string $view one of feedback_plugin::SUMMARY or feedback_plugin::FULL
	 * @param int $coursemoduleid
	 * @param string $returnaction The action required to return to this page
	 * @param array $returnparams The params required to return to this page
	*/
	public function __construct($plugin, stdClass $feedback, $coursemoduleid, $returnaction, $returnparams) {
		$this->plugin = $plugin;
		$this->feedback = $feedback;
		$this->coursemoduleid = $coursemoduleid;
		$this->returnaction = $returnaction;
		$this->returnparams = $returnparams;
	}

}

class newassignment_grading_summary implements renderable {
	/** @var int participantcount - The number of users who can submit to this assignment */
	var $participantcount = 0;
	/** @var int submissiondraftscount - The number of submissions in draft status */
	var $submissionscount = 0;
	/** @var int duedate - The assignment due date (if one is set) */
	var $duedate = 0;
	/** @var int coursemoduleid - The assignment course module id */
	var $coursemoduleid = 0;

	/**
	 * constructor
	 *
	 * @param int $participantcount
	 * @param bool $submissiondraftsenabled
	 * @param int $submissiondraftscount
	 * @param bool $submissionsenabled
	 * @param int $submissionssubmittedcount
	 * @param int $duedate
	 * @param int $coursemoduleid
	 */
	public function __construct($participantcount, $submissionscount, $duedate, $coursemoduleid) {
		$this->participantcount = $participantcount;
		$this->submissionscount = $submissionscount;
		$this->duedate = $duedate;
		$this->coursemoduleid = $coursemoduleid;
	}
}


class newassignment_submit_for_grading_page implements renderable {
	/** @var int $coursemoduleid */
	var $coursemoduleid = 0;

	/**
	 * Constructor
	 * @param string $notifications - Any mesages to display
	 * @param int $coursemoduleid
	 */
	public function __construct($coursemoduleid) {
		$this->coursemoduleid = $coursemoduleid;
	}

}

/**
 * Implements a renderable submission plugin submission
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class newassignment_submission_plugin_submission implements renderable {

	/** @var assign_submission_plugin $plugin */
	var $plugin = null;
	/** @var stdClass $submission */
	var $submission = null;
	/** @var int $coursemoduleid */
	var $coursemoduleid = 0;
	/** @var string returnaction The action to take you back to the current page */
	var $returnaction = '';
	/** @var array returnparams The params to take you back to the current page */
	var $returnparams = array();



	/**
	 * Constructor
	 * @param assign_submission_plugin $plugin
	 * @param stdClass $submission
	 * @param string $view one of submission_plugin::SUMMARY, submission_plugin::FULL
	 * @param int $coursemoduleid - the course module id
	 * @param string $returnaction The action to return to the current page
	 * @param array $returnparams The params to return to the current page
	*/
	public function __construct($plugin, stdClass $submission, $coursemoduleid, $returnaction, $returnparams) {
		$this->plugin = $plugin;
		$this->submission = $submission;
		$this->coursemoduleid = $coursemoduleid;
		$this->returnaction = $returnaction;
		$this->returnparams = $returnparams;
	}
}

class newassignment_submission_status implements renderable {
	/** @var int STUDENT_VIEW */
	const STUDENT_VIEW     = 10;
	/** @var int GRADER_VIEW */
	const GRADER_VIEW      = 20;

	/** @var int allowsubmissionsfromdate */
	var $allowsubmissionsfromdate = 0;
	/** @var bool alwaysshowdescription */
	var $alwaysshowdescription = false;
	/** @var stdClass the submission info (may be null) */
	var $submission = null;
	/** @var bool graded */
	var $graded = false;
	/** @var int duedate */
	var $duedate = 0;
	/** @var string returnaction */
	var $returnaction = '';
	/** @var string returnparams */
	var $returnparams = array();
	/** @var int coursemoduleid */
	var $coursemoduleid = 0;
	/** @var int the view (assign_submission_status::STUDENT_VIEW OR assign_submission_status::GRADER_VIEW) */
	var $view = self::STUDENT_VIEW;
	/** @var bool canedit */
	var $canedit = false;
	
	var $locked = false;
	
	var $submissionplugin;
	
	var $submissionaction;
	
	var $submissioncomments;
	
	var $grademethod;

	/**
	 * constructor
	 *
	 * @param int $allowsubmissionsfromdate
	 * @param bool $alwaysshowdescription
	 * @param stdClass $submission
	 * @param bool $submissionsenabled
	 * @param bool $locked
	 * @param bool $graded
	 * @param int $duedate
	 * @param array $submissionplugins
	 * @param string $returnaction
	 * @param array $returnparams
	 * @param int $coursemoduleid
	 * @param string $view
	 * @param bool $canedit
	 * @param bool $cansubmit
	 */
	public function __construct($allowsubmissionsfromdate, $alwaysshowdescription, $submission, $submissionplugin, $submissionaction, $submissioncomments,
			$grademethod, $locked, $graded, $duedate, $returnaction, $returnparams,
			$coursemoduleid, $view, $canedit) {
		$this->allowsubmissionsfromdate = $allowsubmissionsfromdate;
		$this->alwaysshowdescription = $alwaysshowdescription;
		$this->submission = $submission;
		$this->submissionplugin = $submissionplugin;
		$this->submissionaction = $submissionaction;
		$this->submissioncomments = $submissioncomments;
		$this->grademethod = $grademethod;
		$this->locked = $locked;
		$this->graded = $graded;
		$this->duedate = $duedate;
		$this->returnaction = $returnaction;
		$this->returnparams = $returnparams;
		$this->coursemoduleid = $coursemoduleid;
		$this->view = $view;
		$this->canedit = $canedit;
	}
}

class newassignment_feedback_status implements renderable {

	/** @var stding $gradefordisplay the student grade rendered into a format suitable for display */
	var $gradefordisplay = '';
	/** @var mixed the graded date (may be null) */
	var $gradeddate = 0;
	/** @var mixed the grader (may be null) */
	var $grader = null;
	/** @var stdClass assign_grade record */
	var $grade = null;
	/** @var int coursemoduleid */
	var $coursemoduleid = 0;
	/** @var string returnaction */
	var $returnaction = '';
	/** @var array returnparams */
	var $returnparams = array();
	
	var $assignment;
	
	var $feedback;
	
	var $actualgrade;
	
	var $gradehidden;

	/**
	 * Constructor
	 * @param string $gradefordisplay
	 * @param mixed $gradeddate
	 * @param mixed $grader
	 * @param array $feedbackplugins
	 * @param mixed $grade
	 * @param int $coursemoduleid
	 * @param string $returnaction The action required to return to this page
	 * @param array $returnparams The list of params required to return to this page
	*/
	public function __construct($gradefordisplay, $actualgrade, $gradeddate, $grader, $grade, $gradehidden, $coursemoduleid, $returnaction, $returnparams, $assignment, $feedback) {
		$this->gradefordisplay = $gradefordisplay;
		$this->actualgrade = $actualgrade;
		$this->gradeddate = $gradeddate;
		$this->grader = $grader;
		$this->grade = $grade;
		$this->coursemoduleid = $coursemoduleid;
		$this->returnaction = $returnaction;
		$this->returnparams = $returnparams;
		$this->assignment = $assignment;
		$this->feedback = $feedback;
		$this->gradehidden = $gradehidden;
	}
}

/**
 * Implements a renderable user summary
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class newassignment_user_summary implements renderable {
	/** @var stdClass $user suitable for rendering with user_picture and fullname(). Must contain firstname, lastname, id and picture fields */
	public $user = null;
	/** @var int $courseid */
	public $courseid;
	/** @var bool $viewfullnames */
	public $viewfullnames = false;

	/**
	 * Constructor
	 * @param stdClass $user
	 * @param int $courseid
	 * @param bool $viewfullnames
	 */
	public function __construct(stdClass $user, $courseid, $viewfullnames) {
		$this->user = $user;
		$this->courseid = $courseid;
		$this->viewfullnames = $viewfullnames;
	}
}

class newassignment_form implements renderable {
	/** @var moodleform $form is the edit submission form */
	var $form = null;
	/** @var string $classname is the name of the class to assign to the container */
	var $classname = '';
	/** @var string $jsinitfunction is an optional js function to add to the page requires */
	var $jsinitfunction = '';

	/**
	 * Constructor
	 * @param string $classname This is the class name for the container div
	 * @param moodleform $form This is the moodleform
	 * @param string $jsinitfunction This is an optional js function to add to the page requires
	 */
	public function __construct($classname, moodleform $form, $jsinitfunction = '') {
		$this->classname = $classname;
		$this->form = $form;
		$this->jsinitfunction = $jsinitfunction;
	}

}

class newassignment_files implements renderable {
	/** @var context $context */
	public $context;
	/** @var string $context */
	public $dir;
	/** @var MoodleQuickForm $portfolioform */
	public $portfolioform;
	/** @var stdClass $cm course module */
	public $cm;
	/** @var stdClass $course */
	public $course;


	/**
	 * The constructor
	 *
	 * @param context $context
	 * @param int $sid
	 * @param string $filearea
	 * @param string $component
	 */
	public function __construct(context $context, $sid, $filearea, $component) {
		global $CFG;
		$this->context = $context;
		list($context, $course, $cm) = get_context_info_array($context->id);
		$this->cm = $cm;
		$this->course = $course;
		$fs = get_file_storage();
		$this->dir = $fs->get_area_tree($this->context->id, $component, $filearea, $sid);

		$files = $fs->get_area_files($this->context->id, $component, $filearea, $sid, "timemodified", false);

		if (!empty($CFG->enableportfolios)) {
			require_once($CFG->libdir . '/portfoliolib.php');
			if (count($files) >= 1 && has_capability('mod/newassignment:exportownsubmission', $this->context)) {
				$button = new portfolio_add_button();
				$button->set_callback_options('newassignment_portfolio_caller', array('cmid' => $this->cm->id, 'sid'=>$sid, 'area'=>$filearea, 'component'=>$component), '/mod/newassignment/portfolio_callback.php');
				$button->reset_formats();
				$this->portfolioform = $button->to_html(PORTFOLIO_ADD_TEXT_LINK);
			}

		}

		// plagiarism check if it is enabled
		$output = '';
		if (!empty($CFG->enableplagiarism)) {
			require_once($CFG->libdir . '/plagiarismlib.php');

			// for plagiarism_get_links
			$assignment = new NewAssignment($this->context, null, null);
			foreach ($files as $file) {

				$output .= plagiarism_get_links(array('userid' => $sid,
						'file' => $file,
						'cmid' => $this->cm->id,
						'course' => $this->course,
						'assignment' => $assignment->get_instance()));

				$output .= '<br />';
			}
		}

		$this->preprocess($this->dir, $filearea, $component);
	}

	/**
	 * preprocessing the file list to add the portfolio links if required
	 *
	 * @param array $dir
	 * @param string $filearea
	 * @param string $component
	 * @return void
	 */
	public function preprocess($dir, $filearea, $component) {
		global $CFG;
		foreach ($dir['subdirs'] as $subdir) {
			$this->preprocess($subdir, $filearea, $component);
		}
		foreach ($dir['files'] as $file) {
			$file->portfoliobutton = '';
			if (!empty($CFG->enableportfolios)) {
				$button = new portfolio_add_button();
				if (has_capability('mod/newassignment:exportownsubmission', $this->context)) {
					$button->set_callback_options('newassignment_portfolio_caller', array('cmid' => $this->cm->id, 'fileid' => $file->get_id()), '/mod/newassignment/portfolio_callback.php');
					$button->set_format_by_file($file);
					$file->portfoliobutton = $button->to_html(PORTFOLIO_ADD_ICON_LINK);
				}
			}
			$url = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'.$this->context->id.'/'.$component.'/'.$filearea.'/'.$file->get_itemid(). $file->get_filepath().$file->get_filename(), true);
            $filename = $file->get_filename();
			$file->fileurl = html_writer::link($url, $filename);
		}
	}
}