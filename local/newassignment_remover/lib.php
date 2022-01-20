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

defined('MOODLE_INTERNAL') || die();

/**
 * Creates navigation node in the course settings menu ("cogwheel menu").
 */
function local_newassignment_remover_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('local/newassignment_remover:use', $context)) {
        $url = new moodle_url(
                '/local/newassignment_remover/index.php',
                ['course' => $course->id]);

        $navigation->add(
                get_string('pluginname', 'local_newassignment_remover'),
                $url,
                navigation_node::TYPE_SETTING, null, null, new pix_icon('t/delete', ''));
    }
}