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

use stdClass;
use local_remote_backup_provider\exception\access_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Manages available remotes.
 *
 * @package    local_remote_backup_provider
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remote_manager {
    public function getRemote(int $id, bool $active_only = true) {
        global $DB;

        $remote = $DB->get_record('local_remotebp_remotes', ['id' => $id]);

        // If remote wasn't found, throw a not found exception.
        if (!$remote) {
            throw new access_exception(access_exception::CODE_REMOTE_NOT_FOUND);
        }

        // If parameter active_only is set to true but remote is not active, throw a not found exception. 
        if ($active_only && !$remote->active) {
            throw new access_exception(access_exception::CODE_REMOTE_NOT_FOUND);
        }

        return $remote;
    }

    /**
     * Adds a new remote. It is added as hidden by default.
     * 
     * @param stdClass $remote Database data for the remote.
     * @return int ID of the newly added remote
     */
    public function addRemote(stdClass $remote) : int {
        global $DB;

        // Set to hidden.
        $remote->active = 0;

        // Set position to the highest (meaning last highest + 1).
        $position_array = $DB->get_records('local_remotebp_remotes', null, 'position', 'position');
        $last_position = array_pop($position_array);
        $remote->position = 0;
        if ($last_position !== null) $remote->position = (int)$last_position->position + 1;

        // Normalize address
        $remote->address = rtrim(trim($remote->address), '/\\');

        // Remove ID if set.
        unset($remote->id);

        // Insert into database and return new ID.
        return $DB->insert_record('local_remotebp_remotes', $remote);
    }

    public function editRemote(stdClass $remote) {
        global $DB;

        // Normalize address.
        $remote->address = rtrim(trim($remote->address), '/\\');

        // Insert into database and return new ID.
        $DB->update_record('local_remotebp_remotes', $remote);
    }

    /**
     * Returns list of remotes ordered by their position.
     * 
     * @param bool $active_only If true (default) filters out hidden remotes.
     * @return array Array of stdClasses covering the remotes.
     */
    public function getRemotes(bool $active_only = true) : array {
        global $DB;

        // Fetch remotes from database.
        $remotes = $DB->get_records('local_remotebp_remotes', null, 'position');
        // If active_only variable is true, filter out hidden remotes. 
        if ($active_only) {
            $remotes = array_filter($remotes, function($r) {
                return ($r->active == 1);
            });
        }

        // Return list.
        return $remotes;
    }
}