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
 * This file adds the settings pages to the navigation menu
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
	$settings->add(new admin_setting_configcheckbox('newassignment/showrecentsubmissions',
	               new lang_string('showrecentsubmissions', 'newassignment'),
	               new lang_string('configshowrecentsubmissions', 'newassignment'), 0));
	$settings->add(new admin_setting_configcheckbox('newassignment/submissionreceipts',
	               get_string('sendsubmissionreceipts', 'mod_newassignment'), get_string('sendsubmissionreceipts_help', 'mod_newassignment'), 1));
}