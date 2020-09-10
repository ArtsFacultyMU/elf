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

namespace local_remote_backup_provider\helper;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot.'/course/lib.php');

use local_remote_backup_provider\exception\transfer_manager_exception;
use local_remote_backup_provider\exception\configuration_exception;


/**
 * Manages course lookup and transfer.
 *
 * @package    local_remote_backup_provider
 * @copyright  2019 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class transfer_manager {

    // Constants.

    /**
     * Base of the URL for reaching files on the remote.
     */
    const URL_BASE_FORMAT = '%s/webservice/rest/server.php' . 
            '?wstoken=%s' . 
            '&moodlewsrestformat=json';

    /**
     * URL parameters to get course search.
     */
    const URL_PARAMS_SEARCH = '&wsfunction=local_remote_backup_provider_find_teacher_courses';
    
    /**
     * URL parameters to get course backup.
     */
    const URL_PARAMS_BACKUP = '&wsfunction=local_remote_backup_provider_get_course_backup_by_id';

    /**
     * URL parameters to get course backup.
     */
    const URL_PARAMS_NAME = '&wsfunction=local_remote_backup_provider_get_course_name_by_id';

    /**
     * URL parameters to get course backup.
     */
    const URL_PARAMS_CATEGORYID = '&wsfunction=local_remote_backup_provider_get_course_category_by_id';

    /**
     * URL parameters to get course backup.
     */
    const URL_PARAMS_CATEGORYINFO = '&wsfunction=local_remote_backup_provider_get_category_info';

    const STATUS_ADDED = 'added';
    const STATUS_PROCESSING = 'processing';
    const STATUS_ERROR = 'error';
    const STATUS_CANCELED = 'canceled';
    const STATUS_FINISHED = 'finished';

    const LABEL_FOR_STATUS = [
        self::STATUS_ADDED => 'secondary',
        self::STATUS_PROCESSING => 'secondary',
        self::STATUS_ERROR => 'warning',
        self::STATUS_CANCELED => 'warning',
        self::STATUS_FINISHED => 'success',
    ];





    // Variables.

    /**
     * Remote data.
     * 
     * @var stdClass
     */
    private $remote;

    /**
     * Transfer data.
     * 
     * @var stdClass
     */
    private $transfer;





    // Static classes.

    /**
     * Looks for the courses containing given string in their name or short name on the remote.
     * 
     * @param stdClass $remote Information about remote.
     * @param string $search String to be searched for.
     */
    public static function search($remote, $search) {
        global $USER;
        
        if (empty($remote->address)) {
            throw new configuration_exception(configuration_exception::CODE_NO_ADDRESS);
        }

        if (empty($remote->token)) {
            throw new configuration_exception(configuration_exception::CODE_NO_TOKEN);
        }

        $url = sprintf(self::URL_BASE_FORMAT, $remote->address, $remote->token) . self::URL_PARAMS_SEARCH;
        $params = array('search' => $search, 'username' => $USER->username, 'auth' => $USER->auth);
        $curl = new \curl;
        $results = json_decode($curl->post($url, $params));
        return $results;
    }

    /**
     * Returns name of the remote course.
     * 
     * @param stdClass $remote Information about remote.
     * @param string $course_id Remote course ID.
     */
    public static function get_remote_course_name($remote, $course_id) {
        if (empty($remote->address)) {
            throw new configuration_exception(configuration_exception::CODE_NO_ADDRESS);
        }

        if (empty($remote->token)) {
            throw new configuration_exception(configuration_exception::CODE_NO_TOKEN);
        }
        $url = sprintf(self::URL_BASE_FORMAT, $remote->address, $remote->token) . self::URL_PARAMS_NAME;
        $params = array('id' => $course_id);
        $curl = new \curl;
        $results = json_decode($curl->post($url, $params));
        return $results->name;
    }

    /**
     * Adds new transfer to be used by this manager.
     * 
     * @param stdClass $remote Information about remote.
     * @param string $course_id Remote course ID.
     */
    public static function add_new($remote, $course_id) {
        global $DB, $USER;
        $remote_course_name = self::get_remote_course_name($remote, $course_id);

        // Get current time (to have the same in log & main database table).
        $datetime = new \DateTime();
        
        // Insert information into (main) transfer database table.
        $transfer_data = (object) [
            'remoteid' => $remote->id,
            'remotecourseid' =>$course_id,
            'remotecoursename' => $remote_course_name,
            'remotebackupurl' => null,
            'courseid' => null,
            'status' => 'added',
            'userid' => $USER->id,
            'timecreated' => $datetime->getTimestamp(),
            'timemodified' => $datetime->getTimestamp(),
        ];
        $transfer_id = $DB->insert_record('local_remotebp_transfer', $transfer_data);
        
        // Insert complementary information into the (secondary) transfer log database table.
        $log_data = (object) [
            'transferid' => $transfer_id,
            'timemodified' => $datetime->getTimestamp(),
            'status' => 'added',
            'fullstatus' => 'added',
            'notes' => null,
        ];
        $DB->insert_record('local_remotebp_transfer_log', $log_data);

        // Return ID.
        return (int)$transfer_id;
    }





    // Instance methods.
    /**
     * Creates instance of the transfer manager.
     * 
     * Use for existing transfers, for adding a new transfer,
     * use static {@see add_new()} method beforehand.
     * 
     * @param int $transfer_id ID from the transfer table.
     * @throws transfer_manager_exception If record with given ID does not exist.
     * @throws configuration_exception If remote address and/or token is missing.
     */
    public function __construct(int $transfer_id) {
        global $DB, $CFG;

        if (!$DB->record_exists('local_remotebp_transfer', ['id' => $transfer_id])) {
            throw new transfer_manager_exception(transfer_manager_exception::CODE_RECORD_DOES_NOT_EXIST);
        }

        $this->transfer = $DB->get_record('local_remotebp_transfer', ['id' => $transfer_id]);
        $this->remote = $DB->get_record('local_remotebp_remotes', ['id' => $this->transfer->remoteid]);

        if (empty($this->remote->address)) {
            $this->change_status('conf_noremote', null, self::STATUS_ERROR);
            throw new configuration_exception(configuration_exception::CODE_NO_ADDRESS);
        }

        if (empty($this->remote->token)) {
            $this->change_status('conf_notoken', null, self::STATUS_ERROR);
            throw new configuration_exception(configuration_exception::CODE_NO_TOKEN);
        }
    }

    public function __get($name)
    {
        if ($name === 'transfer') {
            return clone $this->transfer;
        }

        if ($name === 'remote') {
            return clone $this->remote;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    /**
     * Creates a course backup on the remote instalation.
     * 
     * @return bool True on success, False on failure.
     */
    public function backup_on_remote() {
        global $DB;

        $this->change_status('backup_started', null, self::STATUS_PROCESSING);

        $url = sprintf(self::URL_BASE_FORMAT, $this->remote->address, $this->remote->token) . self::URL_PARAMS_BACKUP;

        // Check user defined in transfer.
        if (!$DB->record_exists('user', ['id' => $this->transfer->userid])) {
            // Log error and return failure.
            $this->change_status('backup_usernotfound', (string)$this->transfer->userid, self::STATUS_ERROR);
            throw new \Exception();
        }

        // Finally get user defined in transfer.
        $user = $DB->get_record('user', ['id' => $this->transfer->userid], 'username');

        // Backup course on the remote.
        $params = array('id' => $this->transfer->remotecourseid, 'username' => $user->username);
        $curl = new \curl;
        $post_data = $curl->post($url, $params);

        // Process response.
        // Check returned HTTP status code.
        if ($curl->info['http_code'] != 200) {
            // Log error and return failure.
            $this->change_status('backup_invalidhttpcode', (string)$curl->info['http_code'], self::STATUS_ERROR);
            throw new \Exception();
        }
        // Get url of the backup file.
        $resp = json_decode($post_data);
        $backup_url = $resp->url;

        // Check if url starts with remote's base url
        if (0 !== strpos($backup_url, $this->remote->address)) {
            // Log error and return failure.
            $this->change_status('backup_invalidurlstart', (string)$backup_url, self::STATUS_ERROR);
            throw new \Exception();
        }

        // Remove hostname from the front to ease the database.
        $backup_url = substr($backup_url, strlen($this->remote->address));

        // Save data to the database.
        $transfer_data = (object) [
            'id' => $this->transfer->id,
            'remotebackupurl' => $backup_url,
        ];
        $DB->update_record('local_remotebp_transfer', $transfer_data);
        $this->transfer->remotebackupurl = $backup_url;

        $this->change_status('backup_ended', null, self::STATUS_PROCESSING);

        return true;
    }

    /**
     * Transfers backup file from remote.
     * Backup file is created using the {@see transfer_manager::backup_on_remote()} method
     * 
     * @return True on success, False on failure.
     */
    public function transfer_backup() {
        $this->change_status('transfer_started', null, self::STATUS_PROCESSING);

        if ($this->transfer->remotebackupurl === null) {
            $this->change_status('transfer_missingurl', null, self::STATUS_ERROR);
            throw new \Exception();
        }

        $context = \context_system::instance();
        $fs = get_file_storage();
        
        // Import the backup file.
        $datetime = new \DateTime();
        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'local_remote_backup_provider',
            'filearea' => 'transfer',
            'itemid' => $this->transfer->id,
            'filepath' => '/',
            'filename' => 'transfer.mbz',
            'timecreated' => $datetime->getTimestamp(),
            'timemodified' => $datetime->getTimestamp(),
        );
        try {
            $fs->create_file_from_url($filerecord,
                    $this->remote->address . $this->transfer->remotebackupurl . '?token=' . $this->remote->token, null, true);
        } catch (\Exception $e) {
            $this->change_status('transfer_failedfilecreation', (string)$e, self::STATUS_PROCESSING);
            throw $e;
        }
        
        $this->change_status('transfer_ended', null, self::STATUS_PROCESSING);
        return true;
    }

    /**
     * Restores file to a new course.
     * 
     * @throws transfer_manager_exception on failure
     */
    public function restore() {
        global $DB, $CFG, $SITE, $USER;
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        
        $this->change_status('restore_started', null, self::STATUS_PROCESSING);

        $context = \context_system::instance();
        $fs = get_file_storage();

        $fileinfo = array(
            'contextid' => $context->id,
            'component' => 'local_remote_backup_provider',
            'filearea' => 'transfer',
            'itemid' => $this->transfer->id,
            'filepath' => '/',
            'filename' => 'transfer.mbz',
        );

        $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
                $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);


        // Extract the file.
        $packer = get_file_packer('application/vnd.moodle.backup');
        $backupid = \restore_controller::get_tempdir_name($SITE->id, $USER->id);
        $path = "$CFG->tempdir/backup/$backupid/";
        if (!$packer->extract_to_pathname($file, $path)) {
            $this->change_status('restore_invalidfile', null, self::STATUS_ERROR);
            throw new transfer_manager_exception(transfer_manager_exception::CODE_RESTORE_INVALID_BACKUP_FILE);
        }

        // Transaction.
        $transaction = $DB->start_delegated_transaction();
    
        // Create new course.
        $folder             = $backupid; // as found in: $CFG->dataroot . '/temp/backup/' 
        $categoryid         = 1;//$options['categoryid']; // e.g. 1 == Miscellaneous
        $userdoingrestore   = $USER->id;
        $courseid           = \restore_dbops::create_new_course('', '', $categoryid);
    
        // Restore backup into course.
        $controller = new \restore_controller($folder, $courseid, 
        \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, $userdoingrestore,
        \backup::TARGET_NEW_COURSE);
    
        if ($controller->execute_precheck()) {
            $controller->execute_plan();
        } else {
            try {
                $transaction->rollback(new \Exception('Prechecked failed'));
            } catch (\Exception $e) {
                $this->change_status('restore_prechecksfailed', null, self::STATUS_ERROR);
                throw new transfer_manager_exception(transfer_manager_exception::CODE_RESTORE_PRECHECK_FAILED);
            }
        }

        // Commit and clean up.
        $transaction->allow_commit();
        unset($transaction);
        $controller->destroy();
        unset($controller);

        // Save data to the database.
        $transfer_data = (object) [
            'id' => $this->transfer->id,
            'courseid' => $courseid,
        ];
        $DB->update_record('local_remotebp_transfer', $transfer_data);
        $this->transfer->courseid = $courseid;

        $this->change_status('restore_ended', (string)$courseid, self::STATUS_PROCESSING);
        return true;
    }

    /**
     * Changes status for the current transfer.
     * 
     * If the public_status is set, public status is also changed,
     * otherwise only log (admin) status is updated.
     * 
     * @param string $fullstatus New status to be used in the log table.
     * @param string|null $notes Additional information about the status.
     * @param string|null $public_status Public status to be changed to (if not already that status).
     */
    public function change_status(string $fullstatus, ?string $notes = null, ?string $public_status = null) {
        global $DB;
        
        // Get datetime once to prevent having public and private status out of sync.
        $datetime = new \DateTime();

        // Insert status information into the log table.
        $log_data = (object) [
            'transferid' => $this->transfer->id,
            'timemodified' => $datetime->getTimestamp(),
            'status' => $public_status,
            'fullstatus' => $fullstatus,
            'notes' => $notes,
        ];
        $DB->insert_record('local_remotebp_transfer_log', $log_data);

        // If set, change also public status in the transfer table,
        // but make change if and only if the transfer
        // does not already have the given status.
        if ($public_status !== null && $public_status != $this->transfer->status) {
            $transfer_data = (object) [
                'id' => $this->transfer->id,
                'status' => $public_status,
                'timemodified' => $datetime->getTimestamp(),
            ];
            $DB->update_record('local_remotebp_transfer', $transfer_data);

            // Update information in this instance.
            $this->transfer->status = $public_status;
            $this->transfer->timemodified = $datetime->getTimestamp();
        }
    }


    /**
     * Returns URL of the remote.
     * 
     * @return string
     */
    public function get_remote_url() {
        return $this->remote->address;
    }

    /**
     * Enrol user as (editing) teacher
     */
    public function enrol_teacher() {
        global $DB;

        $this->change_status('teacherenrol_started', null, self::STATUS_PROCESSING);

        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $manualenrol = enrol_get_plugin('manual');
        
        $enrolinstance = $DB->get_record('enrol', array('courseid' => $this->transfer->courseid, 'enrol'=>'manual'), '*', MUST_EXIST);

        $manualenrol->enrol_user($enrolinstance, $this->transfer->userid, $teacherrole->id);

        $this->change_status('teacherenrol_ended', null, self::STATUS_PROCESSING);
    }

    /**
     * Putting course into corresponding category.
     */
    public function categorize() {
        $this->change_status('categorization_started', null, self::STATUS_PROCESSING);

        $this->change_status('categorization_gettingremotecatid', null, self::STATUS_PROCESSING);
        $url = sprintf(self::URL_BASE_FORMAT, $this->remote->address, $this->remote->token) . self::URL_PARAMS_CATEGORYID;
        $params = array('id' => $this->transfer->remotecourseid);
        $curl = new \curl;
        $results = json_decode($curl->post($url, $params));


        $category = $this->_get_local_category($results->category);

        move_courses([$this->transfer->courseid], $category);

        $this->change_status('categorization_ended', null, self::STATUS_FINISHED);
    }

    protected function _get_local_category($remotecategoryid) {
        global $DB;

        $this->change_status('categorization_lookingforlocalcat',
                json_encode(['remote' => $this->remote->id, 'remote_category' => $remotecategoryid]),
                self::STATUS_PROCESSING);

        $record = $DB->get_record('local_remotebp_categories', [
                    'remoteid' => $this->remote->id,
                    'remotecategoryid' => $remotecategoryid,
                ], 'categoryid', IGNORE_MULTIPLE);

        // If found, return its local ID
        if ($record) {
            $this->change_status('categorization_catfound',
                    $record->categoryid,
                    self::STATUS_PROCESSING);

            return (int)$record->categoryid;
        }

        // If not, create a new one
        $this->change_status('categorization_remotenotfoundlocally',
                json_encode(['remote' => $this->remote->id, 'remote_category' => $remotecategoryid]),
                self::STATUS_PROCESSING);

        $url = sprintf(self::URL_BASE_FORMAT, $this->remote->address, $this->remote->token) . self::URL_PARAMS_CATEGORYINFO;
        $params = array('id' => $remotecategoryid);
        $curl = new \curl;
        $results = json_decode($curl->post($url, $params));

        $data = new \stdClass();
        
        $this->change_status('categorization_lookingforparent', $results->path, self::STATUS_PROCESSING);
        $path = explode('/', $results->path);
        array_pop($path); // Removing current category from the end.
        $parent = array_pop($path);        
        if ($parent !== '') {
            $data->parent = $this->_get_local_category($parent);
        }

        $data->idnumber = $results->idnumber;
        $data->name = $results->name;
        $data->visible = (int)$results->visible;

        $this->change_status('categorization_creatingnewcat', json_encode($data), self::STATUS_PROCESSING);
        $category = \core_course_category::create($data);

        $this->change_status('categorization_savingforlater', $category->id, self::STATUS_PROCESSING);

        $record = $DB->insert_record('local_remotebp_categories', (object)[
            'remoteid' => $this->remote->id,
            'remotecategoryid' => $remotecategoryid,
            'categoryid' => $category->id,
        ], 'categoryid', IGNORE_MULTIPLE);

        return (int)$category->id;
    }

    /**
     * Check if the timer for transfer already exceeded.
     * 
     * The value can be set in:
     * Site administration > Plugins > Local plugins
     * > Remote backup provider > General settings
     */
    protected function _is_timed_out() {
        $datetime = new \DateTime();
        $max_transfer_time = get_config('local_remote_backup_provider', 'max_transfer_time');
        
        // If max transfer time is set to zero,
        // do not check it at all 
        if ($max_transfer_time == 0) return false;
        
        $transfer_time = $datetime->getTimestamp() - $this->transfer->timecreated;
        return ($transfer_time > $max_transfer_time);
    }

    /**
     * Checks the transfer before executing other parts of the ad-hoc task.
     * 
     * @throws transfer_manager_exception On any failure.
     * @return bool True on success.
     */
    public function adhoc_preflight_check() {
        // If status is Canceled or Finished, do not continue in processing.
        if ($this->transfer->status == self::STATUS_CANCELED
                || $this->transfer->status == self::STATUS_FINISHED) {
            throw new transfer_manager_exception(transfer_manager_exception::CODE_PREFLIGHT_FAILED);
        }

        // Checks whether the ad-hoc timeout exceeded. See _is_timed_out() method for more information.
        if ($this->_is_timed_out()) {
            $this->change_status('cancelled_timeout', null, self::STATUS_CANCELED);
            throw new transfer_manager_exception(transfer_manager_exception::CODE_PREFLIGHT_FAILED);
        }
    }

    /**
     * Executes manual cancelation of the course.
     */
    public function cancel_manually() {
        global $USER;
        $this->change_status('cancelled_manually', $USER->id, self::STATUS_CANCELED);
    }
}