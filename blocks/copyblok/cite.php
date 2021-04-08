<?php
require_once('../../config.php');
$cid = required_param('course', PARAM_INT);
$bid = required_param('cite', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $cid))){
	print_error("Need course id");
}
if (!$block = $DB->get_record("block",array("name" => "copyblok"))){
	print_error("Block not inicialized");
}
if (!$block_instance = $DB->get_record("block_instances",array("id" => $bid))){
	print_error("No copyblok block in course");
}

$block_content = unserialize(base64_decode($block_instance->configdata));

if(empty($block_content))
	$block_content = new stdClass;

if (empty($block_content->authors)) {
	$block_content->authors = get_string('default_authors','block_copyblok');
}

if (empty($block_content->coursename)) {
	$block_content->coursename = $COURSE->fullname;
}

if (empty($block_content->published)) {
	$block_content->published = time();
}

if (empty($block_content->issn)) {
	$block_content->issn = 'XXXX-XXXXX-XX';
}

require_login($course->id);
//setting basic options
$PAGE->set_url('/blocks/copyblok/cite.php', array('course' => $course->id, 'cite' => $block_instance->id));
$PAGE->set_title(get_string('cite_title','block_copyblok'));
$PAGE->set_heading(get_string('howcitate','block_copyblok'));
$PAGE->set_pagelayout('course');
//Reformát autors
$a_authors = array_filter(explode(",", $block_content->authors));
$a_authors_lenght = count($a_authors);
$authors = "";
for($i=0;$i<$a_authors_lenght;$i++) {
    $part_of_name = explode(" ", trim($a_authors[$i]));
    $part_of_name_lenght = count($part_of_name);
    $part_of_name_lenght--;
    if( $i == 0 ) $authors .= mb_strtoupper($part_of_name[$part_of_name_lenght], 'UTF-8') . ', ';
    for($j=0;$j<$part_of_name_lenght;$j++)
    {
        $authors .= $part_of_name[$j] . ' ';
    }
    if( $i != 0 ) $authors .= mb_strtoupper($part_of_name[$part_of_name_lenght], 'UTF-8');
    $authors = trim($authors);
    if( $i == ($a_authors_lenght - 2) )
        //$authors .= ' a ';
        $authors .= ' ' . get_string('and','block_copyblok') . ' ';
    elseif ($a_authors_lenght > 0 && $i < ($a_authors_lenght -1))
        $authors .= ', ';
}
//create citate
$cite = $authors;
$cite .= '. ' . strip_tags($block_content->coursename) . '. In: <i>' . get_config ( 'copyblok' , 'webname' );
$cite .= '</i> [online]. ' . date('j.m.Y. ',$block_content->published);
$cite .= get_config ( 'copyblok' , 'holdersplace' ) . ': ' . get_config ( 'copyblok' , 'holders' ) . ', ';
$cite .= date('Y',$block_content->published).' [cit. '.date('Y-n-j').']. ';
$cite .= 'Dostupné z: &lt;'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'&gt;. ';
$issn = strip_tags($block_content->issn);
if( strlen($issn) == 11 )
  $cite .= 'ISSN '.$issn.'.';

echo $OUTPUT->header();

echo $cite;

echo $OUTPUT->footer();