<?php

define('ELF_AUTH_TYPE', 'shibboleth');
define('ELF_MNET_HOST_ID', 1);

function get_not_imported_is_teachers() {
    global $DB;
    return $DB->get_recordset_sql("SELECT uco, prijmeni, jmeno, username FROM {is_teachers} WHERE imported=0");
}

function reset_is_teachers() {
    global $DB;

    //reset table
    $DB->execute("UPDATE {is_teachers} SET imported = 0");

    // Mark existing teachers
    if (!$DB->execute("UPDATE {is_teachers}, {user} SET {is_teachers}.imported = 1 WHERE {user}.username = {is_teachers}.username AND {user}.auth=?", array(ELF_AUTH_TYPE))) {
        echo "Nepodarilo se oznacit ucitele, kteri uz jsou v moodle\n";
        if (mysql_error())
            echo mysql_error();
    }

    // Update name an surname for existing teachers
    if (!$DB->execute("UPDATE {is_teachers}, {user} SET {user}.firstname={is_teachers}.jmeno, {user}.lastname={is_teachers}.prijmeni 
        WHERE {user}.username = {is_teachers}.username AND {user}.auth=? AND ({is_teachers}.jmeno != {user}.firstname OR {is_teachers}.prijmeni != {user}.lastname)", array(ELF_AUTH_TYPE))) {
        echo "Nepodarilo se aktualizovat jmena ucitelu, kteri uz jsou v moodle\n";
        if (mysql_error())
            echo mysql_error();
    }
}

function get_not_imported_is_students() {
    global $DB;
    return $DB->get_recordset_sql("SELECT uco, username, prijmeni, jmeno FROM {is_students} WHERE imported=0");
}

function reset_is_students() {
    global $DB;

    //reset table
    $DB->execute("UPDATE {is_students} SET imported = 0");

    // Mark existing teachers
    if (!$DB->execute("UPDATE {is_students}, {user} SET {is_students}.imported = 1 WHERE {user}.username = {is_students}.username AND {user}.auth=?", array(ELF_AUTH_TYPE))) {
        echo "Nepodarilo se oznacit ucitele, kteri uz jsou v moodle\n";
        if (mysql_error())
            echo mysql_error();
    }

    // Update name an surname for existing teachers
    if (!$DB->execute("UPDATE {is_students}, {user} SET {user}.firstname={is_students}.jmeno, {user}.lastname={is_students}.prijmeni 
        WHERE {user}.username = {is_students}.username AND {user}.auth=? AND ({is_students}.jmeno != {user}.firstname OR {is_students}.prijmeni != {user}.lastname)", array(ELF_AUTH_TYPE))) {
        echo "Nepodarilo se aktualizovat jmena ucitelu, kteri uz jsou v moodle\n";
        if (mysql_error())
            echo mysql_error();
    }
}

function get_default_moodle_user_object() {
    $user = new stdClass;

    $user->auth = ELF_AUTH_TYPE;
    $user->confirmed = 1;
    $user->mnethostid = ELF_MNET_HOST_ID;
    $user->password = 'not cached';
    $user->idnumber = '';
    $user->icq = '';
    $user->skype = '';
    $user->yahoo = '';
    $user->aim = '';
    $user->msn = '';
    $user->phone1 = '';
    $user->phone2 = '';
    $user->institution = '';
    $user->department = '';
    $user->address = '';
    $user->city = 'Brno';
    $user->country = 'CZ';
    $user->lang = 'cs';
    $user->theme = '';
    $user->lastip = '';
    $user->secret = '';
    $user->url = '';
    $user->timecreated = time();

    return $user;
}
