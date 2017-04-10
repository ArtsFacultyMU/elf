<?php

class mod_newassignment_feedback_comment {
	
	protected $_assignment;
	
	public function __construct(NewAssignment $assignment) {
		$this->_assignment = $assignment;
	}
	
	/**
     * Get form elements for the grading page
     *
     * @param stdClass|null $grade
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return bool true if elements were added to the form
     */
    public function get_form_elements($feedback, MoodleQuickForm $mform, stdClass $data) {
        if ($feedback) {
            $feedbackcomment = $this->get_feedback_comment($feedback->id);
            if ($feedbackcomment) {
                $data->feedbackcomment_editor['text'] = $feedbackcomment->text;
                $data->feedbackcomment_editor['format'] = $feedbackcomment->format;
            }
        }

        $mform->addElement('editor', 'feedbackcomment_editor', '', null, null);
    }
    
    /**
     * Get quickgrading form elements as html
     *
     * @param int $userid The user id in the table this quickgrading element relates to
     * @param mixed $grade - The grade data - may be null if there are no grades for this user (yet)
     * @return mixed - A html string containing the html form elements required for quickgrading
     */
    public function get_quickgrading_html($userid, $feedback) {
    	$commenttext = '';
    	if ($feedback) {
    		$feedbackcomments = $this->get_feedback_comment($feedback->id);
    		if ($feedbackcomments) {
    			$commenttext = $feedbackcomments->text;
    		}
    	}
    
    	return html_writer::tag('textarea', $commenttext, array('name'=>'quickgrade_comments_' . $userid,
    			'class'=>'quickgrade'));
    }
    
    /**
     * Has the plugin quickgrading form element been modified in the current form submission?
     *
     * @param int $userid The user id in the table this quickgrading element relates to
     * @param stdClass $grade The grade
     * @return boolean - true if the quickgrading form element has been modified
     */
    public function is_quickgrading_modified($userid, $feedback) {
    	$commenttext = '';
    	if ($feedback) {
    		$feedbackcomments = $this->get_feedback_comment($feedback->id);
    		if ($feedbackcomments) {
    			$commenttext = $feedbackcomments->text;
    		}
    	}
    	return optional_param('quickgrade_comments_' . $userid, '', PARAM_TEXT) != $commenttext;
    }
    
    /**
     * Save quickgrading changes
     *
     * @param int $userid The user id in the table this quickgrading element relates to
     * @param stdClass $grade The grade
     * @return boolean - true if the grade changes were saved correctly
     */
    public function save_quickgrading_changes($userid, $feedback) {
    	global $DB;
    	$feedbackcomment = $this->get_feedback_comment($feedback->id);
    	if ($feedbackcomment) {
    		$feedbackcomment->text = optional_param('quickgrade_comments_' . $userid, '', PARAM_TEXT);
    		return $DB->update_record('newassign_feed_comment', $feedbackcomment);
    	} else {
    		$feedbackcomment = new stdClass();
    		$feedbackcomment->text = optional_param('quickgrade_comments_' . $userid, '', PARAM_TEXT);
    		$feedbackcomment->format = FORMAT_HTML;
    		$feedbackcomment->feedback = $feedback->id;
    		return $DB->insert_record('newassign_feed_comment', $feedbackcomment) > 0;
    	}
    }
    
    public function save($feedback, $data) {
    	global $DB;
    	$feedbackcomment = $this->get_feedback_comment($feedback->id);
    	//var_dump($data);
    	if ($feedbackcomment) {
    		$feedbackcomment->text = $data->feedbackcomment_editor['text'];
    		$feedbackcomment->format = $data->feedbackcomment_editor['format'];
    		return $DB->update_record('newassign_feed_comment', $feedbackcomment);
    	} else {
    		$feedbackcomment = new stdClass();
    		$feedbackcomment->text = $data->feedbackcomment_editor['text'];
    		$feedbackcomment->format = $data->feedbackcomment_editor['format'];
    		$feedbackcomment->feedback = $feedback->id;
    		return $DB->insert_record('newassign_feed_comment', $feedbackcomment) > 0;
    	}
    }
    
    public function view_summary(stdClass $feedback) {
    	global $OUTPUT;
    	$comment = $this->get_feedback_comment($feedback->id);
    	if ($comment) {
    		$text = format_text($comment->text, $comment->format, array('context'=>$this->_assignment->get_context()));
    		$shorttext = shorten_text($text, 140);
    		if ($text != $shorttext) {
    			return $shorttext . get_string('numwords', 'newassignment', count_words($text)).' '
    					.$OUTPUT->action_link(new moodle_url('/mod/newassignment/view.php',array('id'=>$this->_assignment->get_course_module()->id,'itemid'=>$feedback->id,'action'=>'viewfeedback')), get_string('showfull','newassignment'));
    		} else {
    			return $shorttext;
    		}
    	}
    }
    
    public function view($feedbackid) {
    	$comment = $this->get_feedback_comment($feedbackid);
    	if ($comment) {
    		return format_text($comment->text);
    	}
    	return false;
    }
    
    public function is_empty(stdClass $feedback) {
    	global $DB;
    	return !$DB->record_exists('newassign_feed_comment', array('feedback'=>$feedback->id));
    }
    
    public function get_name() {
    	return get_string('feedbackcomment','newassignment');
    }
    
    protected function get_feedback_comment($feedbackid) {
    	global $DB;
    	return $DB->get_record('newassign_feed_comment', array('feedback'=>$feedbackid));
    }
    
    
    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance($id) {
        global $DB;
		$DB->delete_records_select('newassign_feed_comment', 'feedback IN (SELECT id FROM {newassign_feedbacks} WHERE assignment=:assignment)', array('assignment'=>$id));
        return true;
    }
	
}