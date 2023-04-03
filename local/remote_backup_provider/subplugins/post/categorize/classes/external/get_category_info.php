<?php

namespace remotebppost_categorize\external;

class get_category_info {
    /**
     * Get category information.
     *
     * @param int $id the category ID.
     * @return array|bool An array with information or false on failure.
     */
    public static function get_category_info($data) {
        global $DB;
        
        if (!isset($data->id) || !is_number($data->id)) {
            throw new \moodle_exception('ID not set');    
        }

        // Capability check.
        if (!has_capability('moodle/course:viewhiddencourses', \context_system::instance())) {
            throw new \moodle_exception('Invalid capability');  
        }

        $category = $DB->get_record('course_categories', array('id' => (int)$data->id));

        if ($category === false) throw new \moodle_exception('Unknown category');  
        return [
            'id' => $category->id,
            'name' => $category->name,
            'idnumber' => $category->idnumber,
            'path' => $category->path,
            'visible' => (int)$category->visible,
        ];
    }
  
}