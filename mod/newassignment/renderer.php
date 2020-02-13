<?php
/**
 * A custom renderer class that extends the plugin_renderer_base and is used by the assign module.
 *
 * @package mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/** Include locallib.php */
require_once($CFG->dirroot . '/mod/newassignment/locallib.php');

class mod_newassignment_renderer extends plugin_renderer_base {
	
	
	/**
	 * rendering assignment files
	 *
	 * @param context $context
	 * @param int $userid
	 * @param string $filearea
	 * @param string $component
	 * @return string
	 */
	public function newassignment_files(context $context, $userid, $filearea, $component) {
		return $this->render(new newassignment_files($context, $userid, $filearea, $component));
	}
	
	/**
	 * Render a grading error notification
	 * @param assign_quickgrading_result $result The result to render
	 * @return string
	 */
	public function render_newassignment_quickgrading_result(newassignment_quickgrading_result $result) {
		$url = new moodle_url('/mod/newassignment/view.php', array('id' => $result->coursemoduleid, 'action'=>'grading'));
	
		$o = '';
		$o .= $this->output->heading(get_string('quickgradingresult', 'newassignment'), 4);
		$o .= $this->output->notification($result->message);
		$o .= $this->output->continue_button($url);
		return $o;
	}
	
	/**
	 * rendering assignment files
	 *
	 * @param assign_files $tree
	 * @return string
	 */
	public function render_newassignment_files(newassignment_files $tree) {
		$this->htmlid = 'newassignment_files_tree_'.uniqid();
		$this->page->requires->js_init_call('M.mod_newassignment.init_tree', array(true, $this->htmlid));
		$html = '<div id="'.$this->htmlid.'">';
		$html .= $this->htmllize_tree($tree, $tree->dir);
		$html .= '</div>';
	
		if ($tree->portfolioform) {
			$html .= $tree->portfolioform;
		}
		return $html;
	}
	
	/**
	 * render the header
	 *
	 * @param assign_header $header
	 * @return string
	 */
	public function render_newassignment_header(newassignment_header $header) {
		$o = '';
	
		if ($header->subpage) {
			$this->page->navbar->add($header->subpage);
		}
	
		$this->page->set_title(get_string('modulename', 'newassignment'));
		$this->page->set_heading($header->assignment->name);
	
		$o .= $this->output->header();
		if ($header->preface) {
			$o .= $header->preface;
		}
		$o .= $this->output->heading(format_string($header->assignment->name,false, array('context' => $header->context)));
	
		if ($header->showintro) {
			$o .= $this->output->box_start('generalbox boxaligncenter', 'intro');
			$o .= format_module_intro('newassignment', $header->assignment, $header->coursemoduleid);
			$o .= $this->output->box_end();
		}
		
		if($header->publish) {
			$o .= '<div style="text-align:right;">';
			$o .= $this->output->action_link(new moodle_url('/mod/newassignment/view.php',array('action' => 'publish', 'id' => $header->coursemoduleid)), get_string('showotherstudentssubmissions','newassignment'));
			$o .= '</div>';
		}
	
		return $o;
	}
	
	
	/**
	 * render a table containing the current status of the grading process
	 *
	 * @param assign_grading_summary $summary
	 * @return string
	 */
	public function render_newassignment_grading_summary(newassignment_grading_summary $summary) {
		// create a table for the data
		$o = '';
		$o .= $this->output->container_start('gradingsummary');
		$o .= $this->output->heading(get_string('gradingsummary', 'newassignment'), 3);
		$o .= $this->output->box_start('boxaligncenter gradingsummarytable');
		$t = new html_table();
	
		// status
		$this->add_table_row_tuple($t, get_string('numberofparticipants', 'newassignment'),
				$summary->participantcount);
	
		// submitted for grading
		$this->add_table_row_tuple($t, get_string('numberofsubmittedassignments', 'newassignment'),
					$summary->submissionscount);
			
		$time = time();
		if ($summary->duedate) {
			// due date
			// submitted for grading
			$duedate = $summary->duedate;
			$this->add_table_row_tuple($t, get_string('duedate', 'newassignment'),
					userdate($duedate));
	
			// time remaining
			$due = '';
			if ($duedate - $time <= 0) {
				$due = get_string('assignmentisdue', 'newassignment');
			} else {
				$due = format_time($duedate - $time);
			}
			$this->add_table_row_tuple($t, get_string('timeremaining', 'newassignment'), $due);
		}
	
		// all done - write the table
		$o .= html_writer::table($t);
		$o .= $this->output->box_end();
	
		// link to the grading page
		$o .= $this->output->container_start('submissionlinks');
		$o .= $this->output->action_link(new moodle_url('/mod/newassignment/view.php',
				array('id' => $summary->coursemoduleid,
						'action'=>'grading')),
				get_string('viewgrading', 'newassignment'));
		$o .= $this->output->container_end();
	
		// close the container and insert a spacer
		$o .= $this->output->container_end();
	
		return $o;
	}
	
