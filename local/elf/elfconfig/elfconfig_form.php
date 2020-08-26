<?php
require_once("$CFG->libdir/formslib.php");

class elfconfig_form extends moodleform {
	//Add elements to form
	
	public $_course = NULL;
	
	function definition() {
		global $CFG, $DB;

		$mform =& $this->_form; // Don't forget the underscore!
	
		$mform->addElement('header', 'experimentalmodulesheader', get_string('experimentalmodules','local_elf'));
		
		$mods = array();
		
		if ($allmods = $DB->get_records("modules")) {
			foreach ($allmods as $mod) {
				if (!file_exists("$CFG->dirroot/mod/$mod->name/lib.php")) 
					continue;
				
				if ($mod->visible) 
					$mods[$mod->name] = get_string("modulename", "$mod->name");
				
			}
			core_collator::asort($mods);
		}
		
		$select = &$mform->addElement('select', 'modules', get_string('modulenames','local_elf'), $mods,array('size' => 8));
		$select->setMultiple(true);
		
		$mform->addHelpButton('modules', 'modulenames','local_elf');
		
		$this->add_action_buttons(false);
		
	}
	
	function validation($data, $files) {
		global $DB;
		/*
		if(!$this->_course = $DB->get_record('course', array('id'=>$data['course'])))
			return array('course' => get_string('coursenotexist','local_elf'));
		*/
		return true;
	}
}