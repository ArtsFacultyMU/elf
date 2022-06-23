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

namespace remotebppost_categorize\task;

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
        global $CFG;
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
        
        $subtransfer_manager->change_status('categorization_gettingremotecatid', null, transfer_manager::STATUS_PROCESSING);
        
        $results = $subtransfer_manager->call_external_function('get_category_by_course_id',
                ['id' => $transfer_manager->transfer->remotecourseid]);
        
        $category = $this->_get_local_category($subtransfer_manager, $results->category);
        

        $subtransfer_manager->change_status('categorization_started', null, transfer_manager::STATUS_PROCESSING);
        move_courses([(int)$transfer_manager->transfer->courseid], $category);
        $subtransfer_manager->change_status('categorization_ended', null, transfer_manager::STATUS_PROCESSING);

        $subtransfer_manager->change_status('finished', null, transfer_manager::STATUS_FINISHED);

        mtrace('Check the sibling subtransfers status.');
        $subtransfer_manager->finish_transfer_if_all_subtransfers_finished();

        mtrace('Finish.');
        return true;
    }

    protected function _get_local_category($subtransfer_manager, $remotecategoryid) {
        global $DB;

        $transfer_manager = $subtransfer_manager->get_transfer_manager();

        $subtransfer_manager->change_status('categorization_lookingforlocalcat',
                json_encode(['remote' => $transfer_manager->remote->id, 'remote_category' => $remotecategoryid]),
                transfer_manager::STATUS_PROCESSING);
        $record = $DB->get_record('remotebppost_categorize', [
                    'remoteid' => $transfer_manager->remote->id,
                    'remotecategoryid' => $remotecategoryid,
                ], 'categoryid', IGNORE_MULTIPLE);

        // If found, return its local ID
        if ($record) {
            $subtransfer_manager->change_status('categorization_catfound',
                    $record->categoryid,
                    transfer_manager::STATUS_PROCESSING);
            return (int)$record->categoryid;
        }

        // If not, create a new one
        $subtransfer_manager->change_status('categorization_remotenotfoundlocally',
                json_encode(['remote' => $transfer_manager->remote->id, 'remote_category' => $remotecategoryid]),
                transfer_manager::STATUS_PROCESSING);

        $results = $subtransfer_manager->call_external_function('get_category_info',
        ['id' => $remotecategoryid]);
        $data = new \stdClass();
        
        $subtransfer_manager->change_status('categorization_lookingforparent', $results->path, transfer_manager::STATUS_PROCESSING);
        $path = explode('/', $results->path);
        array_pop($path); // Removing current category from the end.
        $parent = array_pop($path);        
        if ($parent !== '') {
            $data->parent = $this->_get_local_category($subtransfer_manager, $parent);
        }

        $data->idnumber = $results->idnumber;
        $data->name = $results->name;
        $data->visible = (int)$results->visible;
        
        // Looking for the category with the same name
        $category = $DB->get_record('course_categories', ['parent' => $data->parent ?? 0, 'name' => $results->name], '*', IGNORE_MULTIPLE);
        if (!$category) {
            // Creating new category
            $subtransfer_manager->change_status('categorization_creatingnewcat', json_encode($data), transfer_manager::STATUS_PROCESSING);
            $category = \core_course_category::create($data);
            }

        $subtransfer_manager->change_status('categorization_savingforlater', $category->id, transfer_manager::STATUS_PROCESSING);
        $record = $DB->insert_record('remotebppost_categorize', (object)[
            'remoteid' => $transfer_manager->remote->id,
            'remotecategoryid' => $remotecategoryid,
            'categoryid' => $category->id,
        ], 'categoryid', IGNORE_MULTIPLE);

        return (int)$category->id;
    }
}