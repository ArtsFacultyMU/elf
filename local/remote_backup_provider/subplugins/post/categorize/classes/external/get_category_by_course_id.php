<?php

namespace remotebppost_categorize\external;

class get_category_by_course_id {
    /**
     * Get category information.
     *
     * @param int $id the category ID.
     * @return array|bool An array with information or false on failure.
     */
    public static function get_category_by_course_id($data) {
        global $DB;
        
        if (!isset($data->id) || !is_number($data->id)) {
            throw new \moodle_exception('ID not set');    
        }

        // Capability check.
        if (!has_capability('moodle/course:viewhiddencourses', \context_system::instance())) {
            throw new \moodle_exception('Invalid capability');  
        }

        $course = $DB->get_record('course', array('id' => $data->id));

        if ($course === false) throw new \moodle_exception('Unknown course');  
        
        return ['category' => $course->category];
    }
  
}