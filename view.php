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
    require_once("$CFG->libdir/rsslib.php");

    require_once('pagelib.php');
    

/// One of these is necessary!
    $id    = optional_param('id', 0, PARAM_INT);  // course module id
    $d     = optional_param('d', 0, PARAM_INT);   // database id
    $rid   = optional_param('rid', 0, PARAM_INT);    //record id

    $mode  = optional_param('mode', '', PARAM_ALPHA);    // Force the browse mode  ('single')

    $edit = optional_param('edit', -1, PARAM_BOOL);

/// These can be added to perform an action on a record
    $approve = optional_param('approve', 0, PARAM_INT);    //approval recordid
    $delete = optional_param('delete', 0, PARAM_INT);    //delete recordid
    
    if ($id) {
        if (! $cm = get_coursemodule_from_id('data', $id)) {
            error('Course Module ID was incorrect');
        }
        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }
        if (! $data = get_record('data', 'id', $cm->instance)) {
            error('Course module is incorrect');
        }
        $record = NULL;

    } else if ($rid) {
        if (! $record = get_record('data_records', 'id', $rid)) {
            error('Record ID is incorrect');
        }
        if (! $data = get_record('data', 'id', $record->dataid)) {
            error('Data ID is incorrect');
        }
        if (! $course = get_record('course', 'id', $data->course)) {
            error('Course is misconfigured');
        }
        if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
            error('Course Module ID was incorrect');
        }
    } else {   // We must have $d
        if (! $data = get_record('data', 'id', $d)) {
            error('Data ID is incorrect');
        }
        if (! $course = get_record('course', 'id', $data->course)) {
            error('Course is misconfigured');
        }
        if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
            error('Course Module ID was incorrect');
        }
        $record = NULL;
    }

    require_course_login($course, true, $cm);
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/data:readentry', $context);

/// If it's hidden then it's don't show anything.  :)
    if (empty($cm->visible) and !has_capability('mod/data:managetemplates', $context)) {
        $strdatabases = get_string("modulenameplural", "data");
        $navigation = "<a href=\"index.php?id=$course->id\">$strdatabases</a> ->";
        print_header_simple(format_string($data->name), "",
                 "$navigation ".format_string($data->name), "", "", true, '', navmenu($course, $cm));
        notice(get_string("activityiscurrentlyhidden"));
    }

/// If we have an empty Database then redirect because this page is useless without data
    if (has_capability('mod/data:managetemplates', $context)) {
        if (!record_exists('data_fields','dataid',$data->id)) {      // Brand new database!
            redirect($CFG->wwwroot.'/mod/data/field.php?d='.$data->id);  // Redirect to field entry
        }
    }


/// Check further parameters that set browsing preferences
    if (!isset($SESSION->dataprefs)) {
        $SESSION->dataprefs = array();
    }
    if (!isset($SESSION->dataprefs[$data->id])) {
        $SESSION->dataprefs[$data->id] = array();
        $SESSION->dataprefs[$data->id]['search'] = '';
        $SESSION->dataprefs[$data->id]['sort'] = $data->defaultsort;
        $SESSION->dataprefs[$data->id]['order'] = ($data->defaultsortdir == 0) ? 'ASC' : 'DESC';
    }
    $search = optional_param('search', $SESSION->dataprefs[$data->id]['search'], PARAM_NOTAGS);
    $SESSION->dataprefs[$data->id]['search'] = $search;   // Make it sticky

    $sort = optional_param('sort', $SESSION->dataprefs[$data->id]['sort'], PARAM_INT);
    $SESSION->dataprefs[$data->id]['sort'] = $sort;       // Make it sticky

    $order = (optional_param('order', $SESSION->dataprefs[$data->id]['order'], PARAM_ALPHA) == 'ASC') ? 'ASC': 'DESC';
    $SESSION->dataprefs[$data->id]['order'] = $order;     // Make it sticky


    $oldperpage = get_user_preferences('data_perpage_'.$data->id, 10);
    $perpage = optional_param('perpage', $oldperpage, PARAM_INT);

    if ($perpage < 2) {
        $perpage = 2;
    }
    if ($perpage != $oldperpage) {
        set_user_preference('data_perpage_'.$data->id, $perpage);
    }

    $page = optional_param('page', 0, PARAM_INT);

    add_to_log($course->id, 'data', 'view', "view.php?id=$cm->id", $data->id, $cm->id);


