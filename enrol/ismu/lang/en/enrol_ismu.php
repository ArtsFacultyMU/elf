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
 * Strings for component 'enrol_ismu', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package   enrol_ismu
 * @author    2012 Filip Benčo
 */

$string['autumn'] = 'Autumn';
$string['course_codes'] = 'Course code';
$string['course_codes_help'] = '<p>The code assigned to your course in the Information System of the Masaryk University (IS MU) should be entered into this field. If you need to enter more than one code, insert them all separated by commas (no spaces). Entering the code will not in any way affect the name of the course (do not mistake it for the <i>Short name</i> listed further up on this page).</p>

<b>Using IS MU course codes</b>
<ul>
<li><i><b>FF0001,FF0002,FF0003</b></i> - If you need to enrol students from several IS MU courses, enter all the IS MU course codes into the field and separate them with commas. </li>
<li><i><b>FF0001/A,FF0001/C</b></i> - If you need to enrol students from specific IS MU seminar groups (one or more) only, enter the group codes as listed in the Information system (usually including a slash). When entering more seminar group codes, separate the individual codes with commas (no spaces).</li>
<li><i><b>FF0001/A,FF0003</b></i> - Course codes entered into the field can be combined in a number of ways. For example, you can insert one code referring to a whole IS MU course and combine it with another IS MU code containing one seminar group only.</li>
</ul>

<p><b>NB:</b> If a code referring to a particular IS MU seminar group (e.g. FF0001/A) is filled in the field <i>Course code</i>, all the students belonging to this particular group will be enrolled in the ELF course. Whether or not a similar seminar group will also be created in the ELF course
is, however, further dependent on which setting has been applied in the field <i>Create seminar groups</i> below.</p>';
$string['create_seminars'] = 'Create seminar groups';
$string['create_seminars_help'] = "This function is only available if you selected <i>Yes – enrol enrolled</i> in the previous field <i>Autoenrol students from IS MU</i>.

If you select the option <i><b>Create seminar groups</b></i>, seminar groups will be automatically created in the ELF course according to the IS MU data. Also, all the students will be automatically split in these groups. If you select <i><b>Don't create seminar groups</b></i>, no course groups will be created and similarly the students enrolled in the course will not be split into any groups.

<b>NB:</b> If you want to use both automatically and manually-created groups in the ELF course, remember to disable the autoenrol function before creating the manual groups (<i>Autoenrol students from IS MU -- NO</i>). Otherwise, your manually-created groups will be deleted during the next automatic course enrolment update (app. every 30 minutes).";
$string['create_seminars_no'] = 'Don\'t create seminar groups';
$string['create_seminars_yes'] = 'Create seminar groups';
$string['current_period'] = 'Current IS MU period';
$string['current_period_desc'] = 'Current period, that is used for downloading data from IS MU';
$string['enrol_enrolled'] = 'Yes - Enrol enrolled';
$string['groupswarning'] = 'WARNING!!';
$string['ismu:config'] = 'Configure IS MU instances';
$string['ismu:unenrol'] = 'Unenrol users from the course';
$string['ismu:unenrolself'] = 'Unenrol self from the course';
$string['enrol_no'] = 'No';
$string['enrol_registered'] = 'Yes - Enrol registered ';
$string['enrol_status'] = 'Autoenrol students from IS MU';
$string['enrol_status_help'] = '<p>Using this function, you can automatically enrol such students who have taken up the corresponding subject through IS MU (in the set academic period). The autoenrol function is typically made available at the beginning of the each term (according to the IS MU dates). If you planning on using the autoenrol function, remember to correctly fill in the fields <i>Couse ID number</i>, <i>Faculty</i> and <i>Semester</i> as well. If you want to automatically split students into groups (according to IS MU), set the appropriate value in the field <i>Creating seminar groups</i>.</p> <b>Using the IS MU autoenrol function</b> <ul><li><b><i>NO</i></b> - The autoenrol function in not enabled (has been disabled). </li><li><b><i>YES - ENROL REGISTERED</i></b> - All the students who have <b>registered</b> for the course in the Information system will be enrolled. This setting does not support the automatic set-up of IS MU seminar groups in the ELF course. </li><li><b><i>YES - ENROL ENROLLED</i></b> - All the students who are <b>enrolled</b> in the course in the Information system will be enrolled in the ELF course. This setting supports the automatic set-up of IS MU seminar groups (see the field <i>Creating seminar groups</i> further down on the <i>Course Settings</i> page).</ul><p><b>NB: </b>Even if you only want to open your course to its autoenrolled students, you still have to set up the <i><a href="http://elf.phil.muni.cz/elf/help.php?module=moodle&file=enrolmentkey.html">Enrolment key</a></i> – see the appropriate field below on the <i>Course Settings</i> page. <b>Notice: This service is <u>only</u> available for courses taught at the faculties of Arts, Social Sciences, and Sport Studies.</b> Mass collection of student course data from IS for other faculties has to be approved by the respective vice dean (for education). If interested, ask him/her!</p>';
$string['groupswarning'] = '<p style="text-align: justify; padding: 5px; background-color: lightgrey; border: 2px solid red; max-width: 600px;"><span style="color: red; font-weight: bold;">NB:</span> At the moment, <strong>you are actively using the automatic enrolment of students from IS MU</strong>. All manually created groups will be automatically deleted! If you want to use manually-created groups, first disable the automatic enrolment in the course settings.</p>';
$string['load_archived_students'] = 'Enrol all erchived students';
$string['load_archived_students_help'] = 'Enrol all erchived students';
$string['long_load_notice_label'] = 'Attention';
$string['long_load_notice'] = 'Enrollig students from previous period will take some time. Please be patient.';
$string['period'] = 'Enrolment period';
$string['period_help'] = 'Help for enrolment period';
$string['pluginname'] = 'Autoenrol students from IS MU';
$string['pluginname_desc'] = 'Settings for enrolment plugin against IS MU';
$string['spring'] = 'Spring';
$string['students_courses'] = 'Courses for students';
$string['students_courses_desc'] = 'Course codes (ID) separated by commas (no spaces) where to enrol all students.';
$string['teachers_courses'] = 'Courses for teachers';
$string['teachers_courses_desc'] = 'Course codes (ID) separated by commas (no spaces) where to enrol all teachers.';
$string['students_forums'] = 'Forums for students';
$string['students_forums_desc'] = 'Forums codes (ID) separated by commas (no spaces) where to subscribe all students.';
$string['teachers_forums'] = 'Forums for teachers';
$string['teachers_forums_desc'] = 'Forums codes (ID) separated by commas (no spaces) where to subscribe all teachers.';
$string['students_groups'] = 'Groups for students';
$string['students_groups_desc'] = 'Groups codes (ID) separated by commas (no spaces) where to sign all students.';
$string['teachers_groups'] = 'Groups for teachers';
$string['teachers_groups_desc'] = 'Groups codes (ID) separated by commas (no spaces) where to sign all teachers.';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['task_sync_data_from_ismu'] = 'Download data from IS MU';
$string['task_sync_global_enrolments'] = 'Sync global enrolments for students and teachers';




