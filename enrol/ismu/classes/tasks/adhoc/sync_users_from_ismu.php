<?php

namespace enrol_ismu\tasks\adhoc;

class sync_users_from_ismu extends \core\task\adhoc_task 
{

    /**
     * Specs:
     * enrol users from ISMU
     */
    public function execute()
    {
        global $CFG;
        require_once("{$CFG->dirroot}/group/lib.php");
        $data = $this->get_custom_data();
        $helper = new \enrol_ismu\helper;
        $moodleEnroler = new \enrol_ismu\moodle_enroler;
        $ismuEnroler = new \enrol_ismu\ismu_enroler;
		require_once(__DIR__ . '/../../../lib.php');
        $enrolPlugin = new \enrol_ismu_plugin;
        
        $instance = $moodleEnroler->get_instance_by_course_id($data->courseid);
        if($instance->customchar2 != $helper->get_current_period()['full'] 
            || $instance->customint1 == \enrol_ismu\helper::ISMU_STUDENTS_NO_IMPORT
            || empty(trim($instance->customchar1))) {
            return;
        }
        
        $courseCodes = \enrol_ismu\ismu_enroler::filter_course_codes($instance->customchar1);
        $currentUsers = $moodleEnroler->get_enroled_students($instance->id);
        $ismuUsers = $ismuEnroler->get_students_to_enrol($courseCodes, $instance->customint1);
        
        $enrolPlugin->sync_course_users($instance, $currentUsers, $ismuUsers, $data->roleId);
        
        //customint2 stands for create seminars
        if ($instance->customint2 == \enrol_ismu\helper::ISMU_SEMINARS_CREATE) {
            //SYNCHRONIZACIA SKUPIN V MOODLE
            $currentGroups = array_map(
                function($data) { return $data->name; }, 
                $moodleEnroler->get_course_groups($data->courseid)
            );
            $ismuGroups = $ismuEnroler->get_groups_to_create($courseCodes);
            
            $groupsToDelete = array_diff($currentGroups, $ismuGroups);
            $groupsToCreate = array_diff($ismuGroups, $currentGroups);
        
            foreach ($groupsToDelete as $groupId => $g) {
                $moodleEnroler->delete_group($groupId);
            }
            foreach ($groupsToCreate as $group) {
                $moodleEnroler->create_group($data->courseid, $group);
            }
            //if there are no groups from ISMU
            if(!count($ismuGroups)) {
                return;
            }
            
            $groups = $moodleEnroler->get_course_groups($data->courseid);
            foreach($groups as $group) {
                $currentGroupUsers = $moodleEnroler->get_course_group_students($group->id, $currentUsers);
                $ismuGroupUsers = $ismuEnroler->get_course_group_students($group->name);
                
                $usersToUnenrol = array_diff($currentGroupUsers, $ismuGroupUsers);
                $usersToEnrol = array_diff($ismuGroupUsers, $currentGroupUsers);
                foreach ($usersToUnenrol as $user) {
                    groups_remove_member($group->id, $user);
				}
                foreach ($usersToEnrol as $user) {
                    groups_add_member($group->id, $user);
                }
            }
        } else {
            //delete old seminars from course
            groups_delete_groups($data->courseid);
        } 
    }

}