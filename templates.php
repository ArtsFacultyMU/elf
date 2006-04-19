<?php  // $Id$
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2005 Martin Dougiamas  http://dougiamas.com             //
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

    require_once('../../config.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/blocklib.php');
    
    require_login();

    $id    = optional_param('id', 0, PARAM_INT);  // course module id
    $d     = optional_param('d', 0, PARAM_INT);   // database id
    $mode  = optional_param('mode', 'singletemplate', PARAM_ALPHA);

    if ($id) {
        if (! $cm = get_record('course_modules', 'id', $id)) {
            error('Course Module ID was incorrect');
        }
        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }
        if (! $data = get_record('data', 'id', $cm->instance)) {
            error('Course module is incorrect');
        }

    } else {
        if (! $data = get_record('data', 'id', $d)) {
            error('Data ID is incorrect');
        }
        if (! $course = get_record('course', 'id', $data->course)) {
            error('Course is misconfigured');
        }
        if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
            error('Course Module ID was incorrect');
        }
    }

    require_course_login($course, true, $cm);

    if (!isteacheredit($course->id)){
        error(get_string('noaccess','data'));
    }
    
    if (isteacher($course->id)) {
        if (!count_records('data_fields','dataid',$data->id)) {      // Brand new database!
            redirect($CFG->wwwroot.'/mod/data/field.php?d='.$data->id);  // Redirect to field entry
        }
    }

    //add_to_log($course->id, 'data', 'templates view', "templates.php?id=$cm->id&amp;d=$data->id", $data->id, $cm->id);


/// Print the page header

    $strdata = get_string('modulenameplural','data');
    
    // For the javascript for inserting template tags: initialise the default textarea to
    // 'edit_template' - it is always present in all different possible views.
    $bodytag = 'onload="';
    $bodytag .= 'if (typeof(currEditor) != \'undefined\') { currEditor = edit_template; } ';
    $bodytag .= 'currTextarea = document.tempform.template;';
    $bodytag .= '" ';
    
    print_header_simple($data->name, '', "<a href='index.php?id=$course->id'>$strdata</a> -> $data->name",
                        '', '', true, '', navmenu($course, $cm), '', $bodytag);
    
    print_heading(format_string($data->name));

/// Print the tabs.
    $currenttab = 'templates';
    include('tabs.php'); 


/// Processing submitted data, i.e updating form.
    $resettemplate = false;

    if (($mytemplate = data_submitted($CFG->wwwroot.'/mod/data/templates.php')) && confirm_sesskey()) {
        $newtemplate->id = $data->id;
        $newtemplate->{$mode} = $mytemplate->template;
        
        if (!empty($mytemplate->defaultform)) {
            // Reset the template to default, but don't save yet.
            $resettemplate = true;
            $data->{$mode} = data_generate_default_template($data, $mode, 0, false, false);
        } else {
            if (isset($mytemplate->listtemplateheader)){
                $newtemplate->listtemplateheader = $mytemplate->listtemplateheader;
            }
            if (isset($mytemplate->listtemplatefooter)){
                $newtemplate->listtemplatefooter = $mytemplate->listtemplatefooter;
            }
        
            // Check for multiple tags, only need to check for add template.
            if ($mode != 'addtemplate' or data_tags_check($data->id, $newtemplate->{$mode})) {
                if (update_record('data', $newtemplate)) {
                    notify(get_string('templatesaved', 'data'), 'notifysuccess');
                }
            }
            add_to_log($course->id, 'data', 'templates saved', "templates.php?id=$cm->id&amp;d=$data->id", $data->id, $cm->id);
        }
    } else {
        echo '<div class="littleintro" align="center">'.get_string('header'.$mode,'data').'</div>';
    }

/// If everything is empty then generate some defaults
    if (empty($data->addtemplate) and empty($data->singletemplate) and 
        empty($data->listtemplate) and empty($data->rsstemplate)) {
        data_generate_default_template($data, 'singletemplate');
        data_generate_default_template($data, 'listtemplate');
        data_generate_default_template($data, 'addtemplate');
        data_generate_default_template($data, 'rsstemplate');
    }

