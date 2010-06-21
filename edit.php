<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Manage files in folder module instance
 *
 * @package   mod-folder
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/mod/folder/locallib.php");
require_once("$CFG->dirroot/mod/folder/edit_form.php");
require_once("$CFG->dirroot/repository/lib.php");

$id = required_param('id', PARAM_INT);  // Course module ID

$cm = get_coursemodule_from_id('folder', $id, 0, false, MUST_EXIST);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$folder = $DB->get_record('folder', array('id'=>$cm->instance), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_login($course, true, $cm);
require_capability('moodle/course:managefiles', $context);

add_to_log($course->id, 'folder', 'edit', 'edit.php?id='.$cm->id, $folder->id, $cm->id);

$data = new stdclass;
$data->id = $cm->id;

$options = array('subdirs'=>1, 'maxbytes'=>$CFG->maxbytes, 'maxfiles'=>-1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);

$PAGE->set_url('/mod/folder/edit.php', array('id' => $cm->id));

$PAGE->set_title($course->shortname.': '.$folder->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($folder);


$form = new mod_folder_edit_form(null, array('id'=>$cm->id));

if ($formdata = $form->get_data()) {
    $formdata = file_postupdate_standard_filemanager($formdata, 'files', $options, $context, 'folder_content', 0);
    redirect(new moodle_url('/mod/folder/view.php', array('id'=>$cm->id)));
} else {
    file_prepare_standard_filemanager($data, 'files', $options, $context, 'folder_content', 0);
    $form->set_data($data);
    echo $OUTPUT->header();
    echo $OUTPUT->box_start('generalbox foldertree');
    $form->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
}