	/**
	 * Render the user summary
	 *
	 * @param assign_user_summary $summary The user summary to render
	 * @return string
	 */
	public function render_newassignment_user_summary(newassignment_user_summary $summary) {
		$o = '';
	
		if (!$summary->user) {
			return;
		}
		$o .= $this->output->container_start('usersummary');
		$o .= $this->output->box_start('boxaligncenter usersummarysection');
		$o .= $this->output->user_picture($summary->user);
		$o .= $this->output->spacer(array('width'=>30));
		$o .= $this->output->action_link(new moodle_url('/user/view.php',
				array('id' => $summary->user->id,
						'course'=>$summary->courseid)),
				fullname($summary->user, $summary->viewfullnames));
		$o .= $this->output->box_end();
		$o .= $this->output->container_end();
	
		return $o;
	}
	
	/**
	 * Render a feedback plugin feedback
	 *
	 * @param assign_feedback_plugin_feedback $feedbackplugin
	 * @return string
	 */
	public function render_newassignment_feedback_plugin_feedback(newassignment_feedback_plugin_feedback $feedbackplugin) {
		$o = '';
		$o .= $this->output->box_start('boxaligncenter feedbackfull');
		$o .= $feedbackplugin->plugin->view($feedbackplugin->feedback->id);
		$o .= $this->output->box_end();
		return $o;
	}
	
	/**
	 * render a table containing all the current grades and feedback
	 *
	 * @param assign_feedback_status $status
	 * @return string
	 */
	public function render_newassignment_feedback_status(newassignment_feedback_status $status) {
		global $DB, $CFG;
		$o = '';
	
		$o .= $this->output->container_start('feedback');
		$o .= $this->output->heading(get_string('feedback', 'newassignment'), 3);
		$o .= $this->output->box_start('boxaligncenter feedbacktable');
		$t = new html_table();
	
		if(!$status->gradehidden) {
			$row = new html_table_row();
			$cell1 = new html_table_cell(get_string('recentgrade','newassignment'));
			$cell2 = new html_table_cell($status->actualgrade);
			$row->cells = array($cell1, $cell2);
			$t->data[] = $row;
			
			$row = new html_table_row();
			$cell1 = new html_table_cell(get_string('finalgrade','newassignment'));
			$cell2 = new html_table_cell($status->gradefordisplay);
			$row->cells = array($cell1, $cell2);
			$t->data[] = $row;
		}
		
		$row = new html_table_row();
		$cell1 = new html_table_cell(get_string('gradedon', 'newassignment'));
		$cell2 = new html_table_cell(userdate($status->gradeddate));
		$row->cells = array($cell1, $cell2);
		$t->data[] = $row;
	
		if ($status->grader) {
			$row = new html_table_row();
			$cell1 = new html_table_cell(get_string('gradedby', 'newassignment'));
			$cell2 = new html_table_cell($this->output->user_picture($status->grader) . $this->output->spacer(array('width'=>30)) . fullname($status->grader));
			$row->cells = array($cell1, $cell2);
			$t->data[] = $row;
		}
	
		if($status->feedback) {
			$row = new html_table_row();
			$cell1 = new html_table_cell(get_string('feedbackstatus','newassignment'));
			$cell2 = new html_table_cell(get_string('feedbackstatus_'.$status->feedback->status,'newassignment'));
			$cell2->attributes = array('class'=>'feedbackstatus' . $status->feedback->status);
			$row->cells = array($cell1, $cell2);
			$t->data[] = $row;
			
			require_once($CFG->dirroot.'/mod/newassignment/feedbacks/comment.php');
			$fcomment = new mod_newassignment_feedback_comment($status->assignment);
			if (!$fcomment->is_empty($status->feedback)) {
				$row = new html_table_row();
				$cell1 = new html_table_cell($fcomment->get_name());
				$cell2 = new html_table_cell($fcomment->view_summary($status->feedback));
				$row->cells = array($cell1, $cell2);
				$t->data[] = $row;
			}
			
			require_once($CFG->dirroot.'/mod/newassignment/feedbacks/file.php');
			$ffile = new mod_newassignment_feedback_file($status->assignment);
			if (!$ffile->is_empty($status->feedback)) {
				$row = new html_table_row();
				$cell1 = new html_table_cell($ffile->get_name());
				$cell2 = new html_table_cell($ffile->view_summary($status->feedback));
				$row->cells = array($cell1, $cell2);
				$t->data[] = $row;
			}
		}

		$o .= html_writer::table($t);
		$o .= $this->output->box_end();
	
		$o .= $this->output->container_end();
		return $o;
	}
	
