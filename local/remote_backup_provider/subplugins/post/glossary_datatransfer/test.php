<?php

require_once(__DIR__ . '/../../../../../config.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/mod/glossary/lib.php');

$course = $DB->get_record('course', 
                array('id' => 56),
                '*', MUST_EXIST);

foreach(get_all_instances_in_course('glossary', $course) as $instance) {
  var_dump($instance);continue;
  $glossary = $DB->get_record('glossary', array('id'=>$instance->id));
  var_dump($glossary);continue;
  var_dump(glossary_generate_export_file($glossary));die;
}
