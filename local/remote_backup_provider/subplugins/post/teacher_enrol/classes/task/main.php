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

namespace remotebppost_teacher_enrol\task;

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
        return get_string('task_main', 'remotebppost_teacher_enrol');
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

        mtrace('Run the teacher enrolment process.');
        $subtransfer_manager->change_status('enroling_teacher', null, transfer_manager::STATUS_PROCESSING);
       
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $manualenrol = enrol_get_plugin('manual');
        
        $enrolinstance = $DB->get_record('enrol', array('courseid' => $subtransfer_manager->get_transfer_manager()->transfer->courseid, 'enrol'=>'manual'), '*', MUST_EXIST);

        $manualenrol->enrol_user($enrolinstance, $subtransfer_manager->get_transfer_manager()->transfer->userid, $teacherrole->id);
        $subtransfer_manager->change_status('enroling_teacher_ended', null, transfer_manager::STATUS_PROCESSING);

        
        $subtransfer_manager->change_status('finished', null, transfer_manager::STATUS_FINISHED);

        mtrace('Check the sibling subtransfers status.');
        $subtransfer_manager->finish_transfer_if_all_subtransfers_finished();

        mtrace('Finish.');
        return true;
    }
}