<?php  // $Id$

/// This page prints a particular instance of chat

    require_once('../../config.php');
    require_once('lib.php');

    $id = optional_param('id', 0, PARAM_INT);
    $c  = optional_param('c', 0, PARAM_INT);

    if ($id) {
        if (! $cm = get_record('course_modules', 'id', $id)) {
            error('Course Module ID was incorrect');
        }

        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }

        chat_update_chat_times($cm->instance);

        if (! $chat = get_record('chat', 'id', $cm->instance)) {
            error('Course module is incorrect');
        }

    } else {
        chat_update_chat_times($c);

        if (! $chat = get_record('chat', 'id', $c)) {
            error('Course module is incorrect');
        }
        if (! $course = get_record('course', 'id', $chat->course)) {
            error('Course is misconfigured');
        }
        if (! $cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
            error('Course Module ID was incorrect');
        }
    }

    require_course_login($course);

    if (!$cm->visible and !isteacher($course->id)) {
        print_header();
        notice(get_string("activityiscurrentlyhidden"));
    }

    add_to_log($course->id, 'chat', 'view', "view.php?id=$cm->id", $chat->id, $cm->id);

/// Print the page header

    $strchats        = get_string('modulenameplural', 'chat');
    $strchat         = get_string('modulename', 'chat');
    $strenterchat    = get_string('enterchat', 'chat');
    $stridle         = get_string('idle', 'chat');
    $strcurrentusers = get_string('currentusers', 'chat');
    $strnextsession  = get_string('nextsession', 'chat');

    print_header_simple($chat->name, '',
                 "<a href=\"index.php?id=$course->id\">$strchats</a> -> $chat->name",
                  '', '', true, update_module_button($cm->id, $course->id, $strchat),
                  navmenu($course, $cm));

    if (($chat->studentlogs or isteacher($course->id)) and !isguest()) {
        echo "<p align=\"right\"><a href=\"report.php?id=$cm->id\">".
              get_string('viewreport', 'chat').'</a></p>';
    }

    print_heading($chat->name);

/// Check to see if groups are being used here
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "view.php?id=$cm->id");
    } else {
        $currentgroup = 0;
    }

    if ($currentgroup) {
        $groupselect = " AND groupid = '$currentgroup'";
        $groupparam = "&amp;groupid=$currentgroup";
    } else {
        $groupselect = "";
        $groupparam = "";
    }

/// Print the main part of the page

    if (!isguest()) {
        print_simple_box_start('center');
        link_to_popup_window ("/mod/chat/gui_$CFG->chat_method/index.php?id=$chat->id$groupparam",
                              "chat$course->id$chat->id$groupparam", "$strenterchat", 500, 700, $strchat);
        print_simple_box_end();
    } else {
        notice(get_string('noguests', 'chat'));
    }


    if ($chat->chattime and $chat->schedule) {  // A chat is scheduled
        if (abs($USER->timezone) > 13) {
            $timezone = get_string('serverlocaltime');
        } else if ($USER->timezone < 0) {
            $timezone = 'GMT'.$USER->timezone;
        } else {
            $timezone = 'GMT+'.$USER->timezone;
        }
        echo "<p align=\"center\">$strnextsession: ".userdate($chat->chattime)." ($timezone)</p>";
    } else {
        echo '<br />';
    }

    if ($chat->intro) {
        print_simple_box( format_text($chat->intro) , 'center');
        echo '<br />';
    }

    chat_delete_old_users();

    if ($chatusers = chat_get_users($chat->id, $currentgroup)) {
        $timenow = time();
        print_simple_box_start('center');
        print_heading($strcurrentusers);
        echo '<table width="100%">';
        foreach ($chatusers as $chatuser) {
            $lastping = $timenow - $chatuser->lastmessageping;
            echo '<tr><td width="35">';
            echo "<a href=\"$CFG->wwwroot/user/view.php?id=$chatuser->id&amp;course=$chat->course\">";
            print_user_picture($chatuser->id, 0, $chatuser->picture, false, false, false);
            echo '</a></td><td valign="center">';
            echo '<p><font size="1">';
            echo fullname($chatuser).'<br />';
            echo "<font color=\"#888888\">$stridle: ".format_time($lastping)."</font>";
            echo '</font></p>';
            echo '<td></tr>';
        }
        echo '</table>';
        print_simple_box_end();
    }


/// Finish the page
    print_footer($course);

?>
