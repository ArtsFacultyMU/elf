<?php

/**
 * IS MU enrolment plugin.
 *
 * This plugin synchronises enrolment, roles and groups with databases agains IS MU
 *
 * @package    enrol
 * @subpackage ismu
 * @author     2012 Filip Benčo
 */
defined('MOODLE_INTERNAL') || die();

define('STUDENT_ROLE', 5);

/**
 * IS MU enrolment plugin implementation.
 * @author  Filip Benčo
 */
class enrol_ismu_plugin extends enrol_plugin {

    private $helper;
    
    public function __construct()
    {
        $this->helper = new \enrol_ismu\helper;
    }
    
    /**
     * We are a good plugin and don't invent our own UI/validation code path.
     *
     * @return boolean
     */
    public function use_standard_editing_ui() 
    {
        return true;
    }
    
    /**
     * Adds enrol instance UI to course edit form
     *
     * @param object $instance enrol instance or null if does not exist yet
     * @param MoodleQuickForm $mform
     * @param object $data
     * @param object $context context of existing course or parent category if course does not exist
     * @return void
     */
    public function course_edit_form($instance, MoodleQuickForm $mform, $data, $context) 
    {
        $mform->addElement('header', 'enrol_ismu_header', $this->get_instance_name($instance));

        $this->edit_instance_form($instance, $mform, $context);
	
        if ($instance) {
            $data->enrol_ismu_course_codes = $instance->customchar1;
            $data->enrol_ismu_enrol_status = $instance->customint1;
            $data->enrol_ismu_create_seminars = $instance->customint2;
            $data->enrol_ismu_period = $instance->customchar2;
        }
    }

    /**
     * Adds form elements to add/edit instance form.
     *
     * @since Moodle 3.1
     * @param object $instance enrol instance or null if does not exist yet
     * @param MoodleQuickForm $mform
     * @param context $context
     * @return void
     */
    public function edit_instance_form($instance, MoodleQuickForm $mform, $context) 
    {
        global $PAGE;
        
        $currentPeriod = $this->helper->get_current_period();
        $availalePeriods = $this->helper->get_available_periods($currentPeriod, 4);
        $mform->addElement('select', 'enrol_ismu_period', get_string('period', 'enrol_ismu'), $availalePeriods);
        $mform->addHelpButton('enrol_ismu_period', 'period', 'enrol_ismu');

        $mform->addElement('text', 'enrol_ismu_course_codes', get_string('course_codes', 'enrol_ismu'));
        $mform->addHelpButton('enrol_ismu_course_codes', 'course_codes', 'enrol_ismu');
        $mform->setType('enrol_ismu_course_codes', PARAM_RAW);
        $mform->disabledIf('enrol_ismu_course_codes', 'enrol_ismu_period', 'neq', $currentPeriod['full']);

        $statusOpts = [
            enrol_ismu\helper::ISMU_STUDENTS_NO_IMPORT => get_string('enrol_no', 'enrol_ismu'), 
            enrol_ismu\helper::ISMU_STUDENTS_IMPORT_REGISTERED => get_string('enrol_registered', 'enrol_ismu'), 
            enrol_ismu\helper::ISMU_STUDENTS_IMPORT_ENROLLED => get_string('enrol_enrolled', 'enrol_ismu')
        ];
        $mform->addElement('select', 'enrol_ismu_enrol_status', get_string('enrol_status', 'enrol_ismu'), $statusOpts);
        $mform->addHelpButton('enrol_ismu_enrol_status', 'enrol_status', 'enrol_ismu');
        $PAGE->requires->js_init_call('M.enrol_ismu.init');
        $mform->disabledIf('enrol_ismu_enrol_status', 'enrol_ismu_period', 'neq', $currentPeriod['full']);

        $seminarsOpts = [
            enrol_ismu\helper::ISMU_SEMINARS_NO_CREATE => get_string('create_seminars_no', 'enrol_ismu'), 
            enrol_ismu\helper::ISMU_SEMINARS_CREATE => get_string('create_seminars_yes', 'enrol_ismu')
        ];
        $mform->addElement(
            'select', 
            'enrol_ismu_create_seminars', 
            get_string('create_seminars', 'enrol_ismu'), 
            $seminarsOpts
        );
        $mform->addHelpButton('enrol_ismu_create_seminars', 'create_seminars', 'enrol_ismu');
        $mform->disabledIf('enrol_ismu_create_seminars', 'enrol_ismu_enrol_status', 'neq', 2);
        $mform->disabledIf('enrol_ismu_create_seminars', 'enrol_ismu_period', 'neq', $currentPeriod['full']);
        
        $mform->addElement(
            'static', 
            'enrol_ismu_notice', 
            get_string('long_load_notice_label', 'enrol_ismu'), 
            get_string('long_load_notice', 'enrol_ismu')
        );
        
        $mform->setDefaults((array) $this->helper->convert_moodle_to_ismu_settings($instance));
    }
    
