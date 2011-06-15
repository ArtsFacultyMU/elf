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
pl * plagiarism.php - allows the admin to configure plagiarism stuff
 *
 * @package   plagiarism_turnitin
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once(dirname(dirname(__FILE__)) . '/../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/plagiarismlib.php');
    require_once($CFG->dirroot.'/plagiarism/urkund/lib.php');
    require_once($CFG->dirroot.'/plagiarism/urkund/plagiarism_form.php');

    require_login();
    admin_externalpage_setup('plagiarismurkund');

    $context = get_context_instance(CONTEXT_SYSTEM);

    require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

    require_once('plagiarism_form.php');
    $mform = new plagiarism_setup_form();
    $plagiarismplugin = new plagiarism_plugin_urkund();

    if ($mform->is_cancelled()) {
        redirect('');
    }

    echo $OUTPUT->header();

    if (($data = $mform->get_data()) && confirm_sesskey()) {
        if (!isset($data->urkund_use)) {
            $data->urkund_use = 0;
        }
        foreach ($data as $field=>$value) {
            if (strpos($field, 'urkund')===0) {
                if ($configfield = $DB->get_record('config_plugins', array('name'=>$field, 'plugin'=>'plagiarism'))) {
                    $configfield->value = $value;
                    if (! $DB->update_record('config_plugins', $configfield)) {
                        error("errorupdating");
                    }
                } else {
                    $configfield = new stdClass();
                    $configfield->value = $value;
                    $configfield->plugin = 'plagiarism';
                    $configfield->name = $field;
                    if (! $DB->insert_record('config_plugins', $configfield)) {
                        error("errorinserting");
                    }
                }
            }
        }
        notify(get_string('savedconfigsuccess', 'plagiarism_urkund'), 'notifysuccess');
    }
    $plagiarismsettings = (array)get_config('plagiarism');
    $mform->set_data($plagiarismsettings);
    
    echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
    $mform->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
