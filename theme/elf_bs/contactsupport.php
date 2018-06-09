<?php
require_once('../../config.php');

$url = new moodle_url('/theme/elf_bs/contactsupport.php');
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$user = new stdClass;
$user->email = $PAGE->theme->settings->contactemail;

$subject = required_param('contact-subject', PARAM_TEXT);
$body = required_param('contact-message', PARAM_TEXT);

if (!validate_email($user->email)) {
	die();
}

$user->id = -99; // to prevent anything annoying happening

$from = new stdClass;
$from->firstname = null;
$from->lastname = null;
$from->email = required_param('contact-email', PARAM_TEXT);
$from->maildisplay = true;

email_to_user($user,$from,$subject,$body);


$PAGE->set_title($SITE->fullname);
$PAGE->set_heading(get_string("contactingsupport",'theme_elf_bs'));

echo $OUTPUT->header();

echo $OUTPUT->box_start("generalbox center clearfix");
echo '<div style="height: 15px;"></div>';
echo format_text($PAGE->theme->settings->contactsuccesfullmessage);

echo '<h3>'. get_string("contactmessagedetails",'theme_elf_bs') . '</h3>';

echo $OUTPUT->box_start();
echo '<strong>'.get_string("email",'theme_elf_bs') . ': </strong>' . $from->email;
echo $OUTPUT->box_end();

echo $OUTPUT->box_start();
echo '<strong>'.get_string("subject",'theme_elf_bs') . ': </strong>' . $subject;
echo $OUTPUT->box_end();

echo $OUTPUT->box_start();
echo '<strong>'.get_string("message",'theme_elf_bs') . ': </strong>' . $body;
echo $OUTPUT->box_end();
echo '<div style="height: 15px;"></div>';

$url = new moodle_url('/');
echo $OUTPUT->box_start();
echo '<a href="'.$url->out().'" class="btn btn-primary">'.get_string('returntomainpage','theme_elf_bs').'</a>';
echo $OUTPUT->box_end();
echo '<div style="height: 15px;"></div>';
echo $OUTPUT->box_end();

echo $OUTPUT->footer();