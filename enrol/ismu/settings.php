<?php
/**
 * IS MU enrolment plugin settings and presets.
 *
 * @package    enrol
 * @subpackage ismu
 * @author     2016 Filip Benčo
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_ismu_settings', '', get_string('pluginname_desc', 'enrol_ismu')));

    // Current semester.
    $settings->add(
        new admin_setting_configselect(
            'enrol_ismu/currentperiod', 
            get_string('current_period', 'enrol_ismu'), 
            get_string('current_period_desc', 'enrol_ismu'), 
            \enrol_ismu\helpers\semester::get_current_semester()->full(),
            \enrol_ismu\helpers\semester::get_adminsettings_semesters()
        )
    );

    // First and last semester, which can be chosen in course.
    $settings->add(
        new admin_setting_configselect(
            'enrol_ismu/periodselectionstart', 
            get_string('period_selection_start', 'enrol_ismu'), 
            get_string('period_selection_start_desc', 'enrol_ismu'), 
            \enrol_ismu\helpers\semester::get_current_semester()->full(),
            \enrol_ismu\helpers\semester::get_adminsettings_semesters()
        )
    );
    $settings->add(
        new admin_setting_configselect(
            'enrol_ismu/periodselectionend', 
            get_string('period_selection_end', 'enrol_ismu'), 
            get_string('period_selection_end_desc', 'enrol_ismu'), 
            \enrol_ismu\helpers\semester::get_current_semester()->full(),
            \enrol_ismu\helpers\semester::get_adminsettings_semesters()
        )
    );
    
    // Whether should be enrol_ismu automatically added to new courses.
    $settings->add(
        new admin_setting_configcheckbox(
            'enrol_ismu/defaultenrol',
            get_string('defaultenrol', 'enrol'),
            get_string('defaultenrol_desc', 'enrol'),
            1
        )
    );
    
    // List of courses (IDs) for all teachers to be automatically enroled in.
    $settings->add(
        new admin_setting_configtext(
            'enrol_ismu/teacherscourses',
            get_string('teachers_courses', 'enrol_ismu'),
            get_string('teachers_courses_desc', 'enrol_ismu'),
            ''
        )
    );
    
    // List of courses (IDs) for all students to be automatically enroled in.
    $settings->add(
        new admin_setting_configtext(
            'enrol_ismu/studentscourses',
            get_string('students_courses', 'enrol_ismu'),
            get_string('students_courses_desc', 'enrol_ismu'),
            ''
        )
    );
   
    // List of forums (IDs) for all teachers to be automatically subscribed to.
    $settings->add(
        new admin_setting_configtext(
            'enrol_ismu/teachersforums',
            get_string('teachers_forums', 'enrol_ismu'),
            get_string('teachers_forums_desc', 'enrol_ismu'),
            ''
        )
    );
    
    // List of forums (IDs) for all students to be automatically subscribed to.
    $settings->add(
        new admin_setting_configtext(
            'enrol_ismu/studentsforums',
            get_string('students_forums', 'enrol_ismu'),
            get_string('students_forums_desc', 'enrol_ismu'),
            ''
        )
    );
   
    // List of groups (IDs) for all teachers to be automatically signed in.
    $settings->add(
        new admin_setting_configtext(
            'enrol_ismu/teachersgroups',
            get_string('teachers_groups', 'enrol_ismu'),
            get_string('teachers_groups_desc', 'enrol_ismu'),
            ''
        )
    );
    
    // List of groups (IDs) for all students to be automatically signed in.
    $settings->add(
        new admin_setting_configtext(
            'enrol_ismu/studentsgroups',
            get_string('students_groups', 'enrol_ismu'),
            get_string('students_groups_desc', 'enrol_ismu'),
            ''
        )
    );
    
}
