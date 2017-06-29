<?php
require_once('../../../config.php');
require_once($CFG->dirroot.'/local/elf/elflib.php');

require_login();
//setting basic options
$currentPeriod = get_current_period();
$period = optional_param('period', $currentPeriod['period'], PARAM_TEXT);
$year = optional_param('year', $currentPeriod['year'], PARAM_TEXT);
$actual = optional_param('actual', false, PARAM_BOOL);
$downloading = optional_param('downloading', 0, PARAM_INT);

if($actual) {
    set_current_period($period, $year);
    $currentPeriod = get_current_period();
}

if($downloading == 1) 
    set_config('is_download', true, 'local_elf');
if($downloading == 2)
    set_config('is_download', false, 'local_elf');


$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/elf/studies/index.php', array('year'=>$year,'period'=>$period));
$PAGE->set_title(get_string('studiessettings','local_elf'));
$PAGE->set_heading(get_string('studiessettingsheader','local_elf'));

has_capability('moodle/site:config', context_system::instance());


require_once($CFG->dirroot.'/local/elf/studies/studiestable.php');

echo $OUTPUT->header();

	echo $OUTPUT->box_start();
	echo $OUTPUT->heading(get_string('studiessettingsheader','local_elf').' '.get_string('period_'.$period,'local_elf').' '.$year,3);

        if(get_config('local_elf','is_download') == true) {
            echo get_string('isdownloading','local_elf');
            echo $OUTPUT->single_button(new moodle_url('/local/elf/studies/index.php',array('downloading'=>2)),get_string('issetdownloadingfalse','local_elf'));
        } else {
            echo get_string('isnotdownloading','local_elf');
            echo $OUTPUT->single_button(new moodle_url('/local/elf/studies/index.php',array('downloading'=>1)),get_string('issetdownloadingtrue','local_elf'));
        }
           
	$otherPeriod = ELF_PERIOD_AUTUMN;
	if($period == $otherPeriod)
		$otherPeriod = ELF_PERIOD_SPRING;
	$nextYear = $previousYear = $year;
	if($period == ELF_PERIOD_AUTUMN)
		$nextYear ++;
	else
		$previousYear --;
	
	echo $OUTPUT->action_link(new moodle_url('/local/elf/studies/index.php',array('year' => $previousYear,'period'=>$otherPeriod)), get_string('previoussemester','local_elf'));
	echo ' | ';
	echo $OUTPUT->action_link(new moodle_url('/local/elf/studies/index.php',array('year' => $nextYear,'period'=>$otherPeriod)), get_string('nextsemester','local_elf'));
	
        if($period == $currentPeriod['period'] && $year == $currentPeriod['year']) {
            echo '<br /><strong>'.get_string('currentactualperiod','local_elf').'</strong>';
        } else {
            echo '<br /><strong>'.get_string('actualperiod','local_elf').': '.get_string('period_'.$currentPeriod['period'],'local_elf').' '.$currentPeriod['year'].'</strong>';
            echo $OUTPUT->single_button(new moodle_url('/local/elf/studies/index.php',array('period' => $period, 'year' => $year, 'actual'=>true)),get_string('changecurrentperiod','local_elf'));

        }
        
	$table = new local_elf_studiestable($period, $year);
	
	ob_start();
	$table->out(10, true);
	$o = ob_get_contents();
	ob_end_clean();
	echo $o;
	echo $OUTPUT->single_button(new moodle_url('/local/elf/studies/updatestudies.php',array('period' => $period, 'year' => $year)), get_string('updatestudies','local_elf'));
	echo $OUTPUT->box_end();

echo $OUTPUT->footer();