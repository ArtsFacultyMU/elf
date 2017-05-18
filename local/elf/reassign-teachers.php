<?php
require_once('../../config.php');
require_once($CFG->dirroot.'/local/elf/islib.php');

define('COURSE_CREATOR',2);

reset_is_teachers();
$ISTeachers = $DB->get_recordset_sql("SELECT u.* FROM (SELECT * FROM {is_teachers} WHERE imported=1) AS t INNER JOIN (SELECT * FROM {user} WHERE auth=?) AS u ON t.uco = u.username", array(ELF_AUTH_TYPE));
$assignedTeachersCount = 0;
$allTeachers = 0;

$context = get_context_instance(CONTEXT_SYSTEM, 0); 

foreach ($ISTeachers as $ISTeacher) {
    $allTeachers++;

    if (!user_has_role_assignment($ISTeacher->id, COURSE_CREATOR, $context->id)) { 
        if (!role_assign(COURSE_CREATOR, $ISTeacher->id, $context->id))  
            echo("Nepodarilo se priradit uzivateli {$teacher->username}: $teacher->firstname $teacher->surname, Moodle ID: {$teacher->id} roli COURSE_CREATOR.\n");
        else
           $assignedTeachersCount ++;
            
    }
}
echo "New assigned teachers roles: $assignedTeachersCount\n";
echo "All teachers: $allTeachers\n";