	/**
	 * Render the submit for grading page
	 *
	 * @param assign_submit_for_grading_page $page
	 * @return string
	 */
	public function render_newassignment_submit_for_grading_page($page) {
		$o = '';
	
		$o .= $this->output->container_start('submitforgrading');
		$o .= $this->output->heading(get_string('submitassignment', 'newassignment'), 3);
		$o .= $this->output->spacer(array('height'=>30));
	
		$cancelurl = new moodle_url('/mod/newassignment/view.php', array('id' => $page->coursemoduleid));
		// All submission plugins ready - confirm the student really does want to submit for marking
		$continueurl = new moodle_url('/mod/newassignment/view.php', array('id' => $page->coursemoduleid,
				'action' => 'confirmsubmit',
				'sesskey' => sesskey()));
		$o .= $this->output->confirm(get_string('confirmsubmission', 'newassignment'), $continueurl, $cancelurl);
		$o .= $this->output->container_end();
	
	
		return $o;
	}
	
	/**
	 * render a submission plugin submission
	 *
	 * @param assign_submission_plugin_submission $submissionplugin
	 * @return string
	 */
	public function render_newassignment_submission_plugin_submission(newassignment_submission_plugin_submission $submissionplugin) {
		$o = '';
	
		$o .= $this->output->box_start('boxaligncenter submissionfull');
		$o .= $submissionplugin->plugin->view($submissionplugin->submission->id);
		$o .= $this->output->box_end();
	
		return $o;
	}
	
