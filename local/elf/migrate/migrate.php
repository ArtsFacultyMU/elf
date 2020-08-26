<?php
require_once('../../../config.php');

require_login();
//setting basic options
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/elf/migrate/index.php', array());
$PAGE->set_title(get_string('migrateuser','local_elf'));
$PAGE->set_heading(get_string('migrateuserheader','local_elf'));

has_capability('moodle/site:config', context_system::instance());

$userid = required_param('id',PARAM_INT);

echo $OUTPUT->header();

$elf1user = $DB->get_record('user_elf1',array('id'=>$userid),'*',MUST_EXIST);

$canconvert = true;
	switch($elf1user->auth) {
		case 'http':
			if($DB->record_exists('user',array('username'=>$elf1user->username.'@muni.cz','auth'=>'shibboleth')))
				$canconvert = false;
			break;
		case 'manual':
			if($DB->record_exists('user',array('username'=>$elf1user->username,'auth'=>'manual')))
				$canconvert = false;
			break;
	}

if(!$canconvert)
	echo get_string('useralreadymigrated','local_elf');
else {
	$user = new stdClass;
	$user->auth = 'manual';
	$user->password = $elf1user->password;
	$user->confirmed = 1;
	$user->policyagreed = 0;
	$user->deleted = 0;
	$user->suspended = 0;
	$user->mnethostid = 1;
 	$user->username = $elf1user->username;
	$user->firstname = $elf1user->firstname;
	$user->lastname = $elf1user->lastname;
	$user->email = $elf1user->email;
	$user->emailstop = $elf1user->emailstop;
	$user->icq = $elf1user->icq;
	$user->skype = $elf1user->skype;
	$user->yahoo = $elf1user->yahoo;
	$user->aim = $elf1user->aim;
	$user->msn = $elf1user->msn;
	$user->phone1 = $elf1user->phone1;
	$user->phone2 = $elf1user->phone2;
	$user->institution = $elf1user->institution;
	$user->deparment = $elf1user->deparment;
	$user->offhours = $elf1user->offhours;
	$user->address = $elf1user->address;
	$user->city = $elf1user->city;
	$user->country = $elf1user->country;
	$user->lang = $elf1user->lang;
	$user->timezone = $elf1user->timezone;
	$user->firstaccess = $elf1user->firstaccess;
	$user->lastaccess = $elf1user->lastaccess;
	$user->lastlogin = $elf1user->lastlogin;
	$user->currentlogin = $elf1user->currentlogin;
	$user->lastip = $elf1user->lastip;
	$user->secret = $elf1user->secret;
	$user->picture = $elf1user->picture;
	$user->url = $elf1user->url;
	$user->description = $elf1user->description;
	$user->mailformat = $elf1user->mailformat;
	$user->maildigest = $elf1user->maildigest;
	$user->maildisplay = $elf1user->maildisplay;
	$user->htmleditor = $elf1user->htmleditor;
	$user->autosubscribe = $elf1user->autosubscribe;
	$user->trackforums = $elf1user->trackforums;
	$user->timemodified = $elf1user->timemodified;
	$user->imagealt = $elf1user->imagealt;
	$user->screenreader = $elf1user->screenreader;
	
	$DB->insert_record('user',$user);
	
	$elf1db = mysql_connect('navazka.phil.muni.cz', 'elearnusr', 'H3jHu14m02d1e'); 
	mysql_select_db('elf',$elf1db);
	
	mysql_query('UPDATE mdl_user SET mnethostid=5, auth=\'mnet\', password=\'\' WHERE id='.$elf1user->id,$elf1db);
	
	echo get_string('migrationsuccess','local_elf');
	
	$link = new action_link(new moodle_url('/local/elf/migrate/index.php'),get_string('migrateanotheruser','local_elf'));
	echo $OUTPUR->render($link);
}

echo $OUTPUT->footer();