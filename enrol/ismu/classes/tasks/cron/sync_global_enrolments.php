<?php

namespace enrol_ismu\tasks\cron;

class sync_global_enrolments extends \core\task\scheduled_task 
{
    public function execute()
    {
        global $CFG;
        require_once("{$CFG->dirroot}/group/lib.php");
        require_once("{$CFG->dirroot}/mod//forum/lib.php");
        $ismuEnroler = new \enrol_ismu\ismu_enroler;
        $moodleEnroler = new \enrol_ismu\moodle_enroler;
		require_once(__DIR__ . '/../../../lib.php');
        $enrolPlugin = new \enrol_ismu_plugin;
        
        $teachers = $ismuEnroler->get_teachers();
        $this->sync_courses(
            $this->str_to_ids(get_config('enrol_ismu', 'teacherscourses')),
            $teachers,
            $enrolPlugin,
            $moodleEnroler
        );
        $this->sync_forums(
            $this->str_to_ids(get_config('enrol_ismu', 'teachersforums')),
            $teachers
        );
        $this->sync_groups(
            $this->str_to_ids(get_config('enrol_ismu', 'teachersgroups')),
            $teachers,
            $moodleEnroler
        );
        unset($teachers);
        
        $students = $ismuEnroler->get_students();
        $this->sync_courses(
            $this->str_to_ids(get_config('enrol_ismu', 'studentscourses')),
            $students,
            $enrolPlugin,
            $moodleEnroler
        );
        $this->sync_forums(
            $this->str_to_ids(get_config('enrol_ismu', 'studentsforums')),
            $students
        );
        $this->sync_groups(
            $this->str_to_ids(get_config('enrol_ismu', 'studentsgroups')),
            $students,
            $moodleEnroler
        );
        unset($students);
        
    }

    public function get_name()
    {
        return get_string('task_sync_global_enrolments', 'enrol_ismu');
    }

    private function str_to_ids($string) 
    {
        return array_filter(array_map('trim',explode(",", (string)$string)));
    }

    private function sync_courses(
        array $courseIds, 
        array $users, 
        \enrol_ismu_plugin $enrolPlugin, 
        \enrol_ismu\moodle_enroler $moodleEnroler
    ) {
        global $DB;
        foreach ($courseIds as $course) {
            $instance = \enrol_ismu\moodle_enroler::get_instance_by_course_id($course);
            if(!$instance) {
                $enrolPlugin->add_default_instance((object) ['id' => $course]);
                $instance = \enrol_ismu\moodle_enroler::get_instance_by_course_id($course);
            }
            $currentUsers = $moodleEnroler->get_enroled_students($instance->id);
            
            $studentrole = $DB->get_record('role', ['shortname' => 'student']);
            $enrolPlugin->sync_course_users($instance, $currentUsers, $users, $studentrole->id);
        }
    }
    
    private function sync_forums(array $forumIds, array $users) 
    {
        global $DB;
        foreach ($forumIds as $forumId) {
            $cm = get_coursemodule_from_instance('forum', $forumId);
            if(!$cm) {
                echo "Forum with ID: " . $forumId . " does not exists.\n";
                continue;
            }
            $forum = $DB->get_record('forum', ['id' => $forumId]);
            $context = \context_module::instance($cm->id);
            $currentSubscribers = array_map(
                function($data) { return $data->id; }, 
                \mod_forum\subscriptions::fetch_subscribed_users($forum, 0, $context, "u.id,u.email")
            );
                
            $usersToUnsubscribe = array_diff($currentSubscribers, $users);
            $usersToSubscribe = array_diff($users, $currentSubscribers);

            foreach ($usersToUnsubscribe as $user) {
                \mod_forum\subscriptions::unsubscribe_user($user, $forum, $context);
            }
            foreach ($usersToSubscribe as $user) {
                \mod_forum\subscriptions::subscribe_user($user, $forum, $context);
            }
        }
    }
    
    private function sync_groups(array $groupIds, array $users, \enrol_ismu\moodle_enroler $moodleEnroler)
    {
        foreach($groupIds as $group) {
            $currentGroupUsers = $moodleEnroler->get_course_group_students($group);
            
            $usersToUnenrol = array_diff($currentGroupUsers, $users);
            $usersToEnrol = array_diff($users, $currentGroupUsers);

            foreach ($usersToUnenrol as $user) {
                groups_remove_member($group, $user);
            }
            foreach ($usersToEnrol as $user) {
                groups_add_member($group, $user);
            }
        }
    }
}