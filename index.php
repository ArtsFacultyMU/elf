<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "chat", "view all", "index.php?id=$course->id", "");


/// Get all required strings

    $strchats = get_string("modulenameplural", "chat");
    $strchat  = get_string("modulename", "chat");


/// Print the header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    print_header("$course->shortname: $strchats", "$course->fullname", "$navigation $strchats");

/// Get all the appropriate data

    if (! $chats = get_all_instances_in_course("chat", $course)) {
        notice("There are no chats", "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances (your module will probably extend this)

    $timenow = time();
    $strname  = get_string("name");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname);
        $table->align = array ("CENTER", "LEFT");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ("CENTER", "LEFT", "LEFT", "LEFT");
    } else {
        $table->head  = array ($strname);
        $table->align = array ("LEFT", "LEFT", "LEFT");
    }

    foreach ($chats as $chat) {
        $link = "<A HREF=\"view.php?id=$chat->coursemodule\">$chat->name</A>";

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($chat->section, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo "<br />";

    print_table($table);

/// Finish the page

    print_footer($course);

?>