    /**
     * Perform custom validation of the data used to edit the instance.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param object $instance The instance loaded from the DB
     * @param context $context The context of the instance we are editing
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     * @return void
     */
    public function edit_instance_validation($data, $files, $instance, $context) {
        return array();
    }
    
    /**
     * Return true if we can add a new instance to this course.
     *
     * @param int $courseid
     * @return boolean
     */
    public function can_add_instance($courseid) 
    {
        global $DB;

        $context = context_course::instance($courseid, MUST_EXIST);
        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/ismu:config', $context)) {
            return false;
        }
        if ($DB->record_exists('enrol', ['courseid' => $courseid, 'enrol' => 'ismu'])) {
            return false;
        }
        return true;
    }
        
    /**
     * Add new instance of enrol plugin with default settings.
     * @param object $course
     * @return int id of new instance, null if can not be created
     */
    public function add_default_instance($course) 
    {
        $currentPeriod = $this->helper->get_current_period();
        $fields = [
            'status' => ENROL_INSTANCE_ENABLED, 
            'enrolperiod' => 0, 
            'roleid' => 0, 
            'customint1' => 0, 
            'customint2' => 0,
            'customchar1' => '',
            'customchar2' => $currentPeriod['full']
        ];
        return $this->add_instance($course, $fields);
    }
    
    /**
     * Add new instance of enrol plugin.
     * @param object $course
     * @param array instance fields
     * @return int id of new instance, null if can not be created
     */
    public function add_instance($course, array $fields = NULL) 
    {
        global $DB;
        if ($DB->record_exists('enrol', ['courseid' => $course->id, 'enrol' => 'ismu'])) {
            return null;
        }
        $data = (array) $this->helper->convert_ismu_to_moodle_settings((object) $fields);
        $enrolId = parent::add_instance($course, $data);
        $this->helper->task_sync_users_from_ismu($course->id, STUDENT_ROLE);
        return $enrolId;
    }
    
    public function update_instance($instance, $data)
    {
        $currentCoursePeriod = isset($instance->customchar2)?trim($instance->customchar2):'';
        $data = (object) $data;
        $update = parent::update_instance(
            $instance,
            (object) $this->helper->convert_ismu_to_moodle_settings($data)
        );
        // perform archiving of user enrolments if neccessary.
        $currentIsmuActivePeriod = $this->helper->get_current_period()['full'];
        if (!empty($currentCoursePeriod)) {
            if ($data->enrol_ismu_period != $currentCoursePeriod) {
                //we have selected different enrol period
                $this->helper->task_archive_users($instance->courseid, $instance->id, $currentCoursePeriod);
                
                if($data->enrol_ismu_period != $currentIsmuActivePeriod) {
                    //restore enrolments from archive
                    $this->helper->task_sync_users_from_archive($instance->courseid, $instance->id, $data->enrol_ismu_period);
                } 
            }
        }
        if($data->enrol_ismu_period == $currentIsmuActivePeriod) {
            $this->helper->task_sync_users_from_ismu($instance->courseid, STUDENT_ROLE);
        }
        return $update;
    }
    
    /**
     * Called after updating/inserting course.
     *
     * @param bool $inserted true if course just inserted
     * @param object $course
     * @param object $data form data
     * @return void
     */
    public function course_updated($inserted, $course, $data) 
    {
        global $DB;
        
        $context = context_course::instance($course->id);
        if (!has_capability('enrol/ismu:config', $context)) {
            return;
        }
        
        if (!isset($data->enrol_ismu_enrol_status)) {
            $data->enrol_ismu_enrol_status = 0;
        }
        if (!isset($data->enrol_ismu_create_seminars)) {
            $data->enrol_ismu_create_seminars = 0;
        }
        if (!isset($data->enrol_ismu_course_codes)) {
            $data->enrol_ismu_course_codes = '';
        }
        if (!isset($data->enrol_ismu_period)) {
            $currentPeriod = $this->helper->get_current_period();
            $data->enrol_ismu_period = $currentPeriod['full'];
        }

        if ($inserted) {
            $this->add_instance($course, $this->helper->convert_ismu_to_moodle_settings($data));
            return;
        } 
        $instance = \enrol_ismu\moodle_enroler::get_instance_by_course_id($course->id);
        if (!$instance) {
            return;
        }
        $instance->status = ENROL_INSTANCE_ENABLED;
        $instance->timemodified = time();

        // perform archiving of user enrolments if neccessary.
        $currentCoursePeriod = isset($instance->customchar2)?trim($instance->customchar2):'';
        $currentIsmuActivePeriod = $this->helper->get_current_period()['full'];
        if (!empty($currentCoursePeriod)) {
            if ($data->enrol_ismu_period != $currentCoursePeriod) {
                //we have selected different enrol period
                $this->helper->task_archive_users($course->id, $instance->id, $currentCoursePeriod);
                
                if($data->enrol_ismu_period != $currentIsmuActivePeriod) {
                    //restore enrolments from archive
                    $this->helper->task_sync_users_from_archive($course->id, $instance->id, $data->enrol_ismu_period);
                } 
            }
        }
        if($data->enrol_ismu_period == $currentIsmuActivePeriod) {
            $this->helper->task_sync_users_from_ismu($course->id, STUDENT_ROLE);
        }

        $DB->update_record('enrol', $this->helper->convert_ismu_to_moodle_settings($data, $instance));
    }
    
    
    public function clean_instance($instance)
    {
        global $CFG;
        require_once("{$CFG->dirroot}/group/lib.php");
        $enroler = new \enrol_ismu\moodle_enroler;
        $enroledUsers = $enroler->get_enroled_students($instance->id);
        if(count($enroledUsers)) {
            foreach($enroledUsers as $user) {
                parent::unenrol_user($instance, $user);
            }
        }
        groups_delete_groups($instance->courseid);
    }
    
    public function get_unenrolself_link($instance) {
        if($this->allow_unenrol($instance))
            return parent::get_unenrolself_link ($instance);
        return null;
    }
	
	/**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param object $instance
     * @return bool
     */
    public function can_delete_instance($instance)
    {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/ismu:config', $context);
    }
	
	/**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance)
    {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/ismu:config', $context);
    }


    public function allow_unenrol(stdClass $instance)
    {
        if($instance->customint1 == \enrol_ismu\helper::ISMU_STUDENTS_NO_IMPORT 
                || $instance->customchar2 != $this->helper->get_current_period()['full']
        ) {
            return true;
        }
        return false;
    }
    
    public function unenrol_user(stdClass $instance, $userid)
    {
        if(!empty($instance->customchar2) && $instance->customchar2 != $this->helper->get_current_period()['full']) {
            $this->helper->task_archive_users($instance->coursei, $instance->id, $instance->customchar2);
		}
        parent::unenrol_user($instance, $userid);
    }

    public function sync_course_users(stdClass $instance, $currentUsers, $wantedUsers, $roleId = null) 
    {
        $usersToUnenrol = array_diff($currentUsers, $wantedUsers);
        $usersToEnrol = array_diff($wantedUsers, $currentUsers);

        foreach ($usersToUnenrol as $user) {
            parent::unenrol_user($instance, $user);
        }
        foreach ($usersToEnrol as $user) {
            $this->enrol_user($instance, $user);
        }
        
        if($roleId != null) {
            $context = context_course::instance($instance->courseid, MUST_EXIST);
            foreach($wantedUsers as $user) {
                role_assign($roleId, $user, $context->id);
            }
        }
    }
    
    public function simple_unenrol_user(stdClass $instance, $userid)
    {
        parent::unenrol_user($instance, $userid);
    }
    
    /**
     * Gets an array of the user enrolment actions
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue)
    {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        if ($this->allow_unenrol_user($instance, $ue) && has_capability("enrol/ismu:unenrol", $context)) {
            $params = $manager->get_moodlepage()->url->params();
            $params['ue'] = $ue->id;
            $actions[] = new user_enrolment_action(
                new pix_icon('t/delete', ''),
                get_string('unenrol', 'enrol'),
                new moodle_url('/enrol/unenroluser.php', $params),
                array('class' => 'unenrollink', 'rel' => $ue->id)
            );
        }
        return $actions;
    }
    
}