	/**
	 * render a table containing the current status of the submission
	 *
	 * @param assign_submission_status $status
	 * @return string
	 */
	public function render_newassignment_submission_status(newassignment_submission_status $status) {
		$o = '';
		$o .= $this->output->container_start('submissionstatustable');
		$o .= $this->output->heading(get_string('submissionstatusheading', 'newassignment'), 3);
		$time = time();
	
		if ($status->allowsubmissionsfromdate &&
				$time <= $status->allowsubmissionsfromdate) {
			$o .= $this->output->box_start('generalbox boxaligncenter submissionsalloweddates');
			if ($status->alwaysshowdescription) {
				$o .= get_string('allowsubmissionsfromdatesummary', 'newassignment', userdate($status->allowsubmissionsfromdate));
			} else {
				$o .= get_string('allowsubmissionsanddescriptionfromdatesummary', 'newassignment', userdate($status->allowsubmissionsfromdate));
			}
			$o .= $this->output->box_end();
		}
		$o .= $this->output->box_start('boxaligncenter submissionsummarytable');
	
		$t = new html_table();
	
		if(!isset($status->grademethod))
			$status->grademethod = 'last';
		$row = new html_table_row();
		$cell1 = new html_table_cell(get_string('grademethod', 'newassignment'));
		$cell2 = new html_table_cell(get_string('grade'.$status->grademethod, 'newassignment'));
		$row->cells = array($cell1, $cell2);
		$t->data[] = $row;
		
		$row = new html_table_row();
		$cell1 = new html_table_cell(get_string('submissionstatus', 'newassignment'));
		if ($status->submission) {
			$cell2 = new html_table_cell(get_string('submissionstatus_submitted', 'newassignment'));
			$cell2->attributes = array('class'=>'submissionstatussubmitted');
		} else
			$cell2 = new html_table_cell(get_string('nosubmission', 'newassignment'));
		
		$row->cells = array($cell1, $cell2);
		$t->data[] = $row;
	
		// status
		if ($status->locked) {
			$row = new html_table_row();
			$cell1 = new html_table_cell();
			$cell2 = new html_table_cell(get_string('submissionslocked', 'newassignment'));
			$cell2->attributes = array('class'=>'submissionlocked');
			$row->cells = array($cell1, $cell2);
			$t->data[] = $row;
		}
		
		// grading status
		$row = new html_table_row();
		$cell1 = new html_table_cell(get_string('gradingstatus', 'newassignment'));
	
		if ($status->graded) {
			$cell2 = new html_table_cell(get_string('graded', 'newassignment'));
			$cell2->attributes = array('class'=>'submissiongraded');
		} else {
			$cell2 = new html_table_cell(get_string('notgraded', 'newassignment'));
			$cell2->attributes = array('class'=>'submissionnotgraded');
		}
		$row->cells = array($cell1, $cell2);
		$t->data[] = $row;
	
	
		$duedate = $status->duedate;
		if ($duedate >= 1) {
			$row = new html_table_row();
			$cell1 = new html_table_cell(get_string('duedate', 'newassignment'));
			$cell2 = new html_table_cell(userdate($duedate));
			$row->cells = array($cell1, $cell2);
			$t->data[] = $row;
	
			// time remaining
			$row = new html_table_row();
			$cell1 = new html_table_cell(get_string('timeremaining', 'newassignment'));
			if ($duedate - $time <= 0) {
				if (!$status->submission) {
					$cell2 = new html_table_cell(get_string('duedatereached', 'newassignment'));
				} else {
					if ($status->submission->timemodified > $duedate) {
						$cell2 = new html_table_cell($this->output->box(get_string('submittedlate', 'newassignment', format_time($status->submission->timemodified - $duedate)),'latesubmission'));
					} else {
						$cell2 = new html_table_cell($this->output->box(get_string('submittedearly', 'newassignment', format_time($status->submission->timemodified - $duedate)),'earlysubmission'));
					}
				}
			} else {
				$cell2 = new html_table_cell(format_time($duedate - $time));
			}
			$row->cells = array($cell1, $cell2);
			$t->data[] = $row;
		}
	
		// last modified
		if ($status->submission) {
			$row = new html_table_row();
			$cell1 = new html_table_cell(get_string('timesubmitted', 'newassignment'));
			$cell2 = new html_table_cell(userdate($status->submission->timemodified));
			$row->cells = array($cell1, $cell2);
			$t->data[] = $row;
	
			$plugin = $status->submissionplugin;
			if (!$plugin->is_empty($status->submission)) {
				$row = new html_table_row();
				$cell1 = new html_table_cell($plugin->get_name());
				$cell2 = new html_table_cell($plugin->view_summary($status->submission));
				$row->cells = array($cell1, $cell2);
				$t->data[] = $row;
			}
			
			$row = new html_table_row();
			$cell1 = new html_table_cell(get_string('actualversion', 'newassignment'));
			if($status->submission->version == 1)
				$cell2 = new html_table_cell($status->submission->version);
			else
				$cell2 = new html_table_cell($status->submission->version . ' ('.
							$this->output->action_link(new moodle_url('/mod/newassignment/submissions.php',array('id' => $status->coursemoduleid, 'user' => $status->submission->userid)), 
									get_string('showallversions','newassignment'),null,array('target'=>'_blank')).')');
			$row->cells = array($cell1, $cell2);
			$t->data[] = $row;
			if($status->submissioncomments) {
				$row = new html_table_row();
				$cell1 = new html_table_cell($status->submissioncomments->get_name());
				$cell2 = new html_table_cell($status->submissioncomments->view_summary($status->submission));
				$row->cells = array($cell1, $cell2);
				$t->data[] = $row;
			}
		}
		$o .= html_writer::table($t);
		$o .= $this->output->box_end();
	
		// links
		if ($status->canedit) {
			switch($status->submissionaction) {
				case 'addfirst':
					$o .= $this->output->single_button(new moodle_url('/mod/newassignment/view.php',
							array('id' => $status->coursemoduleid, 'action' => 'editsubmission')), get_string('addsubmission', 'newassignment'), 'get');
					break;
				case 'addnext':
					$o .= $this->output->single_button(new moodle_url('/mod/newassignment/view.php',
							array('id' => $status->coursemoduleid, 'action' => 'editsubmission')), get_string('addnextsubmission', 'newassignment'), 'get');
					break;
			}
		}
		
		$o .= $this->output->container_end();
		return $o;
	}
	
	/**
	 * Page is done - render the footer
	 *
	 * @return void
	 */
	public function render_footer() {
		return $this->output->footer();
	}
	
	/**
	 * Render the generic form
	 * @param assign_form $form The form to render
	 * @return string
	 */
	public function render_newassignment_form(newassignment_form $form) {
		$o = '';
		if ($form->jsinitfunction) {
			$this->page->requires->js_init_call($form->jsinitfunction, array());
		}
		$o .= $this->output->box_start('boxaligncenter ' . $form->classname);
		$o .= $this->moodleform($form->form);
		$o .= $this->output->box_end();
		return $o;
	}
	
