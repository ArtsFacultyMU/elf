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
 * Settings for local_remote_backup_provider
 *
 * @package    local_remote_backup_provider
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $category = new admin_category('local-remote_backup_provider', get_string('pluginname', 'local_remote_backup_provider'));
    $ADMIN->add('localplugins', $category);

    $settings = new admin_externalpage('local-remote_backup_provider-remote_list', get_string('admin_remote_list', 'local_remote_backup_provider'), new moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_remote_list']));
    $ADMIN->add('local-remote_backup_provider', $settings);

    $settings = new admin_externalpage('local-remote_backup_provider-remote_edit', get_string('admin_remote_add', 'local_remote_backup_provider'), new moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_remote_edit']));
    $ADMIN->add('local-remote_backup_provider', $settings);

    $settings = new admin_externalpage('local-remote_backup_provider-transfer_log', get_string('admin_transfer_log', 'local_remote_backup_provider'), new moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_transfer_log']));
    $ADMIN->add('local-remote_backup_provider', $settings);

    $settings = new admin_externalpage('local-remote_backup_provider-detailed_log', get_string('admin_detailed_log', 'local_remote_backup_provider'), new moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_detailed_log']), 'moodle/site:config', true);
    $ADMIN->add('local-remote_backup_provider', $settings);
}
