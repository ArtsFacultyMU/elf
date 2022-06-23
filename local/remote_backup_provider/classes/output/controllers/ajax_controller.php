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

namespace local_remote_backup_provider\output\controllers;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../../config.php');

/**
 * Controller for AJAX calls.
 *
 * @package   local_remote_backup_provider
 * @copyright 2021 Masaryk University
 * @author    Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ajax_controller {
    /**
     * Displays list of remotes.
     */
    public function findUsersAction() {
        global $DB;
        header('Content-Type: text/json;charset=UTF-8');

        $search = optional_param('search', '', PARAM_NOTAGS);
        $context = \context_system::instance();
        if (!has_capability('local/remote_backup_provider:transferasother', $context)) {
            echo '[]';
            exit;
        }

        if (mb_strlen($search,'UTF-8')<3) {
            echo '[]';
            exit;
        }

        $conditions = [
            'email LIKE ?',
            'CONCAT(firstname, " ", lastname) LIKE ?',
            'CONCAT(lastname, " ", firstname) LIKE ?',
        ];
        $records = $DB->get_records_select(
            'user',
            '(' . implode(' OR ', $conditions) .') AND suspended=0 AND deleted=0 AND confirmed=1',
            ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%',],
            '',
            'id, firstname, lastname, email',
            0,
            5
        );

        $output = [];
        foreach ($records as $record) {$output[] = $record;}
        echo json_encode($output);
        exit;
    }
}