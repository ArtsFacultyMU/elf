<?php
require_once("$CFG->libdir/formslib.php");

class migrate_user_form extends moodleform {
	//Add elements to form
	
	public $_user = NULL;
	
	function definition() {
		global $CFG;

		$mform =& $this->_form; // Don't forget the underscore!
		
		$mform->addElement('header', 'migrateuserheader', get_string('migrateuserheader','local_elf'));
		
		$mform->addElement('text', 'username', get_string('username','local_elf')); // Add elements to your form
		$mform->setType('username', PARAM_TEXT);                   //Set type of element
		$mform->addRule('username', get_string('usermissing','local_elf'), 'required');
		
		$this->add_action_buttons(false,get_string('search'));
		
	}
	
	function validation($data, $files) {
		global $DB;
		
		if(!$this->_user = $DB->get_record('user_elf1', array('username'=>$data['username'])))
			return array('username' => get_string('usernotexist','local_elf'));
		
		return true;
	}
}