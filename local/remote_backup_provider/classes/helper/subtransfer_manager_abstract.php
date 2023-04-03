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

use local_remote_backup_provider\exception\transfer_manager_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Manages available remotes.
 *
 * @package    local_remote_backup_provider
 * @copyright  2022 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class subtransfer_manager_abstract {
    protected $subtransferid;

    protected $transferid;
    protected $plugin;
    protected $settings;
    protected $timemodified;
    protected $status;

    /**
     * Prefix of the given plugin type.
     */
    abstract public static function get_type() : string;

    /**
     * Adds new subplugin instance.
     * 
     * @param int $transferid ID of the parent transfer.
     * @param string $plugin Name of the plugin (w/o. the type part).
     * @param mixed $settings Additional settings to the submodule instance.
     * 
     * @return int ID of the new subtransfer.
     */
    public static function add_new(int $transferid, string $plugin, $settings = null) : int {
        global $DB;
        
        // Get current time (to have the same in log & main database table).
        $datetime = new \DateTime();
        
        // Insert information into subtransfer database table.
        $subtransfer_data = (object) [
            'transferid' => $transferid,
            'plugin' => static::get_type() . '_' . $plugin,
            'settings' => json_encode($settings),
            'timemodified' => $datetime->getTimestamp(),
            'status' => transfer_manager::STATUS_ADDED,
        ];
        
        $subtransferid = $DB->insert_record('local_remotebp_subtransfer', $subtransfer_data);

        $transfer_manager = new transfer_manager($transferid);
        $transfer_manager->change_status('added', null, transfer_manager::STATUS_ADDED, 
                (int)$subtransferid, $datetime);

        return (int)$subtransferid;
    }

    /**
     * Gets list of subplugins of the current type.
     */
    public static function get_subplugins() {
        return array_keys(\core_component::get_plugin_list(static::get_type()));
    }

    /**
     * Subtransfer instance.
     * 
     * @param int $subtransferid ID of the subtransfer.
     */
    public function __construct(int $subtransferid) {
        global $DB;

        // Check if the record exists.
        if (!$DB->record_exists('local_remotebp_subtransfer', ['id' => $subtransferid])) {
            throw new transfer_manager_exception(transfer_manager_exception::CODE_RECORD_DOES_NOT_EXIST);
        }

        // Fetch data from database and save them to the respective variables.
        $db_data = $DB->get_record('local_remotebp_subtransfer', ['id' => $subtransferid]);
        $this->transferid = (int)$db_data->transferid;
        $this->plugin = $db_data->plugin;
        $this->settings = json_decode($db_data->settings);
        $time = new \DateTime();
        $time->setTimestamp((int)$db_data->timemodified);
        $this->timemodified = $time;
        $this->status = $db_data->status;

        // Save the subtransfer id.
        $this->subtransferid = $subtransferid;
    }

    /**
     * Returns the transfer manager instance of the parent transfer.
     * 
     * @return transfer_manager
     */
    public function get_transfer_manager() {
        return new transfer_manager($this->transferid);
    }

    /**
     * Changes status of the current subtransfer.
     * 
     * @param string $fullstatus New status to be used in the log table.
     * @param string|null $notes Additional information about the status.
     * @param string|null $public_status Public status to be changed to (if not already that status).
     */
    public function change_status(string $fullstatus, ?string $notes = null, ?string $public_status = null) {
        global $DB;

        $datetime = new \DateTime();

        $this->get_transfer_manager()->change_status($fullstatus, $notes, 
                $public_status, $this->subtransferid, $datetime);

        // If there is change in public status change it in main submodule table.
        if ($public_status !== null && $public_status != $this->status) {
            $transfer_data = (object) [
                'id' => $this->subtransferid,
                'status' => $public_status,
                'timemodified' => $datetime->getTimestamp(),
            ];
            $DB->update_record('local_remotebp_subtransfer', $transfer_data);

            // Update information in this instance.
            $this->status = $public_status;
            $this->timemodified = $datetime->getTimestamp();
        }
    }

    /**
     * Returns all subtransfers of the same type falling under
     * the same transfer ID.
     * 
     * @return static[]
     */
    public function get_sibling_subtransfers() {
        global $DB;

        $records = $DB->get_records_select('local_remotebp_subtransfer', 
                '`transferid`=:transferid AND `plugin` LIKE CONCAT(:plugin, "_%")',
                ['transferid' => $this->transferid, 'plugin' => static::get_type()]);

        return array_map(function ($r) {
            return new static((int)$r->id);
        }, $records);
    }

    /**
     * Returns current (public) status.
     * 
     * @return string The status.
     */
    public function get_status() {
        return $this->status;
    }
}