// Initialize $PAGE, compute blocks
    $PAGE       = page_create_instance($data->id);
    $pageblocks = blocks_setup($PAGE);
    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);

    if (($edit != -1) and $PAGE->user_allowed_editing()) {
        $USER->editing = $edit;
    }

/// RSS and CSS and JS meta
    $meta = '';
    if (!empty($CFG->enablerssfeeds) && !empty($CFG->data_enablerssfeeds) && $data->rssarticles > 0) {
        $rsspath = rss_get_url($course->id, $USER->id, 'data', $data->id);
        $meta .= '<link rel="alternate" type="application/rss+xml" ';
        $meta .= 'title ="'.$course->shortname.': %fullname%" href="'.$rsspath.'" />';
    }
    if ($data->csstemplate) {
        $meta .= '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/mod/data/css.php?d='.$data->id.'" /> ';
    }
    if ($data->jstemplate) {
        $meta .= '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/data/js.php?d='.$data->id.'"></script>';
    }

    
/// Print the page header
    $PAGE->print_header($course->shortname.': %fullname%', '', $meta);
    

/// If we have blocks, then print the left side here
    if (!empty($CFG->showblocksonmodpages)) {
        echo '<table id="layout-table"><tr>';
        if ((blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
            echo '<td style="width: '.$blocks_preferred_width.'px;" id="left-column">';
            blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
            echo '</td>';
        }
        echo '<td id="middle-column">';
    }

    print_heading(format_string($data->name));
    
    // Do we need to show a link to the RSS feed for the records?
    if (!empty($CFG->enablerssfeeds) && !empty($CFG->data_enablerssfeeds) && $data->rssarticles > 0) {
        echo '<div style="float:right;">';
        rss_print_link($course->id, $USER->id, 'data', $data->id, get_string('rsstype'));
        echo '</div>';
        echo '<div style="clear:both;"></div>';
    }
    
    if ($data->intro and empty($page) and empty($record) and $mode != 'single') {
        print_simple_box(format_text($data->intro), 'center', '70%', '', 5, 'generalbox', 'intro');
    }

/// Check to see if groups are being used here
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, 
                                            'view.php?d='.$data->id.'&amp;search='.s($search).'&amp;sort='.s($sort).
                                            '&amp;order='.s($order).'&amp;');
    } else {
        $currentgroup = 0;
    }

/// Delete any requested records

    if ($delete && confirm_sesskey() && (has_capability('mod/data:manageentries', $context) or data_isowner($delete))) {
        if ($confirm = optional_param('confirm',0,PARAM_INT)) {
            if ($deleterecord = get_record('data_records', 'id', $delete)) {   // Need to check this is valid
                if ($deleterecord->dataid == $data->id) {                       // Must be from this database
                    if ($contents = get_records('data_content','recordid', $deleterecord->id)) {
                        foreach ($contents as $content) {  // Delete files or whatever else this field allows
                            if ($field = data_get_field_from_id($content->fieldid, $data)) { // Might not be there
                                $field->delete_content($content->recordid);
                            }
                        }
                    }
                    delete_records('data_content','recordid', $deleterecord->id);
                    delete_records('data_records','id', $deleterecord->id);

                    add_to_log($course->id, 'data', 'record delete', "view.php?id=$cm->id", $data->id, $cm->id);

                    notify(get_string('recorddeleted','data'), 'notifysuccess');
                }
            }

        } else {   // Print a confirmation page
            if ($deleterecord = get_record('data_records', 'id', $delete)) {   // Need to check this is valid
                if ($deleterecord->dataid == $data->id) {                       // Must be from this database
                    notice_yesno(get_string('confirmdeleterecord','data'), 
                            'view.php?d='.$data->id.'&amp;delete='.$delete.'&amp;confirm=1&amp;sesskey='.sesskey(),
                            'view.php?d='.$data->id);

                    $records[] = $deleterecord;
                    echo data_print_template('singletemplate', $records, $data, '', 0, true);

                    print_footer($course);
                    exit;
                }
            }
        }
    }



