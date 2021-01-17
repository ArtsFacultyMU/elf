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
 * Alternative login form for ELF@MUNI ARTS.
 *
 * @package    local_elf_login
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');

// Initialize variables.
$errorcode = optional_param('errorcode', 0, PARAM_INT);
$errormsg = '';

// Take care of basic layout.
$context = context_system::instance();
$PAGE->set_url(new moodle_url('/local/elf_login/'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('login');
$PAGE->set_title("$SITE->fullname: " . get_string("loginsite"));
$PAGE->set_heading("$SITE->fullname");

// Handle error messages.
switch($errorcode) {
    case 0:
        break;
    case 1:
        $errormsg = get_string("cookiesnotenabled");
        break;
    case 2:
        $errormsg = get_string('username').': '.get_string("invalidusername");
        break;
    case AUTH_LOGIN_UNAUTHORISED:
        $errormsg = get_string("unauthorisedlogin", "", clean_param($_GET["username"], PARAM_RAW));
        break;
    case 3:
        $errormsg = get_string("invalidlogin");
        break;
    case 4:
        $errormsg = get_string('sessionerroruser', 'error');
        break;
    default:
        $errormsg = get_string('unknown_error', 'local_elf_login', $errorcode);
}

// Manage auth plugins.
$authsequence = get_enabled_auth_plugins(true);

foreach($authsequence as $authname) {
    $authplugin = get_auth_plugin($authname);
    $authplugin->loginpage_hook();
}

echo $OUTPUT->header();

if (isloggedin() and !isguestuser()) {
    // prevent logging when already logged in, we do not want them to relogin by accident because sesskey would be changed
    echo $OUTPUT->box_start();
    $logout = new single_button(new moodle_url('/login/logout.php', array('sesskey'=>sesskey(),'loginpage'=>1)), get_string('logout'), 'post');
    $continue = new single_button(new moodle_url('/'), get_string('cancel'), 'get');
    echo $OUTPUT->confirm(get_string('alreadyloggedin', 'error', fullname($USER)), $logout, $continue);
    echo $OUTPUT->box_end();
} else {
    $renderable = new \local_elf_login\output\loginform_renderable($authsequence);
    $renderable->set_error($errormsg);
    echo $OUTPUT->render($renderable);
}

echo $OUTPUT->footer();