<?php
 
require_once($CFG->dirroot.'/local/elf/elflib.php');

class block_is_links_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
 		global $DB;
 		//author and title settings
        $mform->addElement('header', 'configheader', get_string('settings_header','block_is_links'));
 
        $mform->addElement('text','config_iscodes',get_string('iscodes','block_is_links'));
        
        $mform->addElement('select', 'config_faculty', get_string('faculty','block_is_links'), get_faculties());
        $mform->setDefault('config_faculty', ELF_FACULTY_PHIL); 
        
        $periods = array();
        $periodsDB = $DB->get_records_sql('SELECT year, period FROM {is_semesters} GROUP BY year, period ORDER BY year DESC, period ASC LIMIT 10');
        foreach ($periodsDB as $periodDB) {
        	$periods[$periodDB->year.'_'.$periodDB->period] = get_string('period_'.$periodDB->period,'local_elf').' '.$periodDB->year;
        }
        
        $mform->addElement('select','config_semester', get_string('semester','block_is_links'), $periods);
        
    }
}