<?php

defined('MOODLE_INTERNAL') || die();
/**
 * File area for online text submission assignment
 */
define('NEWASSIGN_SUBMISSION_ONLINETEXT_FILEAREA', 'newassignsubmission_onlinetext');

class mod_newassignment_submission_onlinetext {

    protected $_assignment;

    public function __construct(NewAssignment $assignment) {
        $this->_assignment = $assignment;
    }

    public function submission_form($submission, MoodleQuickForm $mform, $data) {

        $mform->addElement('header', 'header', get_string('submissiononlinetext', 'newassignment'));

        $submissionid = $submission ? $submission->id : 0;
        $editoroptions = $this->get_edit_options();

        if (!isset($data->onlinetext)) {
            $data->onlinetext = '';
        }
        if (!isset($data->onlinetextformat)) {
            $data->onlinetextformat = editors_get_preferred_format();
        }

        if ($submission) {
            $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);
            if ($onlinetextsubmission) {
                $data->onlinetext = $onlinetextsubmission->text;
                $data->onlinetextformat = $onlinetextsubmission->format;
            }
        }
        $data->cmid = $this->_assignment->get_course_module()->id;
        $data = file_prepare_standard_editor($data, 'onlinetext', $editoroptions, $this->_assignment->get_context(), 'mod_newassignment', NEWASSIGN_SUBMISSION_ONLINETEXT_FILEAREA, $submissionid);
        $mform->addElement('editor', 'onlinetext_editor', '', null, $editoroptions);
        return true;
    }

    public function save_submission($submission, $data) {
        global $DB;

        $editoroptions = $this->get_edit_options();
        $data = file_postupdate_standard_editor($data, 'onlinetext', $editoroptions, $this->_assignment->get_context(), 'mod_newassignment', NEWASSIGN_SUBMISSION_ONLINETEXT_FILEAREA, $submission->id);

        $onlinetext = $this->get_onlinetext_submission($submission->id);
        if ($onlinetext) {

            $onlinetext->text = $data->onlinetext;
            $onlinetext->format = $data->onlinetext_editor['format'];


            return $DB->update_record('newassign_sub_onlinetext', $onlinetext);
        } else {

            $onlinetext = new stdClass();
            $onlinetext->text = $data->onlinetext;
            $onlinetext->format = $data->onlinetext_editor['format'];

            $onlinetext->submission = $submission->id;
            return $DB->insert_record('newassign_sub_onlinetext', $onlinetext) > 0;
        }
    }

    public function view_summary(stdClass $submission, $returnAction = '') {
        global $OUTPUT;
        $onlinetext = $this->get_onlinetext_submission($submission->id);
        if ($onlinetext)
            return $OUTPUT->action_link(new moodle_url('/mod/newassignment/view.php', array('id' => $this->_assignment->get_course_module()->id, 'itemid' => $submission->id, 'action' => 'viewsubmission','returnaction'=>$returnAction)), get_string('showfull', 'newassignment'));
        else
            return get_string('notsubmitted', 'newassignment');
    }

    public function view($submissionid) {
        $onlinetext = $this->get_onlinetext_submission($submissionid);
        if ($onlinetext) {
            $text = file_rewrite_pluginfile_urls($onlinetext->text, 'pluginfile.php', $this->_assignment->get_context()->id, 'mod_newassignment', NEWASSIGN_SUBMISSION_ONLINETEXT_FILEAREA, $submissionid);
            return format_text($text, $onlinetext->format, array('context' => $this->_assignment->get_context()));
        } else
            return get_string('notsubmitted', 'newassignment');
    }

    public function get_name() {
        return get_string('submissiononlinetext', 'newassignment');
    }

    public function is_empty(stdClass $submission) {
        global $DB;
        return !$DB->record_exists('newassign_sub_onlinetext', array('submission' => $submission->id));
    }

    public function format_for_log(stdClass $submissionorgrade) {
        // format the info for each submission plugin add_to_log
        return '';
    }

    /**
     * Produce a list of files suitable for export that represent this submission
     *
     * @param stdClass $submission - For this is the submission data
     * @return array - return an array of files indexed by filename
     */
    public function get_files(stdClass $submission) {
        global $DB;
        $files = array();
        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);
        if ($onlinetextsubmission) {
            $user = $DB->get_record("user", array("id" => $submission->userid), 'id,username,firstname,lastname', MUST_EXIST);

            $prefix = clean_filename(elf_unaccent(fullname($user) . "_" . $submission->version . "_"));
            $finaltext = str_replace('@@PLUGINFILE@@/', $prefix, $onlinetextsubmission->text);
            $submissioncontent = "<html><body>" . format_text($finaltext, $onlinetextsubmission->format, array('context' => $this->_assignment->get_context())) . "</body></html>";      //fetched from database

            $files[get_string('onlinetextfilename', 'newassignment') . '.html'] = array($submissioncontent);

            $fs = get_file_storage();

            $fsfiles = $fs->get_area_files($this->_assignment->get_context()->id, 'newassignsubmission_onlinetext', NEWASSIGN_SUBMISSION_ONLINETEXT_FILEAREA, $submission->id, "timemodified", false);

            foreach ($fsfiles as $file) {
                $files[$file->get_filename()] = $file;
            }
        }

        return $files;
    }

    /**
     * Get the saved text content from the editor
     *
     * @param string $name
     * @param int $submissionid
     * @return string
     */
    public function get_editor_text($name, $submissionid) {
        $onlinetextsubmission = $this->get_onlinetext_submission($submissionid);
        if ($onlinetextsubmission) {
            return $onlinetextsubmission->text;
        }
        return '';
    }

    /**
     * Get the content format for the editor
     *
     * @param string $name
     * @param int $submissionid
     * @return int
     */
    public function get_editor_format($name, $submissionid) {
        $onlinetextsubmission = $this->get_onlinetext_submission($submissionid);
        if ($onlinetextsubmission) {
            return $onlinetextsubmission->format;
        }


        return 0;
    }

    protected function get_onlinetext_submission($submissionid) {
        global $DB;
        return $DB->get_record('newassign_sub_onlinetext', array('submission' => $submissionid));
    }

    /**
     * Editor format options
     *
     * @return array
     */
    protected function get_edit_options() {
        $editoroptions = array(
            'noclean' => true,
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'maxbytes' => $this->_assignment->get_course()->maxbytes,
            'context' => $this->_assignment->get_context()
        );
        return $editoroptions;
    }
    
    public function get_file_area() {
        return NEWASSIGN_SUBMISSION_ONLINETEXT_FILEAREA;
    }
    
    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance($id) {
        global $DB;
		$DB->delete_records_select('newassign_sub_onlinetext', 'submission IN (SELECT id FROM {newassign_submissions} WHERE assignment=:assignment)', array('assignment'=>$id));
        return true;
    }

}