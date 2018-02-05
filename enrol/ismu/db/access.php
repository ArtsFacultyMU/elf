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
 * Capabilities for guest access plugin.
 *
 * @package    enrol
 * @subpackage guest
 * @author     2012 Filip Benčo
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    'enrol/ismu:config' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        )
    ),
		
    'enrol/ismu:unenrol' => array(
            'captype' => 'write',
            'contextlevel' => CONTEXT_COURSE,
            'archetypes' => array(
                    'manager' => CAP_ALLOW,
                    'editingteacher' => CAP_ALLOW,
            )
    ),
    
    /* Voluntarily unenrol self from course - watch out for data loss. */
    'enrol/ismu:unenrolself' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'student' => CAP_ALLOW,
        )
    ),

);


