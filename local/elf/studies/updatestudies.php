<?php
require_once('../../../config.php');
require_once($CFG->dirroot.'/local/elf/elflib.php');

require_login();
//setting basic options
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/elf/studies/updatestudies.php', array());
$PAGE->set_title(get_string('studiessettings','local_elf'));
$PAGE->set_heading(get_string('studiessettingsheader','local_elf'));

has_capability('moodle/site:config', context_system::instance());

$defaultyear = date('o');
$defaultperiod = get_current_period();

$period = optional_param('period', $defaultperiod, PARAM_TEXT);
$year = optional_param('year', $defaultyear, PARAM_TEXT);

require_once($CFG->dirroot.'/local/elf/studies/studiesform.php');

$form = new local_elf_studies_form($period, $year);

if ($data = $form->get_data()) {
	save_faculty_period($data->faculty_phil, $period, $year, ELF_FACULTY_PHIL);
	save_faculty_period($data->faculty_med, $period, $year, ELF_FACULTY_MED);
	save_faculty_period($data->faculty_law, $period, $year, ELF_FACULTY_LAW);
	save_faculty_period($data->faculty_fss, $period, $year, ELF_FACULTY_FSS);
	save_faculty_period($data->faculty_sci, $period, $year, ELF_FACULTY_SCI);
	save_faculty_period($data->faculty_fi, $period, $year, ELF_FACULTY_FI);
	save_faculty_period($data->faculty_ped, $period, $year, ELF_FACULTY_PED);
	save_faculty_period($data->faculty_econ, $period, $year, ELF_FACULTY_ECON);
	save_faculty_period($data->faculty_fsps, $period, $year, ELF_FACULTY_FSPS);
	save_faculty_period($data->cus, $period, $year, ELF_CUS);
	redirect(new moodle_url('/local/elf/studies/index.php',array('period'=>$period,'year'=>$year)));
}



echo $OUTPUT->header();

	echo $OUTPUT->box_start();
	echo $OUTPUT->heading(get_string('studiessettingsheader','local_elf'),3);

	$form->display();
	echo $OUTPUT->box_end();

echo $OUTPUT->footer();

$string['cus'] = 'Pan-university studies';
$string['faculty'] = 'Faculty';
$string['faculty_phil'] = 'Faculty of Arts';
$string['faculty_med'] = 'Faculty of Medicine';
$string['faculty_law'] = 'Faculty of Law';
$string['faculty_fss'] = 'Faculty of Social Studies';
$string['faculty_sci'] = 'Faculty of Science';
$string['faculty_fi'] = 'Faculty of Informatics';
$string['faculty_ped'] = 'Faculty of Education';
$string['faculty_econ'] = 'Faculty of Economics and Administration';
$string['faculty_fsps'] = 'Faculty of Sports Studies';