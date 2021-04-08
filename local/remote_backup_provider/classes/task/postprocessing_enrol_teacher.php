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
 * Ad hoc (immediate) task to enrol teacher to the newly created course.
 *
 * @package    local_remote_backup_provider
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class postprocessing_enrol_teacher extends \core\task\adhoc_task {
    /**
     * Get the name of the task.
     *
     * @return string the name of the task
     */
    public function get_name() {
        return get_string('enrol_teacher_task', 'local_remote_backup_provider');
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

        mtrace('Call for enroling teacher.');
        $transfer_manager->enrol_teacher();

        mtrace('Finish.');
        return true;
    }
}
