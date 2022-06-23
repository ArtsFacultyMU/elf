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

use local_remote_backup_provider\helper\subtransfer\post_subtransfer_manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Ad hoc (immediate) task to enrol teacher to the newly created course.
 *
 * @package    local_remote_backup_provider
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class run_subtransfers_post extends \core\task\adhoc_task {
    /**
     * Get the name of the task.
     *
     * @return string the name of the task
     */
    public function get_name() {
        return get_string('run_subtransfers_post_task', 'local_remote_backup_provider');
    }

    /**
     * Enrols teacher to the course.
     *
     * @return bool Always returns true
     */
    public function execute() {
        mtrace('Get data from task.');
        $data = $this->get_custom_data();

        mtrace('Start transfer manager.');
        $transfer_manager = new \local_remote_backup_provider\helper\transfer_manager($data->transfer_id);

        mtrace('Check the preflight.');
        try {
            $transfer_manager->adhoc_preflight_check();
        } catch (\local_remote_backup_provider\exception\transfer_manager_exception $e) {
            return true;
        }

        mtrace('Call for the subprocesses themselves.');
        $subplugins = post_subtransfer_manager::get_subplugins();
        foreach ($subplugins as $subplugin) {
            $subtransfer_id = post_subtransfer_manager::add_new((int)$data->transfer_id, $subplugin);
            $subtransfer_task_name = '\remotebppost_' . $subplugin . '\task\main';
            $subtransfer_task = new $subtransfer_task_name();
            $subtransfer_task->set_custom_data(array(
                'subtransfer_id' => $subtransfer_id,
            ));
            \core\task\manager::queue_adhoc_task($subtransfer_task);
        }

        mtrace('Finish.');
        return true;
    }
}
