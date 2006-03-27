<?php // $Id$
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1990-onwards Moodle Pty Ltd   http://moodle.com         //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_course_login($course);

    add_to_log($course->id, "data", "view all", "index.php?id=$course->id", "");

    $strweek = get_string('week');
    $strtopic = get_string('topic');
    $strname = get_string('name');
    $strdata = get_string('modulename','data');
  
    if (! $datas = get_all_instances_in_course("data", $course)) {
        notice("There are no databases", "$CFG->wwwroot/course/view.php?id=$course->id");
    }

    print_header_simple($strdata, '', $strdata, '', '', true, "", navmenu($course));

    $timenow  = time();
    $strname  = get_string('name');
    $strweek  = get_string('week');
    $strtopic = get_string('topic');
    $strdescription = get_string("description");
    $strnumrecords = get_string('numrecords', 'data');
    $strnumnotapproved = get_string('numnotapproved', 'data');

    if ($course->format == 'weeks') {
        $table->head  = array ($strweek, $strname, $strdescription, $strnumrecords, $strnumnotapproved);
        $table->align = array ('center', 'center', 'center', 'center', 'center');
    } else if ($course->format == 'topics') {
        $table->head  = array ($strtopic, $strname, $strdescription, $strnumrecords, $strnumnotapproved);
        $table->align = array ('center', 'center', 'center', 'center', 'center');
    } else {
        $table->head  = array ($strname);
        $table->align = array ('center', 'center');
    }

    $currentgroup = get_current_group($course->id);
    if ($currentgroup and isteacheredit($course->id)) {
        $group = get_record("groups", "id", $currentgroup);
        $groupname = " ($group->name)";
    } else {
        $groupname = "";
    }

    $currentsection = "";

    foreach ($datas as $data) {

        $printsection = "";

        //Calculate the href
        if (!$data->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id=$data->coursemodule\">".format_string($data->name,true)."</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$data->coursemodule\">".format_string($data->name,true)."</a>";
        }
        if ($course->format == 'weeks' or $course->format == 'topics') {
            
            $numrecords = count_records_sql('SELECT COUNT(r.id) FROM '.$CFG->prefix.
                                                'data_records r WHERE r.dataid ='.$data->id);
            
            if ($data->approval == 1) {
                $numunapprovedrecords = count_records_sql('SELECT COUNT(r.id) FROM '.$CFG->prefix.
                                                'data_records r WHERE r.dataid ='.$data->id.
                                                ' AND r.approved <> 1');
            } else {
                $numunapprovedrecords = get_string('noapprovalrequired', 'data');
            }
            
            $table->data[] = array ($printsection, $link, $data->intro, $numrecords, $numunapprovedrecords);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo "<br />";
    print_table($table);
    print_footer($course);
    
?>
