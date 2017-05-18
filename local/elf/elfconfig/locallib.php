<?php
function elf_get_experimental_modules() {
	global $DB;
	
	if($modules = $DB->get_record('config', array('name' => 'elfconfig_modules'))) {
		return explode(',',$modules->value);
	}
	return array();
	
}