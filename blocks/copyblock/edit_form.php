<?php
 
class block_copyblok_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
 		global $COURSE;
 		//author and title settings
        $mform->addElement('header', 'configheader', get_string('settings_header','block_copyblok'));
 
        $mform->addElement('text', 'config_coursename', get_string('title','block_copyblok'));
        $mform->setDefault('config_coursename', $COURSE->fullname);
        $mform->setType('config_coursename', PARAM_MULTILANG);  

        $mform->addElement('text', 'config_authors', get_string('authors','block_copyblok'));
        $mform->setDefault('config_authors', get_string('default_authors','block_copyblok'));
        //$mform->setType('config_authors', PARAM_MULTILANG);
        $mform->setType('config_authors', PARAM_TEXT);
        $mform->addHelpButton('config_authors', 'authors', 'block_copyblok');

        $mform->addElement('date_selector', 'config_published', get_string('published','block_copyblok'),
              array( 'startyear' => 2010, 'stopyear'  => 2050, 'timezone'  => 99, 'optional'  => false ));
        $mform->setDefault('config_published', time());
        
        //$days = array_combine(range(1,31),range(1,31));
        //$month = array( 1 => 'leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec');
        //$years = array_combine(range(2010,2050),range(2010,2050));

        //$calendarygroup=array();
        //$calendarygroup[] =& $mform->createElement('select', 'config_day', get_string('colors'), $days);
        //$calendarygroup[] =& $mform->createElement('select', 'config_month', get_string('colors'), $month);
        //$calendarygroup[] =& $mform->createElement('select', 'config_year', get_string('colors'), $years);
        //$mform->addGroup($calendarygroup, 'config_published2', get_string('published','block_copyblok'), ' ', false);
        
        $mform->addElement('text', 'config_issn', get_string('issn','block_copyblok'));
        $mform->setDefault('config_issn', get_string('default_issn','block_copyblok'));
        $mform->setType('config_issn', PARAM_MULTILANG);

        //license settings
        $mform->addElement('header', 'configheader', get_string('license_citate_header','block_copyblok'));

        $radio = array();
        $radio[] =& $mform->createElement('radio', 'config_lcc', '', get_string('yes'), 1);
        $radio[] =& $mform->createElement('radio', 'config_lcc', '', get_string('no'), 0);
        $mform->addGroup($radio, 'config_lcc_action', get_string('lcc','block_copyblok'), ' ', false);
        $mform->setDefault('config_lcc', 1);

        $radio = array();
        $radio[] =& $mform->createElement('radio', 'config_ladaptations', '', get_string('yes'), 1);
        $radio[] =& $mform->createElement('radio', 'config_ladaptations', '', get_string('no'), 0);
        $radio[] =& $mform->createElement('radio', 'config_ladaptations', '', get_string('lyeswithshare','block_copyblok'), 2);
        $mform->addGroup($radio, 'config_ladaptations_action', get_string('license_adaptations','block_copyblok'), ' ', false);
        $mform->setDefault('config_ladaptations', 0);

        $radio = array();
        $radio[] =& $mform->createElement('radio', 'config_lcommercial', '', get_string('yes'), 1);
        $radio[] =& $mform->createElement('radio', 'config_lcommercial', '', get_string('no'), 0);
        $mform->addGroup($radio, 'config_lcommercial_action', get_string('license_commercial','block_copyblok'), ' ', false);
        $mform->setDefault('config_lcommercial', 1);

        //insertion settings
        $mform->addElement('header', 'configheader', get_string('insert_citate_header','block_copyblok'));
        
        $mform->addElement('checkbox', 'config_insert_page', get_string('pluginname','mod_page'));
        $mform->setDefault('config_insert_page',1);
        
        $mform->addElement('checkbox', 'config_insert_book', get_string('pluginname','mod_book'));
        $mform->setDefault('config_insert_book',1);
        
        $mform->addElement('checkbox', 'config_insert_lesson', get_string('pluginname','mod_lesson'));
        $mform->setDefault('config_insert_lesson',1);
        
    }
}                                                 