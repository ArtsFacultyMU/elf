<?php
ini_set('memmory_limit', '256M');
require_once('../../config.php');

$courses = $DB->get_records('course',null,'timecreated');

$splittedCourses = array();

foreach($courses as $c) 
    $splittedCourses[date('o',$c->timecreated)][]=$c;

foreach($splittedCourses as $year => $sc)
    echo "Year: ".$year." - ".count($sc)." new courses<br/>";

unset($courses);
unset($splittedCourses);

echo "<br/><br/>";

$users = $DB->get_records_select('user','firstaccess != 0',null,'firstaccess');

$splittedUsers = array();

foreach($users as $u) 
    $splittedUsers[date('o',$u->firstaccess)][]=$u;

foreach($splittedUsers as $year => $su)
    echo "Year: ".$year." - ".count($su)." new active users<br/>";

unset($users);
unset($splittedUsers);

echo "<br/><br/>";

$teachers = $DB->get_records_sql('SELECT DISTINCT(u.id) AS userid, u.firstaccess AS firstaccess FROM {user} AS u INNER JOIN {role_assignments} AS r ON r.userid WHERE u.firstaccess != 0 AND r.roleid = 3 ORDER BY u.firstaccess');

$splittedTeachers = array();

foreach($teachers as $t) 
    $splittedTeachers[date('o',$t->firstaccess)][]=$t;

foreach($splittedTeachers as $year => $st)
    echo "Year: ".$year." - ".count($st)." new active teachers<br/>";

unset($teachers);
unset($splittedTeachers);