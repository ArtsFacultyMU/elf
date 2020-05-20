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
    /**
     * Displays search form and course list.
     */
    public function listAction() {
        global $PAGE;
        
        $remote_id = optional_param('remote', 0, PARAM_INT);
        $search = optional_param('search', '', PARAM_NOTAGS);

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
        echo $output->render($search_form);


        // Display the courses.
        if (!empty($search)) {
            // Get config settings and initiate transfer manager.
            $transfer_manager = new \local_remote_backup_provider\helper\transfer_manager($remote);
            $courses = $transfer_manager->search($search);
            
            $course_list_renderable = new \local_remote_backup_provider\output\renderables\front_course_list_renderable($courses);
            $course_list_renderable->setRemote($remote);
            $course_list_renderable->setCourses($courses);
            echo $output->render($course_list_renderable);
        }

        echo $output->footer();
    }

    /**
     * Processes selected courses to be transferred.
     */
    public function processAction() {
        global $PAGE, $DB;

        // Check the permissions.
        require_login();
        $context = \context_system::instance();
        require_capability('local/remote_backup_provider:access', $context);


        $remote_course_ids = required_param_array('remote_id', PARAM_INT);
        $remote_id = required_param('remote', PARAM_INT);

        $remote_manager = new \local_remote_backup_provider\helper\remote_manager();

        // Get information about the chosen remote (throws an exception if remote not found).
        $remote = $remote_manager->getRemote($remote_id);
        
        // Get config settings and initiate transfer manager.
        $transfer_manager = new \local_remote_backup_provider\helper\transfer_manager($remote);


        // Start building output.
        $PAGE->set_url('/local/remote_backup_provider/index.php');
        $PAGE->set_pagelayout('report');
        $PAGE->set_title(get_string('import', 'local_remote_backup_provider'));
        $PAGE->set_heading(get_string('import', 'local_remote_backup_provider'));
        $output = $PAGE->get_renderer('local_remote_backup_provider');
        echo $output->header();

        // Iterate over remote courses.
        foreach ($remote_course_ids as $remote_course_id) {
            // Try to process the remote course.
            try {
                // Generate the backup file.
                $storedfile = $transfer_manager->backup_from_remote($remote_course_id, $context);
                // Restore the backup file locally.
                $local_id = $transfer_manager->restore($storedfile);
            
            // Catch known exceptions & print suitable error message.
            } catch (\local_remote_backup_provider\exception\transfer_manager_exception $ex) {
                \core\notification::error(sprintf(get_string('import_failure', 'local_remote_backup_provider'), $remote_course_id) . '<br />' .
                        get_string($ex->getMessage(), 'local_remote_backup_provider'));
                continue;
            
            // Catch unknown exceptions & print suitable error message.
            } catch (\moodle_exception $ex) {
                \core\notification::error(sprintf(get_string('import_failure', 'local_remote_backup_provider'), $remote_course_id) . '<br />' .
                        $ex->getMessage());
                continue;
            }

            // Create success message.
            $new_course = $DB->get_record('course',['id' => $local_id]);
            \core\notification::success(sprintf(get_string('import_success', 'local_remote_backup_provider'),
                    $remote_course_id, new \moodle_url('/course/view.php', ['id' => $local_id]), $new_course->fullname));
        }

        // Add continue button.
        echo $output->container(
            \html_writer::link(
                new \moodle_url('/local/remote_backup_provider/index.php'),
                get_string('continue'), ['class'=>'btn btn-primary']),
            'text-center'
        );

        // Print footer.
        echo $output->footer();
    }
}