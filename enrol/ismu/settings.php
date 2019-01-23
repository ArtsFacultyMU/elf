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
    
    $helper = new enrol_ismu\helper;
    $currentPeriod = $helper->get_current_period();
    $periods = $helper->get_available_periods($currentPeriod, 99999, true);
    $settings->add(
        new admin_setting_configselect(
            'enrol_ismu/currentperiod', 
            get_string('current_period', 'enrol_ismu'), 
            get_string('current_period_desc', 'enrol_ismu'), 
            $currentPeriod['full'], 
            $periods
        )
    );
    
    $settings->add(
        new admin_setting_configcheckbox(
            'enrol_ismu/defaultenrol',
            get_string('defaultenrol', 'enrol'),
            get_string('defaultenrol_desc', 'enrol'),
            1
        )
    );
    
    $settings->add(
        new admin_setting_configtext(
            'enrol_ismu/teacherscourses',
            get_string('teachers_courses', 'enrol_ismu'),
            get_string('teachers_courses_desc', 'enrol_ismu'),
            ''
        )
    );
    
    $settings->add(
        new admin_setting_configtext(
            'enrol_ismu/studentscourses',
            get_string('students_courses', 'enrol_ismu'),
            get_string('students_courses_desc', 'enrol_ismu'),
            ''
        )
    );
   
    $settings->add(
        new admin_setting_configtext(
            'enrol_ismu/teachersforums',
            get_string('teachers_forums', 'enrol_ismu'),
            get_string('teachers_forums_desc', 'enrol_ismu'),
            ''
        )
    );
    
    $settings->add(
        new admin_setting_configtext(
            'enrol_ismu/studentsforums',
            get_string('students_forums', 'enrol_ismu'),
            get_string('students_forums_desc', 'enrol_ismu'),
            ''
        )
    );
   
    $settings->add(
        new admin_setting_configtext(
            'enrol_ismu/teachersgroups',
            get_string('teachers_groups', 'enrol_ismu'),
            get_string('teachers_groups_desc', 'enrol_ismu'),
            ''
        )
    );
    
    $settings->add(
        new admin_setting_configtext(
            'enrol_ismu/studentsgroups',
            get_string('students_groups', 'enrol_ismu'),
            get_string('students_groups_desc', 'enrol_ismu'),
            ''
        )
    );
    
}