	/**
	 * render the grading table
	 *
	 * @param assign_grading_table $table
	 * @return string
	 */
	public function render_newassignment_grading_table(newassignment_grading_table $table) {
		$o = '';
		$o .= $this->output->box_start('boxaligncenter gradingtable');
		$this->page->requires->js_init_call('M.mod_newassignment.init_grading_table', array(get_string('error_feedbackstatus','newassignment')));
		$this->page->requires->string_for_js('nousersselected', 'newassignment');
		$this->page->requires->string_for_js('batchoperationconfirmlock', 'newassignment');
		$this->page->requires->string_for_js('batchoperationconfirmunlock', 'newassignment');
		$this->page->requires->string_for_js('batchoperationconfirmreverttodraft', 'newassignment');
		$this->page->requires->string_for_js('editaction', 'newassignment');
		// need to get from prefs
		$o .= $this->flexible_table($table, $table->get_rows_per_page(), true);
		$o .= $this->output->box_end();
	
		return $o;
	}
	
	/**
	 * render the publish table
	 *
	 * @param assign_publish_table $table
	 * @return string
	 */
	public function render_newassignment_publish_table(newassignment_publish_table $table) {
		$o = '';
		$o .= $this->output->box_start('boxaligncenter publishgtable');
		// need to get from prefs
		$o .= $this->flexible_table($table, 100, true);
		$o .= $this->output->box_end();
	
		return $o;
	}
	
	/**
	 * Utility function to add a row of data to a table with 2 columns. Modified
	 * the table param and does not return a value
	 *
	 * @param html_table $table The table to append the row of data to
	 * @param string $first The first column text
	 * @param string $second The second column text
	 * @return void
	 */
	private function add_table_row_tuple(html_table $table, $first, $second) {
		$row = new html_table_row();
		$cell1 = new html_table_cell($first);
		$cell2 = new html_table_cell($second);
		$row->cells = array($cell1, $cell2);
		$table->data[] = $row;
	}
	
	/**
	 * Helper method dealing with the fact we can not just fetch the output of flexible_table
	 *
	 * @param flexible_table $table The table to render
	 * @param int $rowsperpage How many assignments to render in a page
	 * @param bool $displaylinks - Whether to render links in the table (e.g. downloads would not enable this)
	 * @return string HTML
	 */
	protected function flexible_table(flexible_table $table, $rowsperpage, $displaylinks) {
	
		$o = '';
		ob_start();
		$table->out($rowsperpage, $displaylinks);
		$o = ob_get_contents();
		ob_end_clean();
	
		return $o;
	}
	
	/**
	 * Helper method dealing with the fact we can not just fetch the output of moodleforms
	 *
	 * @param moodleform $mform
	 * @return string HTML
	 */
	protected function moodleform(moodleform $mform) {
	
		$o = '';
		ob_start();
		$mform->display();
		$o = ob_get_contents();
		ob_end_clean();
	
		return $o;
	}
	
	protected function htmllize_tree(newassignment_files $tree, $dir) {
		global $CFG;
		$yuiconfig = array();
		$yuiconfig['type'] = 'html';
	
		if (empty($dir['subdirs']) and empty($dir['files'])) {
			return '';
		}
	
		$result = '<ul>';
		foreach ($dir['subdirs'] as $subdir) {
			$image = $this->output->pix_icon(file_folder_icon(), $subdir['dirname'], 'moodle', array('class'=>'icon'));
			$result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.s($subdir['dirname']).'</div> '.$this->htmllize_tree($tree, $subdir).'</li>';
		}
	
		foreach ($dir['files'] as $file) {
			$filename = $file->get_filename();
			if ($CFG->enableplagiarism) {
				require_once($CFG->libdir.'/plagiarismlib.php');
				$plagiarsmlinks = plagiarism_get_links(array('userid'=>$file->get_userid(), 'file'=>$file, 'cmid'=>$tree->cm->id, 'course'=>$tree->course));
			} else {
				$plagiarsmlinks = '';
			}
			if($file->get_mimetype() == 'audio/mp3') {
                            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'>'.format_text('<div>'.$file->fileurl.'</div>').'</li>';
                        } else {
                            $image = $this->output->pix_icon(file_file_icon($file), $filename, 'moodle', array('class'=>'icon'));
                            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.$file->fileurl.' '.$plagiarsmlinks.$file->portfoliobutton.'</div></li>';
                        }
		}
	
		$result .= '</ul>';
	
		return $result;
	}
}