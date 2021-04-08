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
 * Web service definitions for local_remote_backup_provider
 *
 * @package    local_remote_backup_provider
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_remote_backup_provider_find_courses' => array(
         'classname' => 'local_remote_backup_provider\external\find_courses',
         'methodname' => 'find_courses',
         'classpath' => 'local/remote_backup_provider/externallib.php',
         'description' => 'Find courses matching a given string.',
         'type' => 'read',
         'capabilities' => 'moodle/course:viewhiddencourses',
    ),
    'local_remote_backup_provider_find_teacher_courses' => array(
         'classname' => 'local_remote_backup_provider\external\find_teacher_courses',
         'methodname' => 'find_teacher_courses',
         'classpath' => 'local/remote_backup_provider/externallib.php',
         'description' => 'Find courses matching a given string (limited for teacher).',
         'type' => 'read',
         'capabilities' => 'moodle/course:viewhiddencourses',
    ),
    'local_remote_backup_provider_get_course_backup_by_id' => array(
         'classname' => 'local_remote_backup_provider\external\get_course_backup_by_id',
         'methodname' => 'get_course_backup_by_id',
         'description' => 'Generate a course backup file and return a link.',
         'type' => 'read',
         'capabilities' => 'moodle/backup:backupcourse',
    ),
    'local_remote_backup_provider_get_course_name_by_id' => array(
          'classname' => 'local_remote_backup_provider\external\get_course_name_by_id',
          'methodname' => 'get_course_name_by_id',
          'description' => 'Return name of the course.',
          'type' => 'read',
          'capabilities' => 'moodle/course:viewhiddencourses',
     ),
     'local_remote_backup_provider_get_course_category_by_id' => array(
          'classname' => 'local_remote_backup_provider\external\get_course_category_by_id',
          'methodname' => 'get_course_category_by_id',
          'description' => 'Return category (id) of the course.',
          'type' => 'read',
          'capabilities' => 'moodle/course:viewhiddencourses',
     ),
     'local_remote_backup_provider_get_category_info' => array(
          'classname' => 'local_remote_backup_provider\external\get_category_info',
          'methodname' => 'get_category_info',
          'description' => 'Return name of the course.',
          'type' => 'read',
          'capabilities' => 'moodle/course:viewhiddencourses',
     ),
     'local_remote_backup_provider_delete_course_backup' => array(
          'classname' => 'local_remote_backup_provider\external\delete_course_backup',
          'methodname' => 'delete_course_backup',
          'description' => 'Delete course backup.',
          'type' => 'write',
          'capabilities' => 'moodle/backup:backupcourse',
     ),
);

$services = array(
     'local_remote_backup_provider' => array(
          'functions' => array(
               'local_remote_backup_provider_find_courses',
               'local_remote_backup_provider_find_teacher_courses',
               'local_remote_backup_provider_get_course_backup_by_id',
               'local_remote_backup_provider_get_course_name_by_id',
               'local_remote_backup_provider_get_course_category_by_id',
               'local_remote_backup_provider_get_category_info',
               'local_remote_backup_provider_delete_course_backup',
          ), 
          'restrictedusers' => 0, 
          'enabled' => 1, 
          'shortname' => 'local_remote_backup_provider',
     ),
);