/// Print the browsing interface.

    echo '<form name="tempform" action="templates.php?d='.$data->id.'&amp;mode='.$mode.'" method="post">';
    echo '<input name="sesskey" value="'.sesskey().'" type="hidden" />';
    // Print button to autogen all forms, if all templates are empty

    if (!$resettemplate) {
        // Only reload if we are not resetting the template to default.
        $data = get_record('data', 'id', $d);
    }
    print_simple_box_start('center','80%');
    echo '<table cellpadding="4" cellspacing="0" border="0">';


/// Add the HTML editor(s).
    $usehtmleditor = can_use_html_editor() && ($mode != 'csstemplate');
    if ($mode == 'listtemplate'){
        // Print the list template header.
        echo '<tr>';
        echo '<td>&nbsp;</td>';
        echo '<td>';
        echo '<div align="center">'.get_string('header','data').'</div>';
        print_textarea($usehtmleditor, 10, 72, 0, 0, 'listtemplateheader', $data->listtemplateheader);
        echo '</td>';
        echo '</tr>';
    }
    
    // Print the main template.
    // Add all the available fields for this data.
    echo '<tr><td valign="top">';
    echo get_string('availabletags','data');
    helpbutton('tags', get_string('tags','data'), 'data');
    echo '<br />';
    
    echo '<select name="fields1[]" size="10" ';
    
    // Javascript to insert the field tags into the textarea.
    echo 'onclick="';
    echo 'if (typeof(currEditor) != \'undefined\' && currEditor._editMode == \'wysiwyg\') {';
    echo '    currEditor.insertHTML(this.options[selectedIndex].value); ';     // HTMLArea-specific.
    echo '} else {';
    echo 'insertAtCursor(currTextarea, this.options[selectedIndex].value);';   // For inserting when in HTMLArea code view or for normal textareas.
    echo '}';
    echo '">';
    
    $fields = get_records('data_fields', 'dataid', $data->id);
    foreach ($fields as $field) {
        echo '<option value="[['.$field->name.']]">'.$field->name.'</option>';
    }
    
    // Print special tags.
    echo '<option value="##edit##">##' .get_string('edit', 'data'). '##</option>';
    echo '<option value="##more##">##' .get_string('more', 'data'). '##</option>';
    echo '<option value="##moreurl##">##' .get_string('moreurl', 'data'). '##</option>';
    echo '<option value="##delete##">##' .get_string('delete', 'data'). '##</option>';
    echo '<option value="##approve##">##' .get_string('approve', 'data'). '##</option>';
    echo '<option value="##comments##">##' .get_string('comments', 'data'). '##</option>';
    echo '<option value="##user##">##' .get_string('user'). '##</option>';
    echo '</select>';
    echo '<br /><br /><br /><br /><input type="submit" name="defaultform" value="'.get_string('resettemplate','data').'" />';
    echo '</td>';
    
    echo '<td>';
    if ($mode == 'listtemplate'){
        echo '<div align="center">'.get_string('multientry','data').'</div>';        
    }
    print_textarea($usehtmleditor, 20, 72, 0, 0, 'template', $data->{$mode});
    echo '</td>';
    echo '</tr>';
    
    if ($mode == 'listtemplate'){
        echo '<tr>';
        echo '<td>&nbsp;</td>';
        echo '<td>';
        echo '<div align="center">'.get_string('footer','data').'</div>';
        print_textarea($usehtmleditor, 10, 72, 0, 0, 'listtemplatefooter', $data->listtemplatefooter);
        echo '</td>';
        echo '</tr>';
    }

    echo '<tr><td align="center" colspan="2">';
    echo '<input type="submit" value="'.get_string('savetemplate','data').'" />&nbsp;';
    
    echo '</td></tr></table>';
    
    
    print_simple_box_end();
    echo '</form>';
    if ($usehtmleditor) {
        use_html_editor('template');        
        if ($mode == 'listtemplate'){
            use_html_editor('listtemplateheader');
            use_html_editor('listtemplatefooter');
        }
    }

/// Finish the page
    print_footer($course);
?>
