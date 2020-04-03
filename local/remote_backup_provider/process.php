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

use local_remote_backup_provider\transfer_manager_exception;

require_once(__DIR__ . '/../../config.php');
require_once('classes/transfer_manager.php');
require_once('output/search_form/renderable.php');
require_once('output/search_form/renderer.php');
require_once('output/course_list/renderable.php');
require_once('output/course_list/renderer.php');

$remote = required_param_array('remote', PARAM_INT);
$search = optional_param('search', '', PARAM_NOTAGS);

require_login();
$PAGE->set_url('/local/remote_backup_provider/index.php');
$PAGE->set_pagelayout('report');
$returnurl = new moodle_url('/', array('redirect' => 0,));

// Check the permissions.
$context = context_system::instance();
require_capability('local/remote_backup_provider:access', $context);

// Get config settings and initiate transfer manager.
try {
    $transfer_manager = new local_remote_backup_provider\transfer_manager();
} catch (Exception $e) {
    print_error('pluginnotconfigured', 'local_remote_backup_provider', $returnurl);
}

$PAGE->set_title(get_string('import', 'local_remote_backup_provider'));
$PAGE->set_heading(get_string('import', 'local_remote_backup_provider'));

echo $OUTPUT->header();


$errors = [];
foreach ($remote as $remote_id) {
    // Generate the backup file.
    try {
        $storedfile = $transfer_manager->backup_from_remote($remote_id, $context);
        $local_id = $transfer_manager->restore($storedfile);
    } catch (transfer_manager_exception $ex) {
        \core\notification::error(sprintf(get_string('import_failure', 'local_remote_backup_provider'), $remote_id) . '<br />' .
                get_string($ex->getMessage(), 'local_remote_backup_provider'));
        continue;
    } catch (moodle_exception $ex) {
        \core\notification::error(sprintf(get_string('import_failure', 'local_remote_backup_provider'), $remote_id) . '<br />' .
                $ex->getMessage());
        continue;
    }

    $new_course = $DB->get_record('course',['id' => $local_id]);
    $url = $CFG->wwwroot . '/course/view.php?id=' . $local_id;
    \core\notification::success(sprintf(get_string('import_success', 'local_remote_backup_provider'), $remote_id, $url, $new_course->fullname));
}

echo $OUTPUT->container(
    html_writer::link(
        new moodle_url('/local/remote_backup_provider/index.php'),
        get_string('continue'), ['class'=>'btn btn-primary']),
    'text-center'
);

echo $OUTPUT->footer();