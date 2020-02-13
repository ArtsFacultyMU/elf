<?php
require_once('../../../config.php');
require_once('elfconfig_form.php');

require_login();
//setting basic options
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/elf/elfconfig/index.php', array());
$PAGE->set_title(get_string('elfconfig','local_elf'));
$PAGE->set_heading(get_string('elfconfigheader','local_elf'));

has_capability('moodle/site:config', context_system::instance());

$mform = new elfconfig_form();

echo $OUTPUT->header();

$data = new stdClass();

$elfconfig_modules = $DB->get_record('config',array('name'=>'elfconfig_modules'));


//Form processing and displaying is done here
if ($data = $mform->get_data()) {
//Form processing and displaying is done here
	
	$modules = '';
	foreach($data->modules as $module) {
		if(strlen($modules) != 0)
			$modules .= ',';
		$modules .= $module;
	}
	

	if($DB->record_exists('config', array('name'=>'elfconfig_modules'))) {
		$elfconfig_modules->value = $modules;
		$DB->update_record('config', $elfconfig_modules);
	} else {
		$elfconfig_modules = new stdClass();
		$elfconfig_modules->name = 'elfconfig_modules';
		$elfconfig_modules->value = $modules;
		$DB->insert_record('config', $elfconfig_modules);
	}
	echo $OUTPUT->box_start();
	echo get_string('changessaved');
	echo $OUTPUT->box_end();


} else {
	$data = new stdClass();
	if($elfconfig_modules !== false)
		$data->modules = explode(',',$elfconfig_modules->value);
}

$mform->set_data($data);

$mform->display();

echo $OUTPUT->footer();