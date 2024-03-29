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

    public function remoteShowAction() {
        global $PAGE, $DB, $CFG;
        require_once($CFG->libdir . '/adminlib.php');

        admin_externalpage_setup('local-remote_backup_provider-remote_show');
        // Fetch GET parameter with remote ID (if editing).
        $remote_id = optional_param('remote', 0, PARAM_INT);

        if ($DB->record_exists('local_remotebp_remotes', ['id' => $remote_id])) {
            $PAGE->set_title(get_string('admin_remote_edit', 'local_remote_backup_provider'));
            $PAGE->set_heading(get_string('admin_remote_edit', 'local_remote_backup_provider'));

            $remote = $DB->get_record('local_remotebp_remotes', ['id' => $remote_id]);

            if (!$remote->active) {
                $remote = $DB->update_record('local_remotebp_remotes', 
                        (object)['id' => $remote_id, 'active' => 1]);
                redirect(
                        new \moodle_url('/local/remote_backup_provider/index.php',
                        ['section' => 'admin_remote_list']), 
                        get_string('remote_set_as_visible', 'local_remote_backup_provider'),
                        null, \core\output\notification::NOTIFY_SUCCESS);
            } else {
                redirect(
                    new \moodle_url('/local/remote_backup_provider/index.php',
                    ['section' => 'admin_remote_list']), 
                    get_string('remote_already_visible', 'local_remote_backup_provider'),
                    null, \core\output\notification::NOTIFY_WARNING);
            }

        // Redirect if tried to edit non-existing remote.
        } else {
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_remote_list']), get_string('remote_not_found', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
        }
    }

    public function remoteHideAction() {
        global $PAGE, $DB, $CFG;
        require_once($CFG->libdir . '/adminlib.php');

        admin_externalpage_setup('local-remote_backup_provider-remote_hide');
        // Fetch GET parameter with remote ID (if editing).
        $remote_id = optional_param('remote', 0, PARAM_INT);

        if ($DB->record_exists('local_remotebp_remotes', ['id' => $remote_id])) {
            $PAGE->set_title(get_string('admin_remote_edit', 'local_remote_backup_provider'));
            $PAGE->set_heading(get_string('admin_remote_edit', 'local_remote_backup_provider'));

            $remote = $DB->get_record('local_remotebp_remotes', ['id' => $remote_id]);

            if ($remote->active) {
                $remote = $DB->update_record('local_remotebp_remotes', 
                        (object)['id' => $remote_id, 'active' => 0]);
                redirect(
                        new \moodle_url('/local/remote_backup_provider/index.php',
                        ['section' => 'admin_remote_list']), 
                        get_string('remote_set_as_hidden', 'local_remote_backup_provider'),
                        null, \core\output\notification::NOTIFY_SUCCESS);
            } else {
                redirect(
                    new \moodle_url('/local/remote_backup_provider/index.php',
                    ['section' => 'admin_remote_list']), 
                    get_string('remote_already_hidden', 'local_remote_backup_provider'),
                    null, \core\output\notification::NOTIFY_WARNING);
            }

        // Redirect if tried to edit non-existing remote.
        } else {
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_remote_list']), get_string('remote_not_found', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
        }
    }

    public function remoteMoveAction() {
        global $DB, $CFG;

        require_once($CFG->libdir . '/adminlib.php');
        
        admin_externalpage_setup('local-remote_backup_provider-remote_move');
        // Fetch GET parameter with remote ID (if editing).
        $remote_id = optional_param('remote', 0, PARAM_INT);
        $direction = optional_param('direction', 'none', PARAM_TEXT);

        if ($DB->record_exists('local_remotebp_remotes', ['id' => $remote_id])) {
            if ($direction === 'up' || $direction === 'down') {
                $remote = $DB->get_record('local_remotebp_remotes', ['id' => $remote_id]);
                $neighbour_remotes = $DB->get_records_select(
                    'local_remotebp_remotes',
                    '`position` ' . ($direction === 'up' ? '<' : '>') . ' ?',
                    [$remote->position],
                    '`position` ' . ($direction === 'up' ? 'DESC' : 'ASC')
                );
                if ($neighbour_remotes) {
                    $neighbour = array_shift($neighbour_remotes);
                    $DB->update_record('local_remotebp_remotes', (object)['id' => $neighbour->id, 'position' => $remote->position]);
                    $DB->update_record('local_remotebp_remotes', (object)['id' => $remote->id, 'position' => $neighbour->position]);
                    redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_remote_list']), get_string('move_successful', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_SUCCESS);
                } else {
                    redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_remote_list']), get_string('move_failed', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
                }
            } else {
                redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_remote_list']), get_string('move_failed', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
            }
        // Redirect if tried to edit non-existing remote.
        } else {
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_remote_list']), get_string('remote_not_found', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
        }
    }

    public function transferLogAction() {
        global $DB, $CFG, $PAGE;

        require_once($CFG->libdir . '/adminlib.php');

        require_login();
        $context = \context_system::instance();
        require_capability('local/remote_backup_provider:managetransfers', $context);
        $PAGE->set_context($context);
        $PAGE->set_pagelayout('admin');
        $PAGE->set_url(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_transfer_log']));
        $PAGE->set_title(get_string('admin_transfer_log', 'local_remote_backup_provider'));
        $PAGE->set_heading(get_string('import', 'local_remote_backup_provider') . ': ' . get_string('admin_transfer_log', 'local_remote_backup_provider'));

        // Getting which remote courses to display.
        $remote_id = optional_param('remote', 0, PARAM_INT);

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

        $output = $PAGE->get_renderer('local_remote_backup_provider');

        echo $output->header();

        // Print remote tabs.
        $remote_tabs = new \local_remote_backup_provider\output\renderables\front_remote_tabs_renderable();
        $remote_tabs->setRemote($remote_id);
        $remote_tabs->setRemotes($remotes);
        $remote_tabs->setUrl(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_transfer_log']));
        echo $output->render($remote_tabs);

        // Print the transfer table.
        $transfers = $DB->get_records('local_remotebp_transfer', [
            'remoteid' => $remote_id,
        ], 'id DESC');

        echo $output->render_admin_transfer_log($transfers, $remote_id);
        echo $output->footer();
    }

    public function detailedLogAction() {
        global $PAGE, $CFG, $DB;

        require_once($CFG->libdir . '/adminlib.php');
        require_login();
        $context = \context_system::instance();
        require_capability('local/remote_backup_provider:managetransfers', $context);
        $PAGE->set_context($context);
        $PAGE->set_pagelayout('admin');
        $PAGE->set_url(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_detailed_log']));
        $PAGE->set_title(get_string('admin_detailed_log', 'local_remote_backup_provider'));
        $PAGE->set_heading(get_string('import', 'local_remote_backup_provider') . ': ' . get_string('admin_detailed_log', 'local_remote_backup_provider'));

        $transfer_id = optional_param('id', 0, PARAM_INT);

        try {
            $transfer = new \local_remote_backup_provider\helper\transfer_manager($transfer_id);
        } catch (\local_remote_backup_provider\exception\transfer_manager_exception $e) {
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_transfer_log']), get_string('transfer_not_found', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
        }

        $logs = $DB->get_records('local_remotebp_transfer_log', ['transferid' => $transfer_id], 'id DESC');
        
        $output = $PAGE->get_renderer('local_remote_backup_provider');
        echo $output->header();
        echo $output->render_admin_detailed_log($transfer_id, $logs);
        echo $output->footer();
    }

    public function manualCancelAction() {
        global $PAGE, $CFG, $DB;

        require_once($CFG->libdir . '/adminlib.php');
        require_login();
        $context = \context_system::instance();
        require_capability('local/remote_backup_provider:managetransfers', $context);
        $PAGE->set_context($context);
        $PAGE->set_pagelayout('admin');
        $PAGE->set_url(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_manual_cancel']));
        $PAGE->set_title(get_string('admin_manual_cancel', 'local_remote_backup_provider'));
        $PAGE->set_heading(get_string('import', 'local_remote_backup_provider') . ': ' . get_string('admin_manual_cancel', 'local_remote_backup_provider'));

        $transfer_id = optional_param('id', 0, PARAM_INT);
        $sure = optional_param('sure', 0, PARAM_INT);

        // Prechecks
        try {
            $transfer_manager = new \local_remote_backup_provider\helper\transfer_manager($transfer_id);
        } catch (\local_remote_backup_provider\exception\transfer_manager_exception $e) {
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_transfer_log']), get_string('transfer_not_found', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
        }
        if ($transfer_manager->transfer->status == \local_remote_backup_provider\helper\transfer_manager::STATUS_CANCELED) {
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_transfer_log', 'remote' => $transfer_manager->remote->id]), get_string('transfer_already_canceled', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
        }
        if ($transfer_manager->transfer->status == \local_remote_backup_provider\helper\transfer_manager::STATUS_FINISHED) {
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_transfer_log', 'remote' => $transfer_manager->remote->id]), get_string('transfer_already_finished', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
        }
        if ($transfer_manager->transfer->manualcancel) {
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_transfer_log', 'remote' => $transfer_manager->remote->id]), get_string('transfer_already_canceled', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
        }

        // If already consented, change to canceled
        if ($sure) {
            $transfer_manager->cancel_manually();
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_transfer_log', 'remote' => $transfer_manager->remote->id]), get_string('transfer_canceled_successfully', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_SUCCESS);
        }

        // Otherwise show consent button
        $output = $PAGE->get_renderer('local_remote_backup_provider');
        echo $output->header();
        echo $output->box(
            \html_writer::tag('p', get_string('transfer_manualcancel_areyousure', 'local_remote_backup_provider') . ' ' .  \html_writer::tag('b', $transfer_manager->transfer->remotecoursename))
            . $output->continue_button(new \moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_manual_cancel', 'id' => $transfer_id, 'sure' => 1]))
        );
        echo $output->footer();
    }

    public function manualFinishAction() {
        global $PAGE, $CFG, $DB;

        $FORM_ID = 'manual_finish_form';

        require_once($CFG->libdir . '/adminlib.php');
        require_login();
        $context = \context_system::instance();
        require_capability('local/remote_backup_provider:managetransfers', $context);
        $PAGE->set_context($context);
        $PAGE->set_pagelayout('admin');
        $PAGE->set_url(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_manual_finish']));
        $PAGE->set_title(get_string('admin_manual_finish', 'local_remote_backup_provider'));
        $PAGE->set_heading(get_string('import', 'local_remote_backup_provider') . ': ' . get_string('admin_manual_finish', 'local_remote_backup_provider'));

        $transfer_id = optional_param('id', 0, PARAM_INT);
        $courseid = optional_param('courseid', 0, PARAM_INT);

        // Prechecks
        try {
            $transfer_manager = new \local_remote_backup_provider\helper\transfer_manager($transfer_id);
        } catch (\local_remote_backup_provider\exception\transfer_manager_exception $e) {
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_transfer_log']), get_string('transfer_not_found', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
        }
        if ($transfer_manager->transfer->status == \local_remote_backup_provider\helper\transfer_manager::STATUS_FINISHED) {
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_transfer_log', 'remote' => $transfer_manager->remote->id]), get_string('transfer_already_finished', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
        }

        // If already consented, change to finished
        if ($courseid) {
            if (!$DB->get_record('course', ['id'=> $courseid])) {
                redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_manual_finish', 'id' => $transfer_id]), get_string('course_not_found', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_WARNING);
            }
            $transfer_manager->finish_manually($courseid);
            redirect(new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'admin_transfer_log', 'remote' => $transfer_manager->remote->id]), get_string('transfer_finished_successfully', 'local_remote_backup_provider'), null, \core\output\notification::NOTIFY_SUCCESS);
        }

        // Otherwise show consent button
        $output = $PAGE->get_renderer('local_remote_backup_provider');
        echo $output->header();


        $button_url = new \moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_manual_finish', 'id' => $transfer_id]);
            
        $button = new \single_button($button_url, get_string('continue'), 'POST', true);
        $button->formid = $FORM_ID;

        $control = get_string('course_id', 'local_remote_backup_provider') . ':'. \html_writer::empty_tag(
            'input',
            ['class'=> 'form-control mr-1 ml-1', 'form' => $FORM_ID, 'name'=>'courseid']
        );
        $control .= $output->render($button);
        echo \html_writer::div(
            $control, 
            'col-md-12 form-inline'
        );
        echo $output->footer();
    }
}