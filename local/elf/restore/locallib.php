<?php 

function id_end(&$text, $offset) {
	if(($pos = strpos($text, "\"", $offset))!==false) return $pos;
	if(($pos = strpos($text, "\&", $offset))!==false) return $pos;
	return false;
}

/**
 * method repairs old resources links
 * @param string $text - text, where we want to repair links
 * @param int $course - id fo course, where resources belong
 * 
 * @return int - number of repaired links
 */
function repair_links(&$text,$course,&$repairedLinks,&$unrepairedLinks) {
	
	$needle = "/mod/resource/"; // width ?id= is length 
	
	$pos = -1;
	while($pos = strpos($text,$needle,$pos+1)) {
		$beginId = strpos($text,'id=',$pos)+3;
		$id = substr($text, $beginId, id_end($text,$beginId) - $beginId);
		
		if(elf_is_resource($id, $course)) 
			continue;
		
		if(elf_is_page($id, $course)) {
			$repairedLinks++;
			$text = substr_replace($text, "page", $pos+5,8);
			continue;
		}
		if(elf_is_url($id, $course)) {
			$repairedLinks++;
			$text = substr_replace($text, "url", $pos+5,8);
			continue;
		}
		if(elf_is_imscp($id, $course)) {
			$repairedLinks++;
			$text = substr_replace($text, "imscp", $pos+5,8);
			continue;
		}
		$unrepairedLinks++;
	}
}

function repair_assignments($course) {
	global $DB;
	$assignments = $DB->get_records('assignment',array('course' => $course));
	$repaired = 0; 
	foreach($assignments as $assignment) {
		$lastRepaired = $repaired;
		$unrepaired = 0;
		repair_links($assignment->intro, $course, $repaired, $unrepaired);
		if($unrepaired != 0)
			echo "&nbsp;&nbsp;&nbsp;&nbsp;Found defected link in Assignment: ID - ".$assignment->id." | Name: ".$assignment->name."</br>";
		if($lastRepaired < $repaired)
			$DB->update_record('assignment', $assignment);
	}
	return $repaired;
}

function repair_books($course) {
	global $DB;
	$books = $DB->get_records('book',array('course' => $course));
	$repaired = 0;
	foreach($books as $book) {
		$lastRepaired = $repaired;
		$unrepaired = 0;
		//repair book intros
		repair_links($book->intro, $course, $repaired, $unrepaired);
		if($unrepaired != 0)
			echo "&nbsp;&nbsp;&nbsp;&nbsp;Found defected link in Book: ID - ".$book->id." | Name: ".$book->name."</br>";
		if($lastRepaired < $repaired)
			$DB->update_record('book', $book);
		//repair chapter texts
		$chapters = $DB->get_records('book_chapters',array('bookid' => $book->id));
		foreach($chapters as $chapter) {
			$lastRepaired = $repaired;
			$unrepaired = 0;
			repair_links($chapter->content, $course, $repaired, $unrepaired);
			if($unrepaired != 0)
				echo "&nbsp;&nbsp;&nbsp;&nbsp;Found defected link in Book chapter: Book-ID - ".$book->id." | Book-Name: ".$book->name." | Chapter-ID: ".$chapter->id." || Chapter-Title: ".$chapter->title."</br>";
			if($lastRepaired < $repaired)
				$DB->update_record('book_chapter', $chapter);
		}
	}
	return $repaired;
}

function repair_sections($course) {
	global $DB;
	$sections = $DB->get_records('course_sections',array('course' => $course));
	$repaired = 0;
	foreach($sections as $section) {
		$lastRepaired = $repaired;
		$unrepaired = 0;
		repair_links($section->summary, $course, $repaired, $unrepaired);
		if($unrepaired != 0)
			echo "&nbsp;&nbsp;&nbsp;&nbsp;Found defected link in Section: ID - ".$section->id." | Name: ".$section->name."</br>";
		if($lastRepaired < $repaired)
			$DB->update_record('course_sections', $section);
	}
	return $repaired;
}

function repair_labels($course) {
	global $DB;
	$labels = $DB->get_records('label',array('course' => $course));
	$repaired = 0;
	foreach($labels as $label) {
		$lastRepaired = $repaired;
		$unrepaired = 0;
		repair_links($label->intro, $course, $repaired, $unrepaired);
		if($unrepaired != 0)
			echo "&nbsp;&nbsp;&nbsp;&nbsp;Found defected link in Label: ID - ".$label->id." | Name: ".$label->name."</br>";
		if($lastRepaired < $repaired)
			$DB->update_record('label', $label);
	}
	return $repaired;
}

function repair_pages($course) {
	global $DB;
	$pages = $DB->get_records('page',array('course' => $course));
	$repaired = 0;
	foreach($pages as $page) {
		$lastRepaired = $repaired;
		$unrepaired = 0;
		repair_links($page->content, $course, $repaired, $unrepaired);
		if($unrepaired != 0)
			echo "&nbsp;&nbsp;&nbsp;&nbsp;Found defected link in Page: ID - ".$page->id." | Name: ".$page->name."</br>";
		if($lastRepaired < $repaired)
			$DB->update_record('page', $page);
	}
	return $repaired;
}

function elf_is_resource($id,$course) {
	global $DB;
	if(!get_coursemodule_from_id('resource', $id, $course))
		return false;
	return true;
}

function elf_is_page($id,$course) {
	global $DB;
	if(!get_coursemodule_from_id('page', $id, $course))
		return false;
	return true;
}

function elf_is_url($id,$course) {
	global $DB;
	if(!get_coursemodule_from_id('url', $id, $course))
		return false;
	return true;
}

function elf_is_imscp($id,$course) {
	global $DB;
	if(!get_coursemodule_from_id('imscp', $id, $course))
		return false;
	return true;
}