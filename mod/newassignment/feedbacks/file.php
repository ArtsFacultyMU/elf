<?php

defined('MOODLE_INTERNAL') || die();

define('NEWASSIGNFEEDBACK_FILE_FILEAREA', 'newassignfeedback_files');

class mod_newassignment_feedback_file {

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

        $fileoptions = $this->get_file_options();
        $feedbackid = $feedback ? $feedback->id : 0;

        $data = file_prepare_standard_filemanager($data, 'files', $fileoptions, $this->_assignment->get_context(), 'mod_newassignment', NEWASSIGNFEEDBACK_FILE_FILEAREA, $feedbackid);

        $mform->addElement('filemanager', 'files_filemanager', '', null, $fileoptions);

        return true;
    }

    public function save($feedback, $data) {

        $fileoptions = $this->get_file_options();

        $data = file_postupdate_standard_filemanager($data, 'files', $fileoptions, $this->_assignment->get_context(), 'mod_newassignment', NEWASSIGNFEEDBACK_FILE_FILEAREA, $feedback->id);

        return $feedback->id;
    }

    public function view_summary(stdClass $feedback) {
        $count = $this->count_files($feedback->id, NEWASSIGNFEEDBACK_FILE_FILEAREA);
        if ($count <= 20) {
            return $this->_assignment->render_area_files('mod_newassignment', NEWASSIGNFEEDBACK_FILE_FILEAREA, $feedback->id);
        } else {
            return get_string('countfiles', 'newassignment', $count);
        }

        return '';
    }

    public function view($feedbackid) {
        if ($this->count_files($feedbackid, NEWASSIGNFEEDBACK_FILE_FILEAREA) == 0)
            return false;
        return $this->_assignment->render_area_files('mod_newassignment', NEWASSIGNFEEDBACK_FILE_FILEAREA, $feedbackid);
    }

    public function is_empty(stdClass $feedback) {
        return ($this->count_files($feedback->id, NEWASSIGNFEEDBACK_FILE_FILEAREA) == 0);
    }

    public function get_name() {
        return get_string('feedbackfile', 'newassignment');
    }

    /**
     * Count the number of files
     *
     * @param int $gradeid
     * @param string $area
     * @return int
     */
    private function count_files($feedbackid, $area) {
        global $USER;

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->_assignment->get_context()->id, 'mod_newassignment', $area, $feedbackid, "id", false);

        return count($files);
    }

    /**
     * File format options
     * @return array
     */
    protected function get_file_options() {
        global $COURSE;

        $fileoptions = array('subdirs' => 1,
            'maxbytes' => $COURSE->maxbytes,
            'accepted_types' => '*',
            'return_types' => FILE_INTERNAL);
        return $fileoptions;
    }

    public function get_file_info($browser, $filearea, $itemid, $filepath, $filename) {
        global $CFG, $DB, $USER;
        $urlbase = $CFG->wwwroot . '/pluginfile.php';

        // permission check on the itemid

        if ($itemid) {
            $record = $DB->get_record('newassign_feedbacks', array('id' => $itemid), 'userid', IGNORE_MISSING);
            if (!$record) {
                return null;
            }
            if (!has_capability('mod/newassignment:grade', $this->_assignment->get_context())) {
                return null;
            }
        }

        $fs = get_file_storage();
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;
        if (!($storedfile = $fs->get_file($this->_assignment->get_context()->id, 'submission_file', $filearea, $itemid, $filepath, $filename))) {
            return null;
        }
        return new file_info_stored($browser,
                        $this->_assignment->get_context(),
                        $storedfile,
                        $urlbase,
                        $filearea,
                        $itemid,
                        true,
                        true,
                        false);
    }
    
     public function get_file_area() {
        return NEWASSIGNFEEDBACK_FILE_FILEAREA;
    }
    
    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance($id) {
        return true;
    }

}