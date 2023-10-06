<?php

namespace remotebppost_newassignment_detector\external;

class get_newassignment_count {
    /**
     * Returns number of newassignment instances found in given course.
     *
     * 
     * @param int $course the course ID.
     * @return int Number of newassignment instances found.
     */
    public static function get_newassignment_count($data) {
        global $DB;
        
        if (!isset($data->course) || !is_number($data->course)) {
            throw new \moodle_exception('Course ID not set');    
        }

        // Capability check.
        if (!has_capability('moodle/course:viewhiddencourses', \context_system::instance())) {
            throw new \moodle_exception('Invalid capability');  
        }

        // Check if the course ID is valid.
        $course = $DB->get_record('course', array('id' => $data->course));
        if ($course === false) throw new \moodle_exception('Unknown course');  

        // If the newassignment plugin is not installed, treat it as 0 instances.
        if (!array_key_exists('newassignment', \core_component::get_plugin_list('mod'))) {
            return ['newassignment_count' => 0];
        }

        return ['newassignment_count' => count(get_all_instances_in_course('newassignment', $course))];
    }
  
}