/// Print the tabs

    if ($record or $mode == 'single') {
        $currenttab = 'single';
    } else {
        $currenttab = 'list';
    }
    include('tabs.php'); 


/// Approve any requested records

    if ($approve && confirm_sesskey() && has_capability('mod/data:approve', $context)) {
        if ($approverecord = get_record('data_records', 'id', $approve)) {   // Need to check this is valid
            if ($approverecord->dataid == $data->id) {                       // Must be from this database
                $newrecord->id = $approverecord->id;
                $newrecord->approved = 1;
                if (update_record('data_records', $newrecord)) {
                    notify(get_string('recordapproved','data'), 'notifysuccess');
                }
            }
        }
    }

// If not teacher, check whether user has sufficient records to view
    if (!has_capability('mod/data:managetemplates', $context) and data_numentries($data) < $data->requiredentriestoview){
        notify (($data->requiredentriestoview - data_numentries($data)).'&nbsp;'.get_string('insufficiententries','data'));
        echo '</td></tr></table>';
        print_footer($course);
        exit;
    }


/// We need to examine the whole dataset to produce the correct paging

    if ((!has_capability('mod/data:managetemplates', $context)) && ($data->approval)) {
        if (isloggedin()) {
            $approveselect = ' AND (r.approved=1 OR r.userid='.$USER->id.') ';
        } else {
            $approveselect = ' AND r.approved=1 ';
        }
    } else {
        $approveselect = ' ';
    }

    if ($currentgroup) {
        $groupselect = " AND (r.groupid = '$currentgroup' OR r.groupid = 0)";
    } else {
        $groupselect = ' ';
    }

/// Find the field we are sorting on
    if ($sort) {
        
        $sortfield = data_get_field_from_id($sort, $data);
        $sortcontent = $sortfield->get_sort_field();
        $sortcontentfull = $sortfield->get_sort_sql('c.'.$sortcontent);

        $what = ' DISTINCT r.id, r.approved, r.userid, u.firstname, u.lastname, c.'.$sortcontent.' ';
        $count = ' COUNT(DISTINCT c.recordid) ';
        $tables = $CFG->prefix.'data_content c,'.$CFG->prefix.'data_records r,'.$CFG->prefix.'data_content c1, '.$CFG->prefix.'user u ';
        $where =  'WHERE c.recordid = r.id 
                     AND c.fieldid = '.$sort.' 
                     AND r.dataid = '.$data->id.' 
                     AND r.userid = u.id 
                     AND c1.recordid = r.id ';
        $sortorder = ' ORDER BY '.$sortcontentfull.' '.$order.' , r.id ASC ';
        if ($search) {
            $searchselect = ' AND (c1.content LIKE "%'.$search.'%") ';
        } else {
            $searchselect = ' ';
        }

    } else if ($search) { 
        $what = ' DISTINCT r.id, r.approved, r.userid, u.firstname, u.lastname ';
        $count = ' COUNT(DISTINCT c.recordid) ';
        $tables = $CFG->prefix.'data_content c,'.$CFG->prefix.'data_records r, '.$CFG->prefix.'user u ';
        $where =  'WHERE c.recordid = r.id 
                     AND r.userid = u.id 
                     AND r.dataid = '.$data->id;
        $sortorder = ' ORDER BY r.id ASC ';
        $searchselect = ' AND (c.content LIKE "%'.$search.'%") ';

    } else {
        $what = ' DISTINCT r.id, r.approved, r.timecreated, r.userid, u.firstname, u.lastname ';
        $count = ' COUNT(r.id) ';
        $tables = $CFG->prefix.'data_records r, '.$CFG->prefix.'user u ';
        $where =  'WHERE r.dataid = '.$data->id. ' AND r.userid = u.id ';
        $sortorder = ' ORDER BY r.timecreated '.$order. ' ';
        $searchselect = ' ';
    }


/// To actually fetch the records

    $fromsql = ' FROM '.$tables.$where.$groupselect.$approveselect.$searchselect;
     
    $sqlselect = 'SELECT '.$what.$fromsql.$sortorder;

    $sqlcount  = 'SELECT '.$count.$fromsql;   // Total number of records

