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
 * Landing page for local_remote_backup_provider
 *
 * @package    local_remote_backup_provider
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$section = optional_param('section', '', PARAM_ALPHAEXT);

switch ($section) {
    case 'list':
        $controller = new local_remote_backup_provider\output\controllers\front_controller();
        $controller->listAction();
        break;

    case 'process':
        $controller = new local_remote_backup_provider\output\controllers\front_controller();
        $controller->processAction();
        break;

    case 'status':
        $controller = new local_remote_backup_provider\output\controllers\front_controller();
        $controller->statusAction();
        break;

    case 'admin_remote_list':
        $controller = new local_remote_backup_provider\output\controllers\admin_controller();
        $controller->remoteListAction();
        break;

    case 'admin_remote_edit':
        $controller = new local_remote_backup_provider\output\controllers\admin_controller();
        $controller->remoteEditAction();
        break;

    case 'admin_remote_show':
        $controller = new local_remote_backup_provider\output\controllers\admin_controller();
        $controller->remoteShowAction();
        break;

    case 'admin_remote_hide':
        $controller = new local_remote_backup_provider\output\controllers\admin_controller();
        $controller->remoteHideAction();
        break;

    case 'admin_transfer_log':
        $controller = new local_remote_backup_provider\output\controllers\admin_controller();
        $controller->transferLogAction();
        break;

    case 'admin_detailed_log':
        $controller = new local_remote_backup_provider\output\controllers\admin_controller();
        $controller->detailedLogAction();
        break;

    case 'admin_manual_cancel':
        $controller = new local_remote_backup_provider\output\controllers\admin_controller();
        $controller->manualCancelAction();
        break;

    case 'admin_manual_finish':
        $controller = new local_remote_backup_provider\output\controllers\admin_controller();
        $controller->manualFinishAction();
        break;

    case 'ajax_find_users':
        $controller = new local_remote_backup_provider\output\controllers\ajax_controller();
        $controller->findUsersAction();
        break;

    case '':
        redirect(new moodle_url('/local/remote_backup_provider/index.php', ['section' => 'list']));
        break;

    default:
        throw new moodle_exception('invalid_section', 'local_remote_backup_provider', new moodle_url('/'));
        break;
}