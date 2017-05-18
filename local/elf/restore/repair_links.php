<?php 
require_once('../../../config.php');
require_once('repair_links_form.php');

require_login();
//setting basic options
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/elf/restore/repair_links.php', array());
$PAGE->set_title(get_string('repairresources','local_elf'));
$PAGE->set_heading(get_string('repairresourcesheader','local_elf'));

has_capability('moodle/site:config', context_system::instance());
$mform = new repair_links_form();

echo $OUTPUT->header();

//Form processing and displaying is done here
if ($data = $mform->get_data()) {
	require_once('locallib.php');
	echo $OUTPUT->box_start();
	echo $OUTPUT->heading(get_string('repairresourcesheader','local_elf'),3);
	
	echo "<strong>".get_string('course')."</strong> ".$mform->_course->fullname."</br></br>";
	
	echo get_string('repairsections','local_elf')."</br>";
	echo "&nbsp;&nbsp;".get_string('repairedlinks','local_elf')." ".repair_sections($mform->_course->id)."</br>";
	echo get_string('repairbooks','local_elf')."</br>";
	echo "&nbsp;&nbsp;".get_string('repairedlinks','local_elf')." ".repair_books($mform->_course->id)."</br>";
	echo get_string('repairassignments','local_elf')."</br>";
	echo "&nbsp;&nbsp;".get_string('repairedlinks','local_elf')." ".repair_assignments($mform->_course->id)."</br>";
	echo get_string('repairlabels','local_elf')."</br>";
	echo "&nbsp;&nbsp;".get_string('repairedlinks','local_elf')." ".repair_labels($mform->_course->id)."</br>";
	echo get_string('repairpages','local_elf')."</br>";
	echo "&nbsp;&nbsp;".get_string('repairedlinks','local_elf')." ".repair_pages($mform->_course->id)."</br></br>";
	
	echo $OUTPUT->action_link('/local/elfrestore/resources/repair_links.php', get_string('repairanother','local_elf'));
	echo " | ";
	echo $OUTPUT->action_link('/course/view.php?id='.$mform->_course->id, get_string('viewcourse','local_elf'));
	echo $OUTPUT->box_end();
	
//In this case you process validated data. $mform->get_data() returns data posted in form.
} else {
	// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
	// or on the first display of the form.

	
	
	$mform->display();
	
	
}
echo $OUTPUT->footer();