/// Work out the paging numbers

    $totalcount = count_records_sql($sqlcount);

    if ($record) {     // We need to just show one, so where is it in context?
        $nowperpage = 1;
        $mode = 'single';

#  Following code needs testing to make it work
#        if ($sort) {   // We need to search by that field
#            if ($content = get_field('data_content', 'content', 'recordid', $record->id, 'fieldid', $sort)) {
#                $content = addslashes($content);
#                if ($order == 'ASC') {
#                    $lessthan = " AND $sortcontentfull < '$content' 
#                                   OR ($sortcontentfull = '$content' AND r.id < '$record->id') ";
#                } else {
#                    $lessthan = " AND $sortcontentfull > '$content' 
#                                   OR ($sortcontentfull = '$content' AND r.id < '$record->id') ";
#                }
#            } else {   // Failed to find data (shouldn't happen), so fall back to something easy
#                $lessthan = " r.id < '$record->id' ";
#            }
#        } else {
#            $lessthan = " r.id < '$record->id' ";
#        }
#        $sqlindex = 'SELECT COUNT(DISTINCT c.recordid) '.$fromsql.$lessthan.$sortorder;
#        $page = count_records_sql($sqlindex);


        $allrecords = get_records_sql($sqlselect);      // Kludgey but accurate at least!
        $page = 0;
        foreach ($allrecords as $key => $allrecord) {
            if ($key == $record->id) {
                break;
            }
            $page++;
        }

    } else if ($mode == 'single') {  // We rely on ambient $page settings
        $nowperpage = 1;

    } else {
        $nowperpage = $perpage;
    }

/// Get the actual records

    $limit = sql_paging_limit($page * $nowperpage, $nowperpage);
    $records = get_records_sql($sqlselect.$limit);

    if (empty($records)) {     // Nothing to show!
        if ($record) {         // Something was requested so try to show that at least (bug 5132)
            if (has_capability('mod/data:manageentries', $context) || empty($data->approval) || 
                     $record->approved || (isloggedin() && $record->userid == $USER->id)) {
                if (!$currentgroup || $record->groupid == $currentgroup || $record->groupid == 0) {
                    $records[] = $record;
                }
            }
        }
        if ($records) {  // OK, we can show this one
            data_print_template('singletemplate', $records, $data, $search, $page);
        } else if ($search){
            notify(get_string('nomatch','data'));
        } else {
            notify(get_string('norecords','data'));
        }

    } else {                   //  We have some records to print

        if ($mode == 'single') {                  // Single template
            $baseurl = 'view.php?d='.$data->id.'&amp;mode=single&amp;';

            print_paging_bar($totalcount, $page, $nowperpage, $baseurl, $pagevar='page');

            if (empty($data->singletemplate)){
                notify(get_string('nosingletemplate','data'));
                data_generate_default_template($data, 'singletemplate', 0, false, false);
            }

            data_print_template('singletemplate', $records, $data, $search, $page);

            print_paging_bar($totalcount, $page, $nowperpage, $baseurl, $pagevar='page');

        } else {                                  // List template
            $baseurl = 'view.php?d='.$data->id.'&amp;';

            print_paging_bar($totalcount, $page, $nowperpage, $baseurl, $pagevar='page');

            if (empty($data->listtemplate)){
                notify(get_string('nolisttemplate','data'));
                data_generate_default_template($data, 'listtemplate', 0, false, false);
            }
            echo $data->listtemplateheader;
            data_print_template('listtemplate', $records, $data, $search, $page);
            echo $data->listtemplatefooter;

            print_paging_bar($totalcount, $page, $nowperpage, $baseurl, $pagevar='page');
        }

    }

    if ($records || $search || $page) {
        data_print_preference_form($data, $perpage, $search, $sort, $order);
    }

/// If we have blocks, then print the left side here
    if (!empty($CFG->showblocksonmodpages)) {
        echo '</td>';   // Middle column
        if ((blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $PAGE->user_is_editing())) {
            echo '<td style="width: '.$blocks_preferred_width.'px;" id="right-column">';
            blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
            echo '</td>';
        }
        echo '</table>';
    }

    print_footer($course);
?>
