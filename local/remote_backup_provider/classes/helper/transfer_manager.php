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
     * Remote data.
     * 
     * @var stdClass
     */
    private $remote;

    public function __construct($remote) {
        $this->remote = $remote;
        if (empty($this->remote->address)) {
            throw new configuration_exception(configuration_exception::CODE_NO_ADDRESS);
        }

        if (empty($this->remote->token)) {
            throw new configuration_exception(configuration_exception::CODE_NO_TOKEN);
        }
    }

    /**
     * Looks for the courses containing given string in their name or short name on the remote.
     * 
     * @param string $search String to be searched for.
     */
    public function search($search) {
        global $USER;
        
        $url = sprintf(self::URL_BASE_FORMAT, $this->remote->address, $this->remote->token) . self::URL_PARAMS_SEARCH;
        $params = array('search' => $search, 'username' => $USER->username, 'auth' => $USER->auth);
        $curl = new \curl;
        $results = json_decode($curl->post($url, $params));
        return $results;
    }

    /**
     * Downloads a backup file from the remote and stores it.
     * 
     * @param int $remote_course_id ID of the course on the remote server.
     * @param \context_system $context Context instance.
     * 
     * @return \stored_file
     */
    public function backup_from_remote($remote_course_id, \context_system $context) {
        global $USER;

        $fs = get_file_storage();
        $url = sprintf(self::URL_BASE_FORMAT, $this->remote->address, $this->remote->token) . self::URL_PARAMS_BACKUP;
        $params = array('id' => $remote_course_id, 'username' => $USER->username);
        $curl = new \curl;
        $resp = json_decode($curl->post($url, $params));

        // Import the backup file.
        $timestamp = time();
        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'local_remote_backup_provider',
            'filearea' => 'backup',
            'itemid' => $timestamp,
            'filepath' => '/',
            'filename' => 'foo',
            'timecreated' => $timestamp,
            'timemodified' => $timestamp,
        );
        return $fs->create_file_from_url($filerecord, $resp->url . '?token=' . $this->remote->token, null, true);
    }

    /**
     * Restores file from an archive.
     * 
     * @param stored_file $file File instance to be restored.
     * @return int Output course ID.
     * 
     * @throws transfer_manager_exception on failure
     */
    public function restore(\stored_file $file) {
        global $DB, $CFG, $SITE, $USER;
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        
        // Extract the file.
        $packer = get_file_packer('application/vnd.moodle.backup');
        $backupid = \restore_controller::get_tempdir_name($SITE->id, $USER->id);
        $path = "$CFG->tempdir/backup/$backupid/";
        if (!$packer->extract_to_pathname($file, $path)) {
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
                unset($transaction);
                $controller->destroy();
                unset($controller);
                throw new transfer_manager_exception(transfer_manager_exception::CODE_RESTORE_PRECHECK_FAILED);
            }
        }

        // Commit and clean up.
        $transaction->allow_commit();
        unset($transaction);
        $controller->destroy();
        unset($controller);
        return (int)$courseid;
    }

    /**
     * Returns URL of the remote.
     * 
     * @return string
     */
    public function get_remote_url() {
        return $this->remote->address;
    }
}