<?php

include('../../../config.php');
include('../lib.php');

$chat_sid     = required_param('chat_sid', PARAM_ALPHANUM);
$chat_message = required_param('chat_message', PARAM_RAW);

$PAGE->set_url(new moodle_url($CFG->wwwroot.'/mod/chat/gui_header_js/insert.php', array('chat_sid'=>$chat_sid,'chat_message'=>$chat_message)));

if (!$chatuser = $DB->get_record('chat_users', array('sid'=>$chat_sid))) {
    print_error('notlogged', 'chat');
}

if (!$chat = $DB->get_record('chat', array('id'=>$chatuser->chatid))) {
    print_error('nochat', 'chat');
}

if (!$course = $DB->get_record('course', array('id'=>$chat->course))) {
    print_error('invalidcourseid');
}

if (!$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
    print_error('invalidcoursemodule');
}

require_login($course->id, false, $cm);

if (has_capability('moodle/legacy:guest', get_context_instance(CONTEXT_SYSTEM), 0, false)) {
    print_error('noguests');
}

session_get_instance()->write_close();

/// Delete old users now

chat_delete_old_users();

/// Clean up the message

$chat_message = clean_text($chat_message, FORMAT_MOODLE);  // Strip bad tags

/// Add the message to the database

if (!empty($chat_message)) {

    $message = new object();
    $message->chatid = $chatuser->chatid;
    $message->userid = $chatuser->userid;
    $message->groupid = $chatuser->groupid;
    $message->message = $chat_message;
    $message->timestamp = time();

    $DB->insert_record('chat_messages', $message);
    $DB->insert_record('chat_messages_current', $message);

    $chatuser->lastmessageping = time() - 2;
    $DB->update_record('chat_users', $chatuser);

    if ($cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
        add_to_log($course->id, 'chat', 'talk', "view.php?id=$cm->id", $chat->id, $cm->id);
    }
}

if ($chatuser->version == 'header_js') {
    /// force msg referesh ASAP
    echo $PAGE->requires->js('mod/chat/gui_header_js/chat_gui_header.js')->asap();
    if ($CFG->chat_normal_updatemode != 'jsupdated') {  // See bug MDL-6791
        echo $PAGE->requires->js_function_call('insert_redirect')->asap();;
    }
    echo $PAGE->requires->js_function_call('parent.input.enableForm')->asap();
}

redirect('../empty.php');