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
 * Ad hoc (immediate) task to create backup on the remote.
 *
 * @package    local_remote_backup_provider
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class transfer_create_backup extends \core\task\adhoc_task {
    /**
     * Get the name of the task.
     *
     * @return string the name of the task
     */
    public function get_name() {
        return get_string('create_backup_task', 'local_remote_backup_provider');
    }

    /**
     * Generates backup on the remote and saves url
     *
     * @return bool Always returns true
     */
    public function execute() {
        mtrace('Get data from task.');
        $data = $this->get_custom_data();

        mtrace('Start transfer manager.');
        $transfer_manager = new \local_remote_backup_provider\helper\transfer_manager($data->transfer_id);

        mtrace('Call for backup.');
        $transfer_manager->backup_on_remote();

        mtrace('Queue transfer task.');
        $transfer_backup_task = new transfer_transfer_backup();
        $transfer_backup_task->set_custom_data(array(
            'transfer_id' => $data->transfer_id,
        ));
        \core\task\manager::queue_adhoc_task($transfer_backup_task);

        mtrace('Finish.');
        return true;
    }
}
