<?php

namespace enrol_ismu\tasks\adhoc;

class sync_users_from_archive extends \core\task\adhoc_task 
{
    public function execute()
    {
        $data = $this->get_custom_data();
        $archiver = new \enrol_ismu\archiver;
        $enroler = new \enrol_ismu\moodle_enroler;
        $enrolPlugin = new \enrol_ismu_plugin;
        
        //check if there are any archived users
        $archivedUsers = $archiver->get_archived_users($data->courseid, $data->period);
        if(!count($archivedUsers)) {
            return;
        }
        
        //cleanup current course from enroled users and groups
        $instance = \enrol_ismu\moodle_enroler::get_instance_by_id($data->enrolid);
        $enrolPlugin->clean_instance($instance);

        //restore archived data for selected period
        /// Adding a role if applicable
        if($data->roleId !== null) {
            $context = context_course::instance($data->courseid, MUST_EXIST);
        }

        foreach($archivedUsers as $user) {
            $enrolPlugin->enrol_user($instance, $user);
            if($data->roleId !== null) {role_assign($data->roleId, $user, $context->id);}
        }
        $archivedGroups = $archiver->get_archived_groups($data->courseid, $data->period);
        foreach($archivedGroups as $archivedGroup) {
            $groupId = $enroler->create_group($data->courseid, $archivedGroup->name);
            
            $archivedGroupUsers = $archiver->get_archived_group_users($archivedGroup->id);
            foreach($archivedGroupUsers as $groupUser) {
                groups_add_member($groupId, $groupUser);
            }
        }
    }

}