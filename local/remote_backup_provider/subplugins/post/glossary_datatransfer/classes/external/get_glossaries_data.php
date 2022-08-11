<?php

namespace remotebppost_glossary_datatransfer\external;

class get_glossaries_data {
    /**
     * Get category information.
     *
     * @param int $id the category ID.
     * @return array|bool An array with information or false on failure.
     */
    public static function get_glossaries_data($data) {
        global $DB;
        
        if (!isset($data->course) || !is_number($data->course)) {
            throw new \moodle_exception('ID not set');    
        }

        // Capability check.
        if (!has_capability('moodle/course:viewhiddencourses', \context_system::instance())) {
            throw new \moodle_exception('Invalid capability');  
        }

        $course = $DB->get_record('course', 
                array('id' => $data->course),
                '*', MUST_EXIST);

        if ($course === false) {throw new \moodle_exception('Invalid course');}
        
        $glossaries = [];
        foreach(get_all_instances_in_course('glossary', $course) as $instance) {
            $glossary = $DB->get_record('glossary', array('id'=>$instance->id));
            if ($glossary === false) {throw new \moodle_exception('Invalid data');}
            $glossaries[] = base64_encode(glossary_generate_export_file($glossary));

        }
        
        return ['glossaries' => $glossaries];
    }
  
}