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

namespace remotebppost_glossary_datatransfer\task;

use local_remote_backup_provider\helper\subtransfer\post_subtransfer_manager;
use local_remote_backup_provider\helper\transfer_manager;
use remotebppost_glossary_datatransfer\helper;

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
        require_once($CFG->dirroot . '/course/lib.php');

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

        mtrace('Run the categorization process.');
        
        
        $course = $DB->get_record('course', 
                array('id' => $transfer_manager->transfer->courseid),
                '*', MUST_EXIST);

        $subtransfer_manager->change_status('getting_instances', null, transfer_manager::STATUS_PROCESSING);
        $instances = get_all_instances_in_course('glossary', $course);

        if (!$instances) {
            $subtransfer_manager->change_status('skipped_no_instances', null, transfer_manager::STATUS_PROCESSING);
        } else {
            $subtransfer_manager->change_status('fetching_remote', null, transfer_manager::STATUS_PROCESSING);
            $results = $subtransfer_manager->call_external_function('get_glossaries_data',
                ['course' => $transfer_manager->transfer->remotecourseid]);
            $subtransfer_manager->change_status('fetching_remote_ended', null, transfer_manager::STATUS_PROCESSING);

            $xmlstrings = $results->glossaries;

            $subtransfer_manager->change_status('filling_instances', null, transfer_manager::STATUS_PROCESSING);
            foreach ($instances as $instance) {
                $glossary = $DB->get_record('glossary', array('id'=>$instance->id));
                $context = \context_module::instance($instance->coursemodule);
                $xmlstring = base64_decode(array_shift($xmlstrings));
                helper::import_data($glossary, $context, $xmlstring, $transfer_manager->transfer->userid);
            }
            $subtransfer_manager->change_status('filling_instances_ended', null, transfer_manager::STATUS_PROCESSING);
        }
        
        $subtransfer_manager->change_status('finished', null, transfer_manager::STATUS_FINISHED);

        mtrace('Check the sibling subtransfers status.');
        $subtransfer_manager->finish_transfer_if_all_subtransfers_finished();

        mtrace('Finish.');
        return true;
    }
}