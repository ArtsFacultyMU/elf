<?php
require_once('../../../config.php');
require_once('migrate_user_form.php');

require_login();
//setting basic options
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/elf/migrate/index.php', array());
$PAGE->set_title(get_string('migrateuser','local_elf'));
$PAGE->set_heading(get_string('migrateuserheader','local_elf'));

has_capability('moodle/site:config', context_system::instance());

echo $OUTPUT->header();


$mform = new migrate_user_form();

//Form processing and displaying is done here
if ($data = $mform->get_data()) {
//Form processing and displaying is done here
	
	echo $OUTPUT->box_start();
	
	echo get_string('selecteduser','local_elf');
	
	echo '<strong>'.$mform->_user->firstname.' '.$mform->_user->lastname.'</strong> ('.$mform->_user->username.') '.$mform->_user->email.'<br/>';
	
	$canconvert = true;
	switch($mform->_user->auth) {
		case 'http':
			if($DB->record_exists('user',array('username'=>$mform->_user->username.'@muni.cz','auth'=>'shibboleth')))
				$canconvert = false;
			break;
		case 'manual':
			if($DB->record_exists('user',array('username'=>$mform->_user->username,'auth'=>'manual')))
				$canconvert = false;
			break;
	}
	
	if($canconvert) {
		$link = new action_link(new moodle_url('/local/elf/migrate/migrate.php',array('id'=>$mform->_user->id)),get_string('migrate','local_elf'));
		echo $OUTPUT->render($link);
	} else {
		echo get_string('useralreadymigrated','local_elf');
	}
	
	echo $OUTPUT->box_end();

} 

$mform->display();

echo $OUTPUT->footer();