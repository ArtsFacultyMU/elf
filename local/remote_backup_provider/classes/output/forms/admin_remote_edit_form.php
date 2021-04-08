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

namespace local_remote_backup_provider\output\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * Form to add or edit access to remote.
 *
 * @package   local_remote_backup_provider
 * @copyright 2020 Masaryk University
 * @author    Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_remote_edit_form extends \moodleform {
    /**
     * Defines the search form.
     */
    public function definition() {
        $mform = $this->_form;
        
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'name', get_string('remote_name', 'local_remote_backup_provider'));
        $mform->setType('name', PARAM_NOTAGS);
        
        $mform->addElement('text', 'address', get_string('remote_url', 'local_remote_backup_provider'));
        $mform->setType('address', PARAM_URL);

        $mform->addElement('text', 'token', get_string('remote_token', 'local_remote_backup_provider'));
        $mform->setType('token', PARAM_NOTAGS);
        
        $this->add_action_buttons(false, get_string('edit'));
    }
}