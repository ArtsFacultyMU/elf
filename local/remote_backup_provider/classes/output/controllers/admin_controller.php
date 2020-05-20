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
 * Controller for the admin pages.
 *
 * @package   local_remote_backup_provider
 * @copyright 2020 Masaryk University
 * @author    Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_controller {
    /**
     * Displays list of remotes.
     */
    public function remoteListAction() {
        global $PAGE, $DB, $CFG;
        require_once($CFG->libdir . '/adminlib.php');
        require_once($CFG->libdir . '/tablelib.php');

        admin_externalpage_setup('local-remote_backup_provider-remote_list');

        // Get records from the remotes table.
        $remotes = $DB->get_records('local_remotebp_remotes', null, 'position');

        // Display data.
        $PAGE->set_title(get_string('admin_remote_list', 'local_remote_backup_provider'));
        $PAGE->set_heading(get_string('admin_remote_list', 'local_remote_backup_provider'));
        $output = $PAGE->get_renderer('local_remote_backup_provider');
        
        echo $output->header();
        echo $output->render_admin_remote_list($remotes);
        echo $output->footer();
    }

    public function remoteEditAction() {
        global $PAGE, $DB, $CFG;
        require_once($CFG->libdir . '/adminlib.php');

        admin_externalpage_setup('local-remote_backup_provider-remote_edit');
        // Fetch GET parameter with remote ID (if editing).
        $remote_id = optional_param('remote', 0, PARAM_INT);

        // Initialize the form.
        $form = new \local_remote_backup_provider\output\forms\admin_remote_edit_form(
                new \moodle_url('/local/remote_backup_provider/index.php',
                ['section' => 'admin_remote_edit', 'remote' => $remote_id]));
        
        // Parse form data if form was already sent.
        if ($formdata = $form->get_data()) {
            $manager = new \local_remote_backup_provider\helper\remote_manager();

            // Add a new remote.
            if ($formdata->id==0) {
                $remote_id = $manager->addRemote($formdata);
                redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_remote_list']), get_string('remote_added', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_SUCCESS);
                
            // Edit existing remote.
            } elseif ($DB->record_exists('local_remotebp_remotes', ['id' => $formdata->id])) {
                $remote = $DB->update_record('local_remotebp_remotes', $formdata);
                redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_remote_list']), get_string('remote_updated', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_SUCCESS);

            // Redirect if tried to edit non-existing remote.
            } else {
                redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_remote_list']), get_string('remote_not_found', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
            }
        }

        // Render the page and form.
        // Add a new remote.
        if ($remote_id===0) {
            $PAGE->set_title(get_string('admin_remote_add', 'local_remote_backup_provider'));
            $PAGE->set_heading(get_string('admin_remote_add', 'local_remote_backup_provider'));

            $remote = new \stdClass();
            $remote->id = 0;

        // Edit existing remote.
        } elseif ($DB->record_exists('local_remotebp_remotes', ['id' => $remote_id])) {
            $PAGE->set_title(get_string('admin_remote_edit', 'local_remote_backup_provider'));
            $PAGE->set_heading(get_string('admin_remote_edit', 'local_remote_backup_provider'));

            $remote = $DB->get_record('local_remotebp_remotes', ['id' => $remote_id]);

        // Redirect if tried to edit non-existing remote.
        } else {
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_remote_list']), get_string('remote_not_found', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
        }

        // Display form if not redirected.
        $output = $PAGE->get_renderer('local_remote_backup_provider');
        echo $output->header();
        $form->set_data($remote);
        $form->display();
        echo $output->footer();
    }
}