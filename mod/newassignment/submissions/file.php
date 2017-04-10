<?php

defined('MOODLE_INTERNAL') || die();

define('NEWASSIGNSUBMISSION_FILE_FILEAREA', 'newassignsubmission_files');
define('NEWASSIGNSUBMISSION_FILE_MAXSUMMARYFILES', 20);

class mod_newassignment_submission_file {

    protected $_assignment;

    public function __construct(NewAssignment $assignment) {
        $this->_assignment = $assignment;
    }

    public function submission_form($submission, MoodleQuickForm $mform, $data) {

        $mform->addElement('header', 'header', get_string('submissionfile', 'newassignment'));

        $submissionid = $submission ? $submission->id : 0;

        $fileoptions = $this->get_file_options();

        $data = file_prepare_standard_filemanager($data, 'files', $fileoptions, $this->_assignment->get_context(), 'mod_newassignment', NEWASSIGNSUBMISSION_FILE_FILEAREA, $submissionid);
        $mform->addElement('filemanager', 'files_filemanager', '', null, $fileoptions);
        return true;
    }

    public function save_submission($submission, $data) {
        global $USER;

        $fileoptions = $this->get_file_options();


        $data = file_postupdate_standard_filemanager($data, 'files', $fileoptions, $this->_assignment->get_context(), 'mod_newassignment', NEWASSIGNSUBMISSION_FILE_FILEAREA, $submission->id);



        //plagiarism code event trigger when files are uploaded

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->_assignment->get_context()->id, 'mod_newassignment', NEWASSIGNSUBMISSION_FILE_FILEAREA, $submission->id, "id", false);
        // send files to event system
        // Let Moodle know that an assessable file was uploaded (eg for plagiarism detection)
        $params = array(
            'context' => context_module::instance($this->_assignment->get_course_module()->id),
            'courseid' => $this->_assignment->get_course()->id,
            'objectid' => $submission->id,
            'other' => array(
                'content' => '',
                'pathnamehashes' => array_keys($files)
            )
        );
        if (!empty($submission->userid) && ($submission->userid != $USER->id)) {
            $params['relateduserid'] = $submission->userid;
        }
        
        $event = \mod_newassignment\event\assessable_file_uploaded::create($params);
        $event->set_legacy_files($files);
        $event->trigger();
    }

    public function view_summary(stdClass $submission, $returnAction = '') {
        $count = $this->count_files($submission->id, NEWASSIGNSUBMISSION_FILE_FILEAREA);
        if ($count == 0)
            return get_string('notsubmitted', 'newassignment');

        if ($count <= NEWASSIGNSUBMISSION_FILE_MAXSUMMARYFILES) {
            return $this->_assignment->render_area_files('mod_newassignment', NEWASSIGNSUBMISSION_FILE_FILEAREA, $submission->id);
        } else {
            return get_string('countfiles', 'newassignment', $count);
        }

        return '';
    }

    public function view($submissionid) {
        $count = $this->count_files($submissionid, NEWASSIGNSUBMISSION_FILE_FILEAREA);
        if ($count == 0)
            return get_string('notsubmitted', 'newassignment');

        return $this->_assignment->render_area_files('mod_newassignment', NEWASSIGNSUBMISSION_FILE_FILEAREA, $submissionid);
    }

    public function get_name() {
        return get_string('submissionfile', 'newassignment');
    }

    public function is_empty(stdClass $submission) {
        return $this->count_files($submission->id, NEWASSIGNSUBMISSION_FILE_FILEAREA) == 0;
    }

    public function format_for_log(stdClass $submissionorgrade) {
        // format the info for each submission plugin add_to_log
        return '';
    }

    public function get_file_info($browser, $filearea, $itemid, $filepath, $filename) {
        global $CFG, $DB, $USER;
        $urlbase = $CFG->wwwroot . '/pluginfile.php';

        // permission check on the itemid

        if ($itemid) {
            $record = $DB->get_record('newassign_submissions', array('id' => $itemid), 'userid', IGNORE_MISSING);
            if (!$record) {
                return null;
            }
            if (!$this->_assignment->can_view_submission($record->userid)) {
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

    /**
     * Produce a list of files suitable for export that represent this feedback or submission
     *
     * @param stdClass $submission The submission
     * @return array - return an array of files indexed by filename
     */
    public function get_files(stdClass $submission) {
        $result = array();
        $fs = get_file_storage();

        $files = $fs->get_area_files($this->_assignment->get_context()->id, 'mod_newassignment', NEWASSIGNSUBMISSION_FILE_FILEAREA, $submission->id, "timemodified", false);

        foreach ($files as $file) {
            $result[$file->get_filename()] = $file;
        }
        return $result;
    }

    /**
     * Given a field name, should return the text of an editor field that is part of
     * this plugin. This is used when exporting to portfolio.
     *
     * @param string $name Name of the field.
     * @param int $submissionid The id of the submission
     * @return string - The text for the editor field
     */
    public function get_editor_text($name, $submissionid) {
        return '';
    }

    /**
     * Given a field name, should return the format of an editor field that is part of
     * this plugin. This is used when exporting to portfolio.
     *
     * @param string $name Name of the field.
     * @param int $submissionid The id of the submission
     * @return int - The format for the editor field
     */
    public function get_editor_format($name, $submissionid) {
        return 0;
    }

    protected function get_file_options() {
        $fileoptions = array('subdirs' => 1,
            'maxbytes' => $this->_assignment->get_instance()->submissionmaxfilesize,
            'maxfiles' => $this->_assignment->get_instance()->submissionmaxfilecount,
            'accepted_types' => '*',
            'return_types' => FILE_INTERNAL);
        return $fileoptions;
    }

    protected function count_files($submissionid, $area) {

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->_assignment->get_context()->id, 'mod_newassignment', $area, $submissionid, "id", false);

        return count($files);
    }
    
    public function get_file_area() {
        return NEWASSIGNSUBMISSION_FILE_FILEAREA;
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