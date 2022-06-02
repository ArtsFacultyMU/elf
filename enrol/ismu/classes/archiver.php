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

class archiver
{

    public function get_archived_users($courseId, $period) 
    {
        global $DB;
        return array_map(
                function($data) { return $data->userid; },
                $DB->get_records_sql(
                    "SELECT userid FROM {ismu_archived_enrolments} WHERE courseid = ? AND period = ?", 
                    [$courseId, $period]
                )
        );
    }
    
    public function get_archived_groups($courseId, $period)
    {
        global $DB;
        return $DB->get_records_sql(
            "SELECT * FROM {ismu_archived_groups} WHERE courseid = ? AND period = ?", 
            [$courseId, $period]
        );
    }
    
    public function get_archived_group_users($groupId)
    {
        global $DB;
        return array_map(
                function($data) { return $data->userid; },
                $DB->get_records_sql(
                    "SELECT userid FROM {ismu_archived_group_enrolments} WHERE groupid = ?", 
                    [$groupId,]
                )
        );
    }
    
    public function archive_users($users, $courseId, $period)
    {
        global $DB;
        $archiveInstance = (object) ['courseid' => $courseId, 'period' => $period];
        foreach ($users as $user) {
            $archiveInstance->userid = $user;
            $DB->insert_record('ismu_archived_enrolments', $archiveInstance);
        }
    }
    
    public function archive_group($group, $users, $courseId, $period)
    {
        global $DB;
        $archivedGroupId = $DB->insert_record(
            'ismu_archived_groups',
            (object) ['courseid' => $courseId, 'period' => $period, 'name' => $group->name]
        );
        $archiveInstance = (object) ['groupid' => $archivedGroupId];
        foreach($users as $user) {
            $archiveInstance->userid = $user;
            $DB->insert_record('ismu_archived_group_enrols', $archiveInstance);
        }
    }
}
