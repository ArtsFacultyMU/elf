<?php  // $Id$

    require_once('../../../config.php');
    require_once('../lib.php');

    $id      = required_param('id', PARAM_INT);
    $groupid = optional_param('groupid', 0, PARAM_INT); //only for teachers

    if (!$chat = get_record('chat', 'id', $id)) {
        error('Could not find that chat room!');
    }

    if (!$course = get_record('course', 'id', $chat->course)) {
        error('Could not find the course this belongs to!');
    }

    if (!$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
        error('Course Module ID was incorrect');
    }

    require_login($course->id, false, $cm);

    if (isguest()) {
        error('Guest does not have access to chat rooms');
    }

    if (!$cm->visible and !isteacher($course->id)) {
        print_header();
        notice(get_string("activityiscurrentlyhidden"));
    }

/// Check to see if groups are being used here
     if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        if ($groupid = get_and_set_current_group($course, $groupmode, $groupid)) {
            if (!$group = get_record('groups', 'id', $groupid)) {
                error("That group (id $groupid) doesn't exist!");
            }
            $groupname = ': '.$group->name;
        } else {
            $groupname = ': '.get_string('allparticipants');
        }
    } else {
        $groupid = 0;
        $groupname = '';
    }

    $strchat = get_string('modulename', 'chat'); // must be before current_language() in chat_login_user() to force course language!!!

    if (!$chat_sid = chat_login_user($chat->id, 'sockets', $groupid, $course)) {
        error('Could not log in to chat room!!');
    }

    $params = "chat_sid=$chat_sid";

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=<?php echo get_string('thischarset'); ?>" />
  <title>
   <?php echo "$strchat: $course->shortname: ".format_string($chat->name,true)."$groupname" ?>
  </title>
 </head>
 <frameset cols="*,200" border="5" framespacing="no" frameborder="yes" marginwidth="2" marginheight="1">
  <frameset rows="0,*,50" border="0" framespacing="no" frameborder="no" marginwidth="2" marginheight="1">
   <frame src="empty.php" name="empty" scrolling="auto" noresize marginwidth="2" marginheight="0">
   <frame src="<?php echo "http://$CFG->chat_serverhost:$CFG->chat_serverport?win=chat&amp;$params"; ?>" name="msg" scrolling="auto" noresize marginwidth="2" marginheight="0">
   <frame src="chatinput.php?<?php echo $params ?>" name="input" scrolling="no" marginwidth="2" marginheight="1">
  </frameset>
  <frame src="<?php echo "http://$CFG->chat_serverhost:$CFG->chat_serverport?win=users&amp;$params"; ?>" name="users" scrolling="auto" marginwidth="5" marginheight="5">
 </frameset>
 <noframes>
  Sorry, this version of Moodle Chat needs a browser that handles frames.
 </noframes>
</html>
