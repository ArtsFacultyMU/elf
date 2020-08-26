<?php
require_once("$CFG->libdir/formslib.php");

class repair_links_form extends moodleform {
	//Add elements to form
	
	public $_course = NULL;
	
	function definition() {
		global $CFG;

		$mform =& $this->_form; // Don't forget the underscore!
		
		$mform->addElement('header', 'repairrestoreheader', get_string('repairresourcesheader','local_elf'));
		
		$mform->addElement('text', 'course', get_string('courseid','local_elf')); // Add elements to your form
		$mform->setType('course', PARAM_INT);                   //Set type of element
		$mform->addRule('course', get_string('courseidmissing','local_elf'), 'required');
		$mform->addRule('course', get_string('courseidmissing','local_elf'), 'nonzero');
		
		$this->add_action_buttons(false,get_string('repaircourselinks','local_elf'));
		
	}
	
	function validation($data, $files) {
		global $DB;
		
		if(!$this->_course = $DB->get_record('course', array('id'=>$data['course'])))
			return array('course' => get_string('coursenotexist','local_elf'));
		
		return true;
	}
}