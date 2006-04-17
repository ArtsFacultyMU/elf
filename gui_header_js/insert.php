<?php  // $Id$

    include('../../../config.php');
    include('../lib.php');

    $chat_sid     = required_param('chat_sid', PARAM_ALPHANUM);
    $chat_message = required_param('chat_message', PARAM_RAW);

    if (!$chatuser = get_record('chat_users', 'sid', $chat_sid)) {
        error('Not logged in!');
    }

    if (!$chat = get_record('chat', 'id', $chatuser->chatid)) {
        error('No chat found');
    }

    if (!$course = get_record('course', 'id', $chat->course, '', '', '','', 'id, shortname')) {
        error('Could not find the course this belongs to!');
    }

    require_login($course->id);

    if (isguest()) {
        error('Guest does not have access to chat rooms');
    }

    session_write_close();

/// Delete old users now

    chat_delete_old_users();

/// Clean up the message

    $chat_message = addslashes(clean_text(stripslashes($chat_message), FORMAT_MOODLE));  // Strip bad tags

/// Add the message to the database

    if (!empty($chat_message)) {

        $message->chatid = $chatuser->chatid;
        $message->userid = $chatuser->userid;
        $message->groupid = $chatuser->groupid;
        $message->message = $chat_message;
        $message->timestamp = time();

        if (!insert_record('chat_messages', $message)) {
            error('Could not insert a chat message!');
        }

        $chatuser->lastmessageping = time() - 2;
        update_record('chat_users', $chatuser);

        if ($cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
            add_to_log($course->id, 'chat', 'talk', "view.php?id=$cm->id", $chat->id, $cm->id);
        }
    }

    if ($chatuser->version == 'header_js') {
        /// force msg referesh ASAP
        echo '<script type="text/javascript">parent.jsupdate.location.href = parent.jsupdate.document.anchors[0].href;</script>';
    }

    redirect('../empty.php');
?>
