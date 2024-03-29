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
 * Task which cleans up old backup files.
 *
 * @package    local_remote_backup_provider
 * @copyright  2018 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_remote_backup_provider\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task (cron task) that removes old remote backup files.
 *
 * @package    local_remote_backup_provider
 * @copyright  2018 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remove_old extends \core\task\scheduled_task {
    const URL_BASE_FORMAT = '%s/webservice/rest/server.php?wstoken=%s&moodlewsrestformat=json';
    const URL_PARAMS_BACKUPDELETE = '&wsfunction=local_remote_backup_provider_delete_course_backup';

    /**
     * Get the name of the task.
     *
     * @return string the name of the task
     */
    public function get_name() {
        return get_string('remove_old_task', 'local_remote_backup_provider');
    }

    /**
     * Find and remove expired backup files generated by this plugin.
     *
     * @return bool Always returns true
     */
    public function execute() {
        global $DB;
        mtrace('Deleting old remote backup files.');

        $records = $DB->get_records_select('local_remotebp_transfer', '(status = ? OR status = ?) AND remotebackupurl IS NOT NULL',
                [\local_remote_backup_provider\helper\transfer_manager::STATUS_FINISHED, \local_remote_backup_provider\helper\transfer_manager::STATUS_CANCELED]);

        mtrace('Found ' . count($records) . ' record(s).');

        $counter = 0;

        foreach($records as $record) {
            $counter++;
            mtrace('Processing ' . $counter . '/' . count($records) . '.');
            $remotebackupurl_bits = array_reverse(explode('/', $record->remotebackupurl));
            if (count($remotebackupurl_bits) < 2) {
                mtrace('Backupurl invalid.');
                continue;
            }

            $remote = (new \local_remote_backup_provider\helper\remote_manager())->getRemote($record->remoteid);
            $timestamp = $remotebackupurl_bits[1];

            $url = sprintf(self::URL_BASE_FORMAT, $remote->address, $remote->token) . self::URL_PARAMS_BACKUPDELETE;
            $params = array('id' => $record->remotecourseid, 'timestamp' => $timestamp);
            $curl = new \curl;
            $results = json_decode($curl->post($url, $params));

            if ($results) {
                $context = \context_system::instance();
                $fs = get_file_storage();

                $fileinfo = array(
                    'contextid' => $context->id,
                    'component' => 'local_remote_backup_provider',
                    'filearea' => 'transfer',
                    'itemid' => $record->id,
                    'filepath' => '/',
                    'filename' => 'transfer.mbz',
                );

                $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
                        $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);

                if ($file) {
                    $file->delete();
                }

                $DB->update_record('local_remotebp_transfer', ['id'=> $record->id, 'remotebackupurl' => NULL]);

                mtrace('Success.');
                continue;
            }
            mtrace('Failed on remote file.');
        }
        mtrace('Deleting old remote backup files ended.');
        mtrace('Deleting old local backup files.');

        $records = $DB->get_records('files', ['component' => 'local_remote_backup_provider', 'filename' => '.']);
        mtrace('Deleting invalid files (found ' . count($records)  . ' records).');
        $count = $this->delete_file_records($records);
        mtrace('Deleted ' . $count . ' files');

        $interval = new \DateInterval('P3D');
        $datetime = new \DateTime();
        $datetime->sub($interval);

        $records = $DB->get_records_select('files', 'component=? AND timecreated<?', ['local_remote_backup_provider', $datetime->getTimestamp()]);
        mtrace('Deleting old files (found ' . count($records)  . ' records).');
        $count = $this->delete_file_records($records);
        mtrace('Deleted ' . $count . ' files');

        mtrace('Deleting old local backup files ended.');
        return true;
    }

    private function delete_file_records($records) {
        $deleted_counter = 0;
        $filestorage = get_file_storage();
        foreach ($records as $record) {
            $file = $filestorage->get_file(
                $record->contextid,
                $record->component,
                $record->filearea,
                $record->itemid,
                $record->filepath,
                $record->filename
            );
            if ($file) {
                $file->delete();
                $deleted_counter++;
            }
        }
        return $deleted_counter;
    }
}
