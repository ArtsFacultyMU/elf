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
 * A moodle form to manage files
 *
 * @package   mod-folder
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class mod_folder_edit_form extends moodleform {
    function definition() {
        $mform =& $this->_form;
        $cmid = $this->_customdata['id'];
        $mform->addElement('hidden', 'id', $cmid);
        $mform->addElement('filemanager', 'files_filemanager', get_string('files'), null, array('subdirs'=>1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL));
        $submit_string = get_string('submit');
        $this->add_action_buttons(true, $submit_string);
    }
}
