<?php
require_once('../../../config.php');
require_once('../lib.php');

$id      = required_param('id', PARAM_INT);
$groupid = optional_param('groupid', 0, PARAM_INT); //only for teachers
$theme   = optional_param('theme', 'compact', PARAM_SAFEDIR);

$url = new moodle_url('/mod/chat/gui_ajax_elf/index.php', array('id'=>$id));
if ($groupid !== 0) {
    $url->param('groupid', $groupid);
}
$PAGE->set_url($url);
$PAGE->set_popup_notification_allowed(false); // No popup notifications in the chat window

$chat = $DB->get_record('chat', array('id'=>$id), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$chat->course), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id, false, MUST_EXIST);

$context = context_module::instance($cm->id);
require_login($course, false, $cm);
require_capability('mod/chat:chat', $context);

/// Check to see if groups are being used here
 if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
    if ($groupid = groups_get_activity_group($cm)) {
        if (!$group = groups_get_group($groupid)) {
            print_error('invalidgroupid');
        }
        $groupname = ': '.$group->name;
    } else {
        $groupname = ': '.get_string('allparticipants');
    }
} else {
    $groupid = 0;
    $groupname = '';
}

// if requested theme doesn't exist, use default 'bubble' theme
if (!file_exists(dirname(__FILE__) . '/theme/'.$theme.'/chat.css')) {
    $theme = 'compact';
}

// login chat room
if (!$chat_sid = chat_login_user($chat->id, 'ajax', $groupid, $course)) {
    print_error('cantlogin', 'chat');
}
$courseshortname = format_string($course->shortname, true, array('context' => context_course::instance($course->id)));
$module = array(
    'name'      => 'mod_chat_ajax', // chat gui's are not real plugins, we have to break the naming standards for JS modules here :-(
    'fullpath'  => '/mod/chat/gui_ajax_elf/module.js',
    'requires'  => array('base', 'dom', 'event', 'event-mouseenter', 'event-key', 'json-parse', 'io', 'overlay', 'yui2-resize', 'yui2-layout', 'yui2-menu'),
    'strings'   => array(array('send', 'chat'), array('sending', 'chat'), array('inputarea', 'chat'), array('userlist', 'chat'),
                         array('modulename', 'chat'), array('beep', 'chat'), array('talk', 'chat'))
);
$modulecfg = array(
    'home'=>$CFG->httpswwwroot.'/mod/chat/view.php?id='.$cm->id,
    'chaturl'=>$CFG->httpswwwroot.'/mod/chat/gui_ajax_elf/index.php?id='.$id,
    'theme'=>$theme,
    'userid'=>$USER->id,
    'sid'=>$chat_sid,
    'timer'=>3000,
    'chat_lasttime'=>0,
    'chat_lastrow'=>null,
    'chatroom_name' => $courseshortname . ": " . format_string($chat->name, true) . $groupname
);
$PAGE->requires->js_init_call('M.mod_chat_ajax.init', array($modulecfg), false, $module);

$PAGE->set_title(get_string('modulename', 'chat').": $courseshortname: ".format_string($chat->name,true)."$groupname");
$PAGE->add_body_class('yui-skin-sam');
$PAGE->set_pagelayout('embedded');
$PAGE->requires->css('/mod/chat/gui_ajax_elf/theme/'.$theme.'/chat.css');

echo $OUTPUT->header();
echo $OUTPUT->box(html_writer::tag('h2',  get_string('participants'), array('class' => 'accesshide')) .
        '<ul id="users-list"></ul>', '', 'chat-userlist');
echo $OUTPUT->box('', '', 'chat-options');
echo $OUTPUT->box(html_writer::tag('h2',  get_string('messages', 'chat'), array('class' => 'accesshide')) .
        '<ul id="messages-list"></ul>', '', 'chat-messages');
echo $OUTPUT->box_start('','chat-input-area');
echo $OUTPUT->box('<textarea disabled="true" id="input-message">Loading...</textarea>','','message-area');
echo $OUTPUT->box('<input type="button" id="button-send" value="'.get_string('send', 'chat').'" /> <a id="choosetheme" href="###">'.get_string('themes').' &raquo; </a><br /><br />'.get_string('send','chat').': ALT+ENTER','','send-area');
echo $OUTPUT->box_end();
echo $OUTPUT->box('', '', 'chat-notify');
echo $OUTPUT->footer();
