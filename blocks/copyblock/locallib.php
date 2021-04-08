<?php

function create_cite($title, $authors, $published, $holders) {
	global $CFG;
	$cite  = ' <p id="inserttext" style="text-align: left;"><font size="1">__________<br />'.$authors.'.';
	$cite .= ' Materiál e-kurzu <em>'.$title.'</em>. &lt;<a title="Domovská';
	$cite .= ' stránka portálu" href="'.$CFG->wwwroot.'"';
	$cite .= ' target="_blank">'.$CFG->wwwroot.'</a>&gt;. (c) '.$holders.' ';
	$cite .= date('Y',$published).'.</font><br /></p> ';
	return $cite;
}

function append_page($cite) {
	global $COURSE, $DB;
	
	$pages = $DB->get_records('page',array('course' => $COURSE->id));
	foreach($pages as $page) {
		//remove old citate if exists
		$pos = strpos($page->content,'<p id="inserttext"');
		if ($pos != false){
			$page->content = substr($page->content,0,$pos);
		}
		
		//append new citate
		$page->content = $page->content.$cite;
		$page->timemodified = time();
		
		$DB->update_record('page', $page);
	}
}

function append_book($cite) {
	global $COURSE, $DB;
	
	$books = $DB->get_records('book',array('course' => $COURSE->id));
	foreach($books as $book) {
		$chapters = $DB->get_records('book_chapters',array('bookid' => $book->id));
		foreach($chapters as $chapter) {
			//remove old citate if exists
			$pos = strpos($chapter->content,'<p id="inserttext"');
			if ($pos != false){
				$chapter->content = substr($chapter->content,0,$pos);
			}
			
			//append new citate
			$chapter->content = $chapter->content.$cite;
			$chapter->timemodified = time();
			
			$DB->update_record('book_chapters', $chapter);
		}
	}
}

function append_lesson($cite) {
	global $COURSE, $DB;
	
	$lessons = $DB->get_records('lesson',array('course' => $COURSE->id));
	foreach($lessons as $lesson) {
		$pages = $DB->get_records('lesson_pages',array('lessonid' => $lesson->id));
		foreach($pages as $page) {
			//remove old citate if exists
			$pos = strpos($page->contents,'<p id="inserttext"');
			if ($pos != false){
				$page->contents = substr($page->contents,0,$pos);
			}
			
			//append new citate
			$page->contents = $page->contents.$cite;
			$page->timemodified = time();
			
			$DB->update_record('lesson_pages', $page);
		}
	}
}