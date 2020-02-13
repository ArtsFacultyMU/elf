<?php

/**
 * Archiver class
 *
 * @package    enrol_ismu
 * @copyright  2016 Masaryk University
 * @author     Filip Benco
 */
namespace enrol_ismu;

defined('MOODLE_INTERNAL') || die();

class moodle_enroler
{
    public static function get_instance_by_id($enrolId)
    {
        global $DB;
        return $DB->get_record('enrol', ['id' => $enrolId]);
    }
    
    public static function get_instance_by_course_id($courseId)
    {
        global $DB;
        return $DB->get_record('enrol', ['courseid' => $courseId, 'enrol' => 'ismu']);
    }
    
    public function get_enroled_students($enrolId)
    {
        global $DB;
        $dbStudents = $DB->get_records_sql("SELECT userid FROM {user_enrolments} WHERE enrolid = ?", [$enrolId]);
        return array_map(function($data) { return $data->userid; }, $dbStudents);
    }
    
    public function get_active_courses($period)
    {
        global $DB;
        $courses = $DB->get_records_sql(
            "SELECT courseid FROM {enrol} WHERE enrol = ? AND customchar2 LIKE ? AND status = 0",
            ['ismu', $period]
        );
        return array_map(function($data) { return $data->courseid; }, $courses);
    }
    
    public function get_course_groups($courseId)
    {
        $groups = [];
        foreach(groups_get_all_groups($courseId) as $group) {
            if(!empty($group->name)) {
                $groups[$group->id] = (object) ['id' => $group->id, 'name' => $group->name];
            }
        }
        return $groups;
    }
    
    public function get_course_group_students($groupId, $courseStudents = null)
    {
        global $DB;
        $members = $DB->get_records_sql(
            "SELECT DISTINCT(userid) AS userid FROM {groups_members} WHERE groupid = ?",
            [$groupId]
        );
        if($courseStudents !== null) {
            $members = array_filter(
                $members, 
                function($data) use($courseStudents) { return in_array($data->userid, $courseStudents); }
            );
        } 
        return array_map(function($data) { return $data->userid; }, $members);
    }
    
    public function create_group($courseId, $name) {
        return groups_create_group(
            (object) [
                'courseid' => $courseId,
                'description' => 'Automatically set up(IS import)',
                'name' => $name
            ]
        );
    }
    
    public function delete_group($groupId) {
        groups_delete_group($groupId);
    }
}
