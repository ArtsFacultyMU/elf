<?php

namespace enrol_ismu\tasks\cron;
define('COURSE_CREATOR',2);

class sync_data_from_ismu extends \core\task\scheduled_task 
{
    public function execute()
    {
        $helper = new \enrol_ismu\helper;
        $ismuEnroler = new \enrol_ismu\ismu_enroler;
        $moodleEnroler = new \enrol_ismu\moodle_enroler;
        $period = $this->convert_period_to_ismu_code($helper->get_current_period());
        if($this->download_data_from_is_mu($period, $ismuEnroler)) {
            $this->update_current_enrolments($moodleEnroler, $helper);
        } else {
            //todo add notifications
        }
    }

    public function get_name()
    {
        return get_string('task_sync_data_from_ismu', 'enrol_ismu');
    }

    protected function convert_period_to_ismu_code($period)
    { 
        $ismuPeriods = array(\enrol_ismu\helper::ELF_PERIOD_SPRING => 'jaro', \enrol_ismu\helper::ELF_PERIOD_AUTUMN => 'podzim');
        return $ismuPeriods[$period['period']] . '_' . $period['year'];
    }

    protected function download_data_from_is_mu($period, \enrol_ismu\ismu_enroler $ismuEnroller)
    {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        try {
            // Generates list of faculty IDs interested in ELF
            $interested_faculties = array(
                \enrol_ismu\helper::ELF_FACULTY_FSS,
                \enrol_ismu\helper::ELF_FACULTY_PHIL,
                \enrol_ismu\helper::ELF_FACULTY_FSPS,
                \enrol_ismu\helper::ELF_FACULTY_SCI
            );

            // Enrols teacher, students and studies
            $ismuEnroller->delete_teachers();
            foreach($interested_faculties as $faculty) {
                $this->download_teachers_from_is_mu($period, $faculty, $ismuEnroller);
            }
            $this->sync_missing_users($ismuEnroller->get_not_inserted_teachers(), true);
            
            $ismuEnroller->delete_students();
            foreach($interested_faculties as $faculty) {
                $this->download_students_from_is_mu($period, $faculty, $ismuEnroller);
            }
            $this->sync_missing_users($ismuEnroller->get_not_inserted_students(), false);
            
            $ismuEnroller->delete_studies();
            foreach($interested_faculties as $faculty) {
                $this->download_studies_from_is_mu($period, $faculty, $ismuEnroller);
            }

            $transaction->allow_commit();
        } catch (\Exception $ex) {
            $transaction->rollback($ex);
            return false;
        }
        return true;
    }
    
    protected function update_current_enrolments(
        \enrol_ismu\moodle_enroler $moodleEnroller,
        \enrol_ismu\helper $helper
    ) {
        $currentPeriod = $helper->get_current_period();
        $courses = $moodleEnroller->get_active_courses($currentPeriod['full']);
        foreach($courses as $course) {
            $helper->task_sync_users_from_ismu($course, 5);
        }
    }
    
    protected function download_students_from_is_mu($period, $faculty, \enrol_ismu\ismu_enroler $ismuEnroller)
    {
        $dataUrl = 'https://is.muni.cz/export/studium_export_data.pl?fak=' . $faculty . ';obd=' 
                . str_replace('_', '%20', $period) . ';format=dvojt;kodovani=il2;typ=1';
        $handle = fopen($dataUrl, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                list($uco,,$surname,$firstname,,$studyId,) = explode(':',$line);
                $uco = trim($uco);
                if(!empty($uco) && $uco != 0) {
                    $ismuEnroller->create_student(
                        $uco, 
                        trim($uco) . '@muni.cz', 
                        trim($surname), 
                        trim($firstname), 
                        trim($studyId)
                    );
                }
            }
            fclose($handle);
        } else {
            throw new \Exception('Could not download students from IS MU for faculty ' . $faculty);
        }
    }
    
    protected function download_teachers_from_is_mu($period, $faculty, \enrol_ismu\ismu_enroler $ismuEnroller)
    {
        $dataUrl = 'https://is.muni.cz/export/studium_export_data.pl?fak=' . $faculty . ';obd=' 
                . str_replace('_', '%20', $period) . ';format=dvojt;kodovani=il2;typ=3';
        $handle = fopen($dataUrl, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                list($uco,,$surname,$firstname,) = explode(':',$line);
                $uco = trim($uco);
                if(!empty($uco) && $uco != 0) {
                    $ismuEnroller->create_teacher($uco, trim($uco) . '@muni.cz', trim($surname), trim($firstname));
                }
            }
            fclose($handle);
        } else {
            throw new \Exception('Could not download teachers from IS MU for faculty ' . $faculty);
        }
    }
    
    protected function download_studies_from_is_mu($period, $faculty, \enrol_ismu\ismu_enroler $ismuEnroller)
    {
        $dataUrl = 'https://is.muni.cz/export/studium_export_data.pl?fak=' . $faculty . ';obd=' 
                . str_replace('_', '%20', $period) . ';format=dvojt;kodovani=il2;typ=2';
        $handle = fopen($dataUrl, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                list($courseCode,$studyId,$enrolDate,$group) = explode(':',$line);
                $ismuEnroller->create_study(trim($courseCode), trim($studyId), trim($enrolDate), trim($group));
            }
            fclose($handle);
        } else {
            throw new \Exception('Could not download studies from IS MU for faculty ' . $faculty);
        }
    }

    /**
     * Creates new Moodle users from not enrolled IS users.
     *
     * @param array $notEnrolledUsers Array of not imported IS users
     * @param bool $teachers Set TRUE if users are teachers
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function sync_missing_users(array $notEnrolledUsers, bool $teachers = null)
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

            // TEACHER-SPECIFIC: If teacher doesn't have "Course creator" role, adds it
            if(!user_has_role_assignment($userId,COURSE_CREATOR, $context->id) && $teachers)
                role_assign(COURSE_CREATOR, $userId, $context->id);
        }
    }
    
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