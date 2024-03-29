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
 * Definition of IMS Enterprise enrolment scheduled tasks.
 *
 * @package   enrol_imsenterprise
 * @category  task
 * @copyright 2014 Universite de Montreal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'enrol_ismu\tasks\cron\sync_data_from_ismu',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '3,12',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ),
    array(
        'classname' => 'enrol_ismu\tasks\cron\sync_global_enrolments',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '5,14',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    )
);
