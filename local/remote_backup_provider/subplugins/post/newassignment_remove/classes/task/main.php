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

namespace remotebppost_newassignment_remove\task;

use local_remote_backup_provider\helper\subtransfer\post_subtransfer_manager;
use local_remote_backup_provider\helper\transfer_manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Ad hoc (immediate) task to create backup on the remote.
 *
 * @package    remotebppost_newassignment_remove
 * @copyright  2022 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main extends \core\task\adhoc_task {
    /**
     * Get the name of the task.
     *
     * @return string the name of the task
     */
    public function get_name() {
        return get_string('task_main', 'remotebppost_newassignment_remove');
    }

    /**
     * Generates backup on the remote and saves url
     *
     * @return bool Always returns true
     */
    public function execute() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/course/modlib.php');

        mtrace('Get data from task.');
        $data = $this->get_custom_data();

        mtrace('Start subtransfer manager.');
        $subtransfer_manager = new post_subtransfer_manager($data->subtransfer_id);
        $subtransfer_manager->change_status('started', null, transfer_manager::STATUS_PROCESSING);

        mtrace('Check the preflight.');
        try {
            $subtransfer_manager->get_transfer_manager()->adhoc_preflight_check();
        } catch (\local_remote_backup_provider\exception\transfer_manager_exception $e) {
            $subtransfer_manager->change_status('stopped_on_preflight', null, transfer_manager::STATUS_CANCELED);
            return true;
        }

        mtrace('Get course instance.');
        $course = $DB->get_record('course', 
                array('id' => $subtransfer_manager->get_transfer_manager()->transfer->courseid),
                '*', MUST_EXIST);
        
        mtrace('Run the newassignment conversion process.');
        // Get all instances of mod_newassignment to be looped over.
        $subtransfer_manager->change_status('getting_newassignments', null, transfer_manager::STATUS_PROCESSING);
        $newassignments = get_all_instances_in_course('newassignment', $course);
        
        if ($newassignments) {
            // Convert newassignment instance to assignment instances.
            $subtransfer_manager->change_status('processing_newassignments', null, transfer_manager::STATUS_PROCESSING);
            foreach ($newassignments as $newassignment) {
                // Create assign instance.
                \local_newassignment_remover\converter::convert($course, $newassignment);
                // Remove obsolete newassignment instance.
                course_delete_module($newassignment->coursemodule);
            }
            $subtransfer_manager->change_status('purging_caches', null, transfer_manager::STATUS_PROCESSING);
            purge_caches();
            $subtransfer_manager->change_status('processing_newassignments_ended', null, transfer_manager::STATUS_PROCESSING);
        } else {
            $subtransfer_manager->change_status('skipped_no_instances', null, transfer_manager::STATUS_PROCESSING);
        }

        $subtransfer_manager->change_status('finished', null, transfer_manager::STATUS_FINISHED);

        mtrace('Check the sibling subtransfers status.');
        $subtransfer_manager->finish_transfer_if_all_subtransfers_finished();

        mtrace('Finish.');
        return true;
    }
}