<?php

namespace enrol_ismu\tasks\cron;

class sync_data_from_ismu extends \core\task\scheduled_task 
{
    // STATIC SETTINGS
    const DATA_URL = 'https://is.muni.cz/export/studium_export_data.pl?fak=%s;obd=%s;format=dvojt;kodovani=il2;typ=%s';
    const DATA_TYPE_STUDENTS = 1;
    const DATA_TYPE_STUDIES = 2;
    const DATA_TYPE_TEACHERS = 3;
    
    const FACULTY_PHIL = 1421; // Faculty of Arts.
    const FACULTY_MED = 1411; // Faculty of Medicine.
    const FACULTY_LAW = 1422; // Faculty of Law.
    const FACULTY_FSS = 1423; // Faculty of Social Studies.
    const FACULTY_SCI = 1431; // Faculty of Science.
    const FACULTY_FI = 1433; // Faculty of Informatics.
    const FACULTY_PED = 1441; // Faculty of Education.
    const FACULTY_FSPS = 1451; // Faculty of Sports Studies.
    const FACULTY_ECON = 1456; // Faculty of Economics and Administration.
    const CUS = 1490; // Pan-university studies.

    /**
     * List of involved faculties
     * @var array
     */
    private static $interested_faculties = array(
        self::FACULTY_FSS,
        self::FACULTY_PHIL,
        self::FACULTY_FSPS,
        self::FACULTY_SCI
    );










    // PRIMARY CRON METHODS

    /**
     * The main cron function.
     *
     * Is ran when the cron is started.
     */
    public function execute()
    {
        $ismuEnroler = new \enrol_ismu\ismu_enroler;
        $moodleEnroler = new \enrol_ismu\moodle_enroler;

        if($this->download_data_from_is_mu($ismuEnroler)) {
            $this->update_current_enrolments($moodleEnroler);
        } else {
            //todo add notifications
        }
    }

    /**
     * Returns public name of this cron.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_name()
    {
        return get_string('task_sync_data_from_ismu', 'enrol_ismu');
    }










    // DOWNLOADING FROM IS (and helpers)

    /**
     * Downloads data from IS MUNI and stores them in the special db tables
     *
     * @param \enrol_ismu\ismu_enroler $ismuEnroller
     * @return bool
     * @throws \dml_transaction_exception
     */
    protected function download_data_from_is_mu(\enrol_ismu\ismu_enroler $ismuEnroller)
    {
        global $DB;

        // Creating period code
        $ismuPeriods = [
            \enrol_ismu\helpers\semester::SEMESTER_SPRING => 'jaro',
            \enrol_ismu\helpers\semester::SEMESTER_AUTUMN => 'podzim'];
        $currentsemester = \enrol_ismu\helpers\semester::get_current_semester();
        $period = $ismuPeriods[$currentsemester->semester()] . '%20' . $currentsemester->year();


        // Starting enrolments
        $transaction = $DB->start_delegated_transaction();

        try {
            /// Remove old database data
            $DB->delete_records('ismu_students');
            $DB->delete_records('ismu_teachers');
            $DB->delete_records('ismu_studies');

            /// Enrols teacher, students and studies into module-specific tables
            foreach(self::$interested_faculties as $faculty) {
                $this->download_teachers_from_is_mu($period, $faculty);
                $this->download_students_from_is_mu($period, $faculty);
                $this->download_studies_from_is_mu($period, $faculty);
            }

            // Inserts teachers and students not in moodle yet
            //@todo Change to sync missing users + adding COURSE_CREATOR role afterwards
            $this->sync_missing_teachers($ismuEnroller->get_not_inserted_teachers());
            $this->sync_missing_students($ismuEnroller->get_not_inserted_students());

            $transaction->allow_commit();

        } catch (\Exception $ex) {
            $transaction->rollback($ex);
            error_log('[ERROR: enrol_ismu\cron\ismu\download_data_from_is_mu] Ended unsuccessfully. The exception follows.');
            error_log($ex->getTraceAsString());
            return false;
        }
        return true;
    }

    /**
     * Fills special table with students from IS MU
     *
     * @param $period
     * @param $faculty
     * @param \enrol_ismu\ismu_enroler $ismuEnroller
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function download_students_from_is_mu($period, $faculty)
    {
        global $DB;
        $dataUrl = sprintf(self::DATA_URL, $faculty, $period, self::DATA_TYPE_STUDENTS);
        $handle = fopen($dataUrl, 'r');
        if ($handle) {
            $students = [];
            while (($line = fgets($handle)) !== false) {
                list($uco,,$surname,$firstname,,$studyId,) = explode(':',$line);
                $uco = trim($uco);
                if(!empty($uco)) {
                  $student = new \stdClass();
                  $student->uco = $uco;
                  $student->username = $uco . '@muni.cz';
                  $student->surname = trim($surname);
                  $student->firstname = trim($firstname);
                  $student->studyid = trim($studyId);
                  $students[] = $student;
                }
            }
            if ($students) {$DB->insert_records('ismu_students', $students, false);}
            fclose($handle);
        } else {
            error_log('[ERROR: enrol_ismu\cron\ismu\download_students_from_is_mu] Could not download students from IS MU for faculty ID ' . $faculty . '.');
            throw new \Exception('Could not download students from IS MU for faculty ' . $faculty);
        }
    }

    /**
     * Fills special table with teachers from IS MU
     *
     * @param $period
     * @param $faculty
     * @param \enrol_ismu\ismu_enroler $ismuEnroller
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function download_teachers_from_is_mu($period, $faculty)
    {
        global $DB;
        $dataUrl = sprintf(self::DATA_URL, $faculty, $period, self::DATA_TYPE_TEACHERS);
        $handle = fopen($dataUrl, 'r');
        if ($handle) {
            $teachers = [];
            while (($line = fgets($handle)) !== false) {
                list($uco,,$surname,$firstname,) = explode(':',$line);
                $uco = trim($uco);
                if(!empty($uco)) {
                  $teacher = new \stdClass();
                  $teacher->uco = $uco;
                  $teacher->username = $uco . '@muni.cz';
                  $teacher->surname = trim($surname);
                  $teacher->firstname = trim($firstname);
                  $teachers[] = $teacher;
                }
            }
            if ($teachers) {$DB->insert_records('ismu_teachers', $teachers, false);}
            fclose($handle);
        } else {
            error_log('[ERROR: enrol_ismu\cron\ismu\download_teachers_from_is_mu] Could not download teachers from IS MU for faculty ID ' . $faculty . '.');
            throw new \Exception('Could not download teachers from IS MU for faculty ' . $faculty);
        }
    }

    /**
     * Fills special table with studies from IS MU
     *
     * @param $period
     * @param $faculty
     * @param \enrol_ismu\ismu_enroler $ismuEnroller
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function download_studies_from_is_mu($period, $faculty)
    {
        global $DB;
        $dataUrl = sprintf(self::DATA_URL, $faculty, $period, self::DATA_TYPE_STUDIES);
        $handle = fopen($dataUrl, 'r');
        if ($handle) {
            $studies = [];
            while (($line = fgets($handle)) !== false) {
                list($courseCode,$studyId,$enrolDate,$group) = explode(':',$line);
                $study = new \stdClass();
                $study->coursecode = trim($courseCode);
                $study->studyid = trim($studyId);
                $study->enroldate = trim($enrolDate);
                $study->groupcode = trim($group);
                $studies[] = $study;
            }
            if ($studies) {$DB->insert_records('ismu_studies', $studies, false);}
            fclose($handle);
        } else {
            error_log('[ERROR: enrol_ismu\cron\ismu\download_studies_from_is_mu] Could not download studies from IS MU for faculty ID ' . $faculty . '.');
            throw new \Exception('Could not download studies from IS MU for faculty ' . $faculty);
        }
    }






    // IS MU data <-> MOODLE synchronization
    protected function update_current_enrolments(\enrol_ismu\moodle_enroler $moodleEnroller) {
        $currentsemester = \enrol_ismu\helpers\semester::get_current_semester();
        $courses = $moodleEnroller->get_active_courses($currentsemester->full());
        foreach($courses as $course) {
            \enrol_ismu\helpers\tasks::sync_students_from_ismu($course);
        }
    }

    /**
     * Creates new Moodle users from not enrolled IS users.
     *
     * @param array $notEnrolledStudents Array of not imported IS users
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function sync_missing_students(array $notEnrolledStudents)
    {
        // Gets database object
        global $DB;

        $users = [];

        // Iterates over not-enrolled users
        foreach ($notEnrolledStudents as $newUser) {
            // Creates standard-class user object
            $user = $this->get_default_moodle_user_object();

            // Rewrites information specific for this user
            $user->username = $newUser->username;
            $user->firstname = addslashes($newUser->firstname);
            $user->lastname = addslashes($newUser->surname);
            $user->email = $newUser->uco.'@mail.muni.cz';
            $users[] = $user;
        }

        if ($users) {$DB->insert_records('user', $users);}
    }

    /**
     * Creates new Moodle users from not enrolled IS users.
     *
     * @param array $notEnrolledUsers Array of not imported IS users
     * @param bool $teachers Set TRUE if users are teachers
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function sync_missing_teachers(array $notEnrolledUsers)
    {
        // Gets database object
        global $DB;

        // Creates standard-class user object
        $user = $this->get_default_moodle_user_object();

        // Iterates over not-enrolled users
        foreach ($notEnrolledUsers as $newUser) {
            // Rewrites information specific for this user
            $user->username = $newUser->username;
            $user->firstname = addslashes($newUser->firstname);
            $user->lastname = addslashes($newUser->surname); 
            $user->email = $newUser->uco.'@mail.muni.cz';

            // Inserts information about user into the database and gets context
            $userId = $DB->insert_record('user', $user);
            $context = \context_system::instance();

            // If teacher doesn't have "Course creator" role, adds it
            $coursecreatorrole = $DB->get_record('role', ['shortname' => 'coursecreator']);
            if(!user_has_role_assignment($userId, $coursecreatorrole->id, $context->id))
                role_assign($coursecreatorrole->id, $userId, $context->id);
        }
    }









    // HELPERS
    /**
     * Returns default moodle user object
     *
     * @return \stdClass
     */
    protected function  get_default_moodle_user_object() {
        $user = new \stdClass;

        $user->auth = 'shibboleth';
        $user->confirmed = 1;
        $user->mnethostid = 1;
        $user->password = 'not cached';
        $user->idnumber = '';
        $user->icq = '';
        $user->skype = '';
        $user->yahoo = '';
        $user->aim = '';
        $user->msn = '';
        $user->phone1 = '';
        $user->phone2 = '';
        $user->institution = '';
        $user->department = '';
        $user->address = '';
        $user->city = 'Brno';
        $user->country = 'CZ';
        $user->lang = 'cs';
        $user->theme = '';
        $user->lastip = '';
        $user->secret = '';
        $user->url = '';
        $user->timecreated = time();

        return $user;
    }
}