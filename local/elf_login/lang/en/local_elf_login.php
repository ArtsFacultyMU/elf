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
 * English language strings for local ELF login form.
 *
 * @package    local_elf_login
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Login page for ELF';
$string['unified_login'] = 'Unified Login';

$string['teachers_and_students'] = 'Teachers and students';
$string['others_and_guests'] = 'Others and guests';

$string['cannot_login'] = 'I can\'t log in';
$string['first_time_here'] = 'Is this your first time here?';

$string['in_ismuni'] = 'in IS MUNI';
$string['outside_ismuni'] = 'outside IS MUNI';

$string['teachers_students_info_1'] = 'Provided you have an active account in
        the <a href="{$a->ismunilink}" target="_blank">Information system of
        Masaryk University (IS MUNI)</a>, click on the 
        university logo on the left and fill in your <b>UCO (university
        number)</b> and your <b>primary password</b>. If you haven\'t
        want to restore access to your university account,
        you can ask for a new password <b><a href="{$a->ismunipasslink}"
        target="_blank">by clicking on this link</a></b>.';
$string['teachers_students_info_2'] = '<b>FIRST LOGIN:</b> Students and teachers
        at <b>FF</b>, <b>FSS</b> and <b>FSpS</b> have their accounts in ELF
        <b>set up automatically</b>. Other users that have a valid IS MUNI
        account can set up their own account immediately here <b>by logging into
        ELF</b> using their IS MUNI credentials (see above); upon first login you
        will be re-directed to your new personal profile page, which need to be
        edited and saved before being allowed to enter the ELF itself.
        After this initial setup, you will be using the same IS MUNI credentials
        to re-enter the ELF at any time later on.';

$string['others_guests_info_1'] = 'Use this way of logging in ELF if you received
        your login credentials (i.e. username and password) from the teacher of
        your course or from the site admin, but you <b>do not have an active
        account in the <a href="{$a->ismunilink}" target="_blank">Information
        system of Masaryk University</a></b>. This may apply to students of
        other institutions, external teachers, preparatory courses, etc.';
$string['others_guests_info_2'] = 'Also, you can use this login method when you
        want to access the ELF e-learning site <b>as a guest</b> ("Login as a
        guest" button) - some courses may be accessible by guests without any
        need of logging in, others may allow guests with an enrolment key
        (if set up by the course admin).';

$string['unknown_error'] = 'Unknown error ({$a}).';