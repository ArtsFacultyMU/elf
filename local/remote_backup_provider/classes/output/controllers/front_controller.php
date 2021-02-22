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
 * Controller for the non-admin pages.
 *
 * @package   local_remote_backup_provider
 * @copyright 2020 Masaryk University
 * @author    Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class front_controller {
    const ISSUE_TRANSFER_ERROR_LINK = 'http://moodledocs.phil.muni.cz/sprava-kurzu/zaloha-a-obnova';
    /**
     * Displays search form and course list.
     */
    public function listAction() {
        global $PAGE;
        
        $remote_id = optional_param('remote', 0, PARAM_INT);
        $search = optional_param('search', '', PARAM_NOTAGS);
        $searchall = optional_param('searchall', false, PARAM_BOOL);

        require_login();
        $PAGE->set_url('/local/remote_backup_provider/index.php');
        $PAGE->set_pagelayout('report');

        // Check the permissions.
        $context = \context_system::instance();
        require_capability('local/remote_backup_provider:access', $context);

        // Get list of all (visible) remotes.
        $remote_manager = new \local_remote_backup_provider\helper\remote_manager();
        $remotes = $remote_manager->getRemotes();
        if (!$remotes) {
            throw new \local_remote_backup_provider\exception\configuration_exception(
                    \local_remote_backup_provider\exception\configuration_exception::CODE_NO_REMOTE);
        }

        // Set remote to default (currently one on the first position) if not specified.
        if ($remote_id === 0) {
            $remote_data = current($remotes);
            $remote_id = (int)$remote_data->id;
            unset($remote_data);
        }

        // Get chosen remote data (throws an exception if invalid).
        $remote = $remote_manager->getRemote($remote_id);


        $PAGE->set_context($context);
        $PAGE->set_title(get_string('import', 'local_remote_backup_provider'));
        $PAGE->set_heading(get_string('import', 'local_remote_backup_provider'));

        $output = $PAGE->get_renderer('local_remote_backup_provider');

        echo $output->header();

        // Print the heading.
        if (empty($search)) {
            echo $output->heading(get_string('available_courses_search', 'local_remote_backup_provider'), 2);
        } else {
            echo $output->heading(get_string('available_courses', 'local_remote_backup_provider'), 2);
        }

        $remote_tabs = new \local_remote_backup_provider\output\renderables\front_remote_tabs_renderable();
        $remote_tabs->setRemote($remote_id);
        $remote_tabs->setRemotes($remotes);
        $remote_tabs->setUrl(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'list']));
        echo $output->render($remote_tabs);

        $search_form = new \local_remote_backup_provider\output\renderables\front_search_form_renderable();
        $search_form->setAction(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'list', 'remote' => $remote_id]));
        $search_form->setValue((string)$search);
        $search_form->checkSearchall($searchall);
        echo $output->render($search_form);


        // Display the courses.
        if (!empty($search)) {
            // Get config settings and initiate transfer manager.
            $courses = \local_remote_backup_provider\helper\transfer_manager::search($remote, $search, $searchall);
            
            $course_list_renderable = new \local_remote_backup_provider\output\renderables\front_course_list_renderable($courses);
            $course_list_renderable->setRemote($remote);
            $course_list_renderable->setCourses((array)$courses);
            echo $output->render($course_list_renderable);
        }

        echo \html_writer::tag('p', \html_writer::link(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'status', 'remote' => $remote_id]), get_string('issued_transfers', 'local_remote_backup_provider')));

        echo $output->footer();
    }

    /**
     * Processes selected courses to be transferred.
     */
    public function processAction() {

        global $PAGE, $DB, $USER;

        // Check the permissions.
        require_login();
        $context = \context_system::instance();
        require_capability('local/remote_backup_provider:access', $context);


        $remote_course_ids = required_param_array('remote_id', PARAM_INT);
        $remote_id = required_param('remote', PARAM_INT);
        $transfer_as = optional_param('transferas', 'self', PARAM_TEXT);
        $user_id = optional_param('userid', 0, PARAM_INT);

        $remote_manager = new \local_remote_backup_provider\helper\remote_manager();

        // Get information about the chosen remote (throws an exception if remote not found).
        $remote = $remote_manager->getRemote($remote_id);

        // Start building output.
        $PAGE->set_context($context);
        $PAGE->set_url('/local/remote_backup_provider/index.php');
        $PAGE->set_pagelayout('report');
        $PAGE->set_title(get_string('import', 'local_remote_backup_provider'));
        $PAGE->set_heading(get_string('import', 'local_remote_backup_provider'));
        $output = $PAGE->get_renderer('local_remote_backup_provider');

        if (has_capability('local/remote_backup_provider:transferasother', $context) && $transfer_as == 'other') {
            if (!$DB->get_record('user', ['id'=> $user_id, 'deleted' => '0'])) {
                redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'list', 'remote' => $remote_id]), get_string('user_not_found', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
            }
        } else {
            $user_id = $USER->id;
        }

        // Iterate over remote courses.
        $errors = [];
        foreach ($remote_course_ids as $remote_course_id) {
            $existing_records = $DB->get_records('local_remotebp_transfer', ['remotecourseid' => $remote_course_id]);

            if (has_capability('local/remote_backup_provider:multitransfer', $context) OR !$existing_records) {
                $transfer_id = \local_remote_backup_provider\helper\transfer_manager::add_new($remote, $remote_course_id, $user_id);
            
                $create_backup_task = new \local_remote_backup_provider\task\transfer_create_backup();
                $create_backup_task->set_custom_data(array(
                    'transfer_id' => $transfer_id,
                ));
                \core\task\manager::queue_adhoc_task($create_backup_task);
            } else {
                $record = \array_pop($existing_records);
                $user = $DB->get_record('user', ['id' => $record->userid]);
                if ($record->courseid !== NULL) {
                    $errors[] = '<a href="' . new \moodle_url('/course/view.php', ['id' => $record->courseid]) . '">' . $record->remotecoursename . '</a>'
                            . ' (' . get_string('issued_by', 'local_remote_backup_provider') . ' ' . $user->firstname . ' ' . $user->lastname . ')';
                } else {
                    $errors[] = $record->remotecoursename . ' (' . get_string('issued_by', 'local_remote_backup_provider') . ' ' . $user->firstname . ' ' . $user->lastname . ')';
                }
            }
        }

        if (!$errors) {
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'status', 'remote' => $remote_id]), get_string('courses_issued_for_transfer', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_SUCCESS);
        }

        $errors = '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
        $error_msg = get_string('courses_issued_for_transfer_error', 'local_remote_backup_provider', ['errors' => $errors, 'link' => self::ISSUE_TRANSFER_ERROR_LINK]);

        redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'status', 'remote' => $remote_id]), $error_msg, null, \core\output\notification::NOTIFY_WARNING);
        
    }

    /**
     * Prints list of processed courses and their statuses.
     */
    public function statusAction() {
        global $DB, $USER, $PAGE;

        // Getting which remote courses to display.
        $remote_id = optional_param('remote', 0, PARAM_INT);

        require_login();
        $PAGE->set_url('/local/remote_backup_provider/index.php');
        $PAGE->set_pagelayout('report');

        // Check the permissions.
        $context = \context_system::instance();
        require_capability('local/remote_backup_provider:access', $context);

        // Get list of all (visible) remotes.
        $remote_manager = new \local_remote_backup_provider\helper\remote_manager();
        $remotes = $remote_manager->getRemotes();
        if (!$remotes) {
            throw new \local_remote_backup_provider\exception\configuration_exception(
                    \local_remote_backup_provider\exception\configuration_exception::CODE_NO_REMOTE);
        }

        // Set remote to default (currently one on the first position) if not specified.
        if ($remote_id === 0) {
            $remote_data = current($remotes);
            $remote_id = (int)$remote_data->id;
            unset($remote_data);
        }

        // Get chosen remote data (throws an exception if invalid).
        $remote = $remote_manager->getRemote($remote_id);

        $PAGE->set_context($context);
        $PAGE->set_title(get_string('import', 'local_remote_backup_provider'));
        $PAGE->set_heading(get_string('import', 'local_remote_backup_provider'));

        $output = $PAGE->get_renderer('local_remote_backup_provider');

        echo $output->header();

        // Print the heading.
        echo $output->heading(get_string('issued_transfers', 'local_remote_backup_provider'), 2);

        // Print remote tabs.
        $remote_tabs = new \local_remote_backup_provider\output\renderables\front_remote_tabs_renderable();
        $remote_tabs->setRemote($remote_id);
        $remote_tabs->setRemotes($remotes);
        $remote_tabs->setUrl(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'status']));
        echo $output->render($remote_tabs);

        // Print the transfer table.
        $transfers = $DB->get_records('local_remotebp_transfer', [
            'userid' => $USER->id,
            'remoteid' => $remote_id,
        ], 'timecreated');

        echo $output->render_front_transfer_list($remote_id, array_reverse($transfers));

        // Print return link;
        echo \html_writer::tag('p', $output->larrow() . \html_writer::link(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'list', 'remote' => $remote_id]), get_string('back_to_selection', 'local_remote_backup_provider')));

        // Print footer.
        echo $output->footer();
    }
}