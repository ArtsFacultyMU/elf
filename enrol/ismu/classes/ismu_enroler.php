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

class ismu_enroler
{
    const ISMU_STUDENTS_NO_IMPORT =  0;
    const ISMU_STUDENTS_IMPORT_REGISTERED =  1;
    const ISMU_STUDENTS_IMPORT_ENROLLED =  2;

    const ISMU_SEMINARS_NO_CREATE =  0;
    const ISMU_SEMINARS_CREATE =  1;
    
    public static function filter_course_codes($courseCodesString)
    {
        return array_map(function($data) { return trim($data); }, explode(',', $courseCodesString));
    }
    
    public function get_students_to_enrol(array $codes, $enrolStaus)
    {
        $students = [];
        foreach($codes as $code) {
            if(strpos($code, '/') !== false) {
                $students = array_merge($students, $this->get_course_group_students($code));
            } else {
                $students = array_merge($students, $this->get_course_students($code, $enrolStaus));
            }
        }
        return $students;
    }
    
    public function get_course_students($courseCode, $enrolStatus)
    {
        global $DB;
        switch ($enrolStatus) {
            case self::ISMU_STUDENTS_IMPORT_REGISTERED :
                $query = "SELECT DISTINCT(student.id) AS userid 
                    FROM
                        (SELECT su.username FROM (SELECT studyid FROM {ismu_studies} WHERE coursecode LIKE ?) AS st 
                            INNER JOIN {ismu_students} AS su ON st.studyid = su.studyid) AS studium 
                        INNER JOIN {user} AS student ON studium.username = student.username";
                break;
            case self::ISMU_STUDENTS_IMPORT_ENROLLED :
                $query = "SELECT DISTINCT(student.id) AS userid 
                    FROM
                        (SELECT su.username FROM 
                            (SELECT studyid FROM {ismu_studies} WHERE coursecode LIKE ? AND enroldate != '') AS st 
                            INNER JOIN {ismu_students} AS su ON st.studyid = su.studyid) AS studium 
                        INNER JOIN {user} AS student ON studium.username = student.username";
                break;
        }

        return array_map(
            function($data) { return $data->userid; },
            $DB->get_records_sql($query, [$this->sanitize_code($courseCode)])
        );
    }
    
    public function get_course_group_students($seminarCode)
    {
        global $DB;
        $query = "SELECT DISTINCT(user.id) AS userid 
            FROM
                (SELECT su.username FROM 
                    (SELECT studyid FROM {ismu_studies} WHERE groupcode LIKE ? AND enroldate != '') AS st  
                    INNER JOIN {ismu_students} AS su ON st.studyid = su.studyid) AS studium
                INNER JOIN {user} AS user ON studium.username = user.username";

        return array_map(
            function($data) { return $data->userid; },
            $DB->get_records_sql($query, [$this->sanitize_code($seminarCode)])
        );
    }
    
    public function get_groups_to_create(array $codes) 
    {
        $groups = [];
        foreach ($codes as $code) {
            if(strpos($code, '/') !== false) {
                $groups[] = $code;
            } else {
                $groups = array_merge($groups, $this->get_course_groups($code));
            }
        }
        return array_filter($groups);
    }
    
    public function get_course_groups($courseCode)
    {
        global $DB;
        $query = "SELECT DISTINCT(groupcode) AS grp FROM {ismu_studies} "
                . "WHERE coursecode LIKE ? AND groupcode IS NOT NULL";
        return array_map(
            function($data) { return $data->grp; },
            $DB->get_records_sql($query, [$this->sanitize_code($courseCode)])
        );
    }
    
    public function get_students() 
    {
        global $DB;
        return array_map(
            function($data) {return $data->userid;}, 
            $DB->get_records_sql(
                    "SELECT DISTINCT(mdl.id) AS userid FROM {ismu_students} AS ismu "
                    . "INNER JOIN {user} AS mdl ON ismu.username = mdl.username"
            )
        );
    }
    
    public function get_teachers() 
    {
        global $DB;
        return array_map(
            function($data) {return $data->userid;}, 
            $DB->get_records_sql(
                    "SELECT DISTINCT(mdl.id) AS userid FROM {ismu_teachers} AS ismu "
                    . "INNER JOIN {user} AS mdl ON ismu.username = mdl.username"
            )
        );
    }
    
    protected function sanitize_code($code)
    {
        return str_replace('_', '\_', $code);
    }
    
    public function get_not_inserted_students()
    {
        global $DB;
        return $DB->get_records_sql(
            "SELECT DISTINCT(username) AS username, uco, firstname, surname FROM {ismu_students} "
                . "WHERE username NOT IN (SELECT username FROM {user})"
        );
    }
    
    public function get_not_inserted_teachers() 
    {
        global $DB;
        return $DB->get_records_sql(
            "SELECT DISTINCT(username) AS username, uco, firstname, surname FROM {ismu_teachers} "
                . "WHERE username NOT IN (SELECT username FROM {user})"
        );
    }
}
