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

namespace remotebppost_newassignment_detector\task;

use local_remote_backup_provider\helper\subtransfer\post_subtransfer_manager;
use local_remote_backup_provider\helper\transfer_manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Ad hoc (immediate) task to detect mod_newassignment instances in the remote
 * course.
 *
 * @package    remotebppost_newassignment_detector
 * @copyright  2023 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main extends \core\task\adhoc_task {
    /**
     * In passive mode, the module serves only as an external interface for a
     * different MOODLE instance. In that case, no check is provided and after
     * initial integrity check the module finishes successfully in any case.
     * 
     * @var bool If set to true, no check is provided.
     */
    const PASSIVE = true;

    /**
     * Get the name of the task.
     *
     * @return string the name of the task
     */
    public function get_name() {
        return get_string('task_main', 'remotebppost_newassignment_detector');
    }

    /**
     * Checks the remote installation for a mod_newassignment instances.
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
        $transfer_manager = $subtransfer_manager->get_transfer_manager();

        mtrace('Check the preflight.');
        try {
            $subtransfer_manager->get_transfer_manager()->adhoc_preflight_check();
        } catch (\local_remote_backup_provider\exception\transfer_manager_exception $e) {
            $subtransfer_manager->change_status('stopped_on_preflight', null, transfer_manager::STATUS_CANCELED);
            return true;
        }

        # If not in passive mode, check the remote
        if (!self::PASSIVE) {        
            mtrace('Run the detection.');
            $subtransfer_manager->change_status('checking_newassignment', null, transfer_manager::STATUS_PROCESSING);
        
            $results = $subtransfer_manager->call_external_function('get_newassignment_count',
                    ['course' => $transfer_manager->transfer->remotecourseid]);

            if (!property_exists($results, 'newassignment_count')) {
                $subtransfer_manager->change_status('checking_newassignment_error', null, transfer_manager::STATUS_ERROR);
                return false;
            }

            if ((int)$results->newassignment_count!==0) {
                $subtransfer_manager->change_status('newassignment_found_stopping', json_encode($results->newassignment_count), transfer_manager::STATUS_ERROR);
                return true;
            }
        }

        $subtransfer_manager->change_status('finished', null, transfer_manager::STATUS_FINISHED);

        mtrace('Check the sibling subtransfers status.');
        $subtransfer_manager->finish_transfer_if_all_subtransfers_finished();

        mtrace('Finish.');
        return true;
    }
}
