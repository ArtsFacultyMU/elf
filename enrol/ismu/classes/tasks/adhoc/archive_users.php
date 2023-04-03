<?php

namespace enrol_ismu\tasks\adhoc;

class archive_users extends \core\task\adhoc_task 
{

    /**
     * Specs:
     * enrol users from ISMU
     */
    public function execute()
    {
        $data = $this->get_custom_data();
        $archiver = new \enrol_ismu\archiver;
        $enroler = new \enrol_ismu\moodle_enroler;
        
        $enroledUsers = $enroler->get_enroled_students($data->enrolid);
        if(!count($enroledUsers)) {
            return;
        }
        
        $archivedUsers = $archiver->get_archived_users($data->courseid, $data->period);
        if(count($archivedUsers)) {
            return;
        }
        
        $archiver->archive_users($enroledUsers, $data->courseid, $data->period);
        
        $groups = $enroler->get_course_groups($data->courseid);
        if(count($groups)) {
            foreach($groups as $group) {
                $groupUsers = $enroler->get_course_group_students($group->id, $enroledUsers);
                $archiver->archive_group($group, $groupUsers, $data->courseid, $data->period);
            }
        }
    }

}