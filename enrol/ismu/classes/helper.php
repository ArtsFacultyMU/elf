<?php

/**
 * ISMU core class
 *
 * @package    enrol_ismu
 * @copyright  2016 Masaryk University
 * @author     Filip Benco
 */
namespace enrol_ismu;

defined('MOODLE_INTERNAL') || die();

class helper
{
    const ISMU_STUDENTS_NO_IMPORT =  0;
    const ISMU_STUDENTS_IMPORT_REGISTERED =  1;
    const ISMU_STUDENTS_IMPORT_ENROLLED =  2;

    const ISMU_SEMINARS_NO_CREATE =  0;
    const ISMU_SEMINARS_CREATE =  1;

    const ELF_PERIOD_AUTUMN = 'autumn';
    const ELF_PERIOD_SPRING = 'spring';

    const ELF_FACULTY_PHIL = 1421;
    const ELF_FACULTY_MED = 1411;
    const ELF_FACULTY_LAW = 1422;
    const ELF_FACULTY_FSS = 1423;
    const ELF_FACULTY_SCI = 1431;
    const ELF_FACULTY_FI = 1433;
    const ELF_FACULTY_PED = 1441;
    const ELF_FACULTY_FSPS = 1451;
    const ELF_FACULTY_ECON = 1456;
    const ELF_CUS = 1490;
    
    private $config;

    public function __construct()
    {
        $this->config = get_config("enrol_ismu");
    }
    
    /**
     * Returns plugin config value
     * @param  string $name
     * @param  string $default value if config does not exist yet
     * @return string value or default
     */
    public function get_config($name, $default = NULL) {
        return isset($this->config->$name) ? $this->config->$name : $default;
    }
    
    public function get_teacher_courses()
    {
        return $this->get_ids_from_string($this->get_config('teacherscourses',''));
    }
    
    public function get_teacher_forums()
    {
        return $this->get_ids_from_string($this->get_config('teachersforums',''));
    }
    
    public function get_teacher_groups()
    {
        return $this->get_ids_from_string($this->get_config('teachersgroups',''));
    }
    
    public function get_student_groups()
    {
        return $this->get_ids_from_string($this->get_config('studentsgroups',''));
    }

    public function get_student_courses()
    {
        return $this->get_ids_from_string($this->get_config('studentscourses',''));
    }
    
    public function get_student_forums()
    {
        return $this->get_ids_from_string($this->get_config('studentsforums',''));
    }
    
    public function get_current_period()
    {
        list($year, $period) = explode('_', $this->get_config('currentperiod', '2016_spring'));
        return [
            'full' => $this->get_config('currentperiod', '2016_spring'),
            'period' => $period,
            'year' => $year,
        ];
    }

    public function get_available_periods($curPeriod, $historyDepth, $future = false) 
    {
        $availablePeriods = [];
        if($future) {
            $period = $this->get_future_period($curPeriod);
            $availablePeriods[$period['year'] . '_' . $period['period']] 
                    = get_string($period['period'], 'enrol_ismu') . ' ' . $period['year'];
        }
        $availablePeriods[$curPeriod['full']] 
                = get_string($curPeriod['period'], 'enrol_ismu') . ' ' . $curPeriod['year'];
        $lastPeriod = $curPeriod;
        for ($i = 0; $i < $historyDepth; $i++) {
            $lastPeriod = $this->get_previous_period($lastPeriod);
            if ($lastPeriod['period'] == self::ELF_PERIOD_SPRING && $lastPeriod['year'] == '2012') {
                break;
            }
            $availablePeriods[$lastPeriod['year'] . '_' . $lastPeriod['period']] 
                    = get_string($lastPeriod['period'], 'enrol_ismu') . ' ' . $lastPeriod['year'];
        }
        
        return $availablePeriods;
    }
    
    public function get_previous_period($period)
    {
        if ($period['period'] == self::ELF_PERIOD_AUTUMN) {
            return ['period' => self::ELF_PERIOD_SPRING, 'year' => $period['year']];
        } else {
            return ['period' => self::ELF_PERIOD_AUTUMN, 'year' => $period['year'] - 1];
        }
    }
    
    public function get_future_period($period) {
        if ($period['period'] == self::ELF_PERIOD_SPRING) {
            return ['period' => self::ELF_PERIOD_AUTUMN, 'year' => $period['year']];
        } else {
            return ['period' => self::ELF_PERIOD_SPRING, 'year' => $period['year'] + 1];
        }
    }
    
    public function convert_ismu_to_moodle_settings($data, $settingsObject = null) {
        if($settingsObject != null) {
            $settingsObject->customint1 = $data->enrol_ismu_enrol_status;
            $settingsObject->customint2 = $data->enrol_ismu_create_seminars;
            $settingsObject->customchar1 = $data->enrol_ismu_course_codes;
            $settingsObject->customchar2 = $data->enrol_ismu_period;
            return $settingsObject;
        } else {
            return [
               'customint1' => isset($data->enrol_ismu_enrol_status) ? $data->enrol_ismu_enrol_status : 0,
               'customint2' => isset($data->enrol_ismu_create_seminars) ? $data->enrol_ismu_create_seminars : 0,
               'customchar1' => isset($data->enrol_ismu_course_codes) ? $data->enrol_ismu_course_codes : '',
               'customchar2' => isset($data->enrol_ismu_period) ? $data->enrol_ismu_period : $this->get_config('currentperiod', '2016_spring')
            ];
        }
    }
    
    public function convert_moodle_to_ismu_settings($data) {
        return (object) [
           'enrol_ismu_enrol_status' => isset($data->customint1)?$data->customint1:0,
           'enrol_ismu_create_seminars' => isset($data->customint2)?$data->customint2:0,
           'enrol_ismu_course_codes' => isset($data->customchar1)?$data->customchar1:'',
           'enrol_ismu_period' => isset($data->customchar2)?$data->customchar2:$this->get_config('currentperiod', '2016_spring')
        ];
    }
    
    public function task_sync_users_from_ismu($courseId, $roleId = null) {
        $task = new \enrol_ismu\tasks\adhoc\sync_users_from_ismu;
        $task->set_custom_data(['courseid' => $courseId, 'roleId' => $roleId]);
        \core\task\manager::queue_adhoc_task($task);
    }
    
    public function task_sync_users_from_archive($courseId, $enrolId, $period, $roleId = null) {
        $task = new \enrol_ismu\tasks\adhoc\sync_users_from_archive;
        $task->set_custom_data(['courseid' => $courseId, 'enrolid' => $enrolId, 'period' => $period, 'roleId' => $roleId]);
        \core\task\manager::queue_adhoc_task($task);
    }
    
    public function task_archive_users($courseId, $enrolId, $period) {
        $task = new \enrol_ismu\tasks\adhoc\archive_users;
        $task->set_custom_data(['courseid' => $courseId, 'enrolid' => $enrolId, 'period' => $period]);
        \core\task\manager::queue_adhoc_task($task);
    }
    
    protected function get_ids_from_string($string) 
    {
        return array_filter(array_map('trim',explode(",", $string)));
    }
}
