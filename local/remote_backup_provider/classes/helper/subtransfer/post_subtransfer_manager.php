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

namespace local_remote_backup_provider\helper\subtransfer;

use local_remote_backup_provider\helper\subtransfer_manager_abstract;
use local_remote_backup_provider\helper\transfer_manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Manages available remotes.
 *
 * @package    local_remote_backup_provider
 * @copyright  2022 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_subtransfer_manager extends subtransfer_manager_abstract {
    const URL_PARAMS_EXTERNAL = '&wsfunction=local_remote_backup_provider_process_subplugin_post';

    public static function get_type() : string {return 'remotebppost';}

    /**
     * Sets the status of the parent transfer to "finished"
     * if all of its (post) submodules are finished.
     * 
     * @return bool True if all subtransfers finished, false otherwise.
     */
    public function finish_transfer_if_all_subtransfers_finished() {
        // Check status of all sibling subtransfers, whether it is "finished".
        foreach ($this->get_sibling_subtransfers() as $st) {
            if ($st->get_status() !== transfer_manager::STATUS_FINISHED) {
                return false;
            }
        }

        // Change the status of the main transfer.
        $this->get_transfer_manager()->change_status('post_subtransfers_finished',
                null, transfer_manager::STATUS_FINISHED);
        return true;
    }

    public function call_external_function($function, $data) {
        $transfer_manager = $this->get_transfer_manager();
        $plugin_raw = substr($this->plugin, strlen(self::get_type()) + 1);

        $url = sprintf(transfer_manager::URL_BASE_FORMAT, 
                $transfer_manager->remote->address, 
                $transfer_manager->remote->token) . 
                self::URL_PARAMS_EXTERNAL;
        $params = [
            'subplugin' => $plugin_raw,
            'function' => $function,
            'data' => json_encode($data)
        ];
        $curl = new \curl;
        $results = json_decode($curl->post($url, $params));
        
        if (!isset($results->data)) {
            throw new \moodle_exception('There was a problem calling external function:'
                    . "\n" . var_export($results, true));
        }

        return json_decode($results->data);
    }
}