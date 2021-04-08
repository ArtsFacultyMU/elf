<?php

require_once('../../config.php');

$labels = $DB->get_records('label');
foreach ($labels as $label) {
    $label->intro = convert_to_https($label->intro);
    $DB->update_record('label',$label);
}
unset($labels);
echo 'Labels completed</br>';

$pages = $DB->get_records('page');
foreach($pages as $page) {
    $page->content = convert_to_https($page->content);
    $DB->update_record('page',$page);
}
unset($pages);
echo 'Pages completed</br>';

$books = $DB->get_records('book_chapters');
foreach($books as $book) {
    $book->content = convert_to_https($book->content);
    $DB->update_record('book_chapters',$book);
}
unset($books);
echo 'Books completed</br>';

$assignments = $DB->get_records('assing');
foreach($assignments as $assign) {
    $assign->intro = convert_to_https($assign->intro);
    $DB->update_record('assign',$assign);
}
unset($assignments);
echo 'Assignments completed</br>';

$quizes = $DB->get_records('guiz');
foreach($quizes as $quiz) {
    $quiz->intro = convert_to_https($quiz->intro);
    $DB->update_record('quiz',$quiz);
}
unset($quiz);
echo 'Quizes completed</br>';

$newassignments = $DB->get_records('newassignment');
foreach($newassignments as $newassign) {
    $newassign->intro = convert_to_https($newassign->intro);
    $DB->update_record('newassignment',$newassign);
}
unset($newassignments);
echo 'New Assignments completed</br>';

$glossaries = $DB->get_records('glossary');
foreach($glossaries as $glossary) {
    $glossary->intro = convert_to_https($glossary->intro);
    $DB->update_record('glossary',$glossary);
}
unset($glossaries);
echo 'Glossaries completed</br>';

$glossaryEntries = $DB->get_records('glossary_entries');
foreach($glossaryEntries as $glossary) {
    $glossary->definition = convert_to_https($glossary->definition);
    $DB->update_record('glossary_entries',$glossary);
}
unset($glossaryEntries);
echo 'Glossary entries completed</br>';

echo 'DONE!!';



function convert_to_https($text) {
    $search = array(
        'src="http://quizlet.com/',
        'src="http://www.quizlet.com/',
        'src="​http://www.youtube.com/',
        'src="​​http://youtube.com/',
        'src="​​http://vimeo.com/',
​        'src="​http://www.vimeo.com/',
​        'src="​http://glogster.com/',
        'src="​​http://www.glogster.com/',
​        'src="​http://edu.glogengine.com/',
​        'src="​http://vhss-d.oddcast.com/',
​        'src="​http://www.vocaroo.com/',
        'src="​​http://vocaroo.com/',
​        'src="​http://www.slideshare.net/',
​        'src="​http://slideshare.net/',
​        'src="​http://e.issuu.com/',
        'src="​​http://www.prezi.com/',
​        'src="​http://prezi.com/',
        'src="​​http://www.dipity.com/',
​        'src="​http://dipity.com/',
        'src="​​http://www.goanimate.com/',
​        'src="​http://goanimate.com/',
        'data="http://quizlet.com/',
        'data="http://www.quizlet.com/',
        'data="​http://www.youtube.com/',
        'data="​​http://youtube.com/',
        'data="​​http://vimeo.com/',
​        'data="​http://www.vimeo.com/',
​        'data="​http://glogster.com/',
        'data="​​http://www.glogster.com/',
​        'data="​http://edu.glogengine.com/',
​        'data="​http://vhss-d.oddcast.com/',
​        'data="​http://www.vocaroo.com/',
        'data="​​http://vocaroo.com/',
​        'data="​http://www.slideshare.net/',
        'data="​http://slideshare.net/',
​        'data="​http://e.issuu.com/',
        'data="​​http://www.prezi.com/',
​        'data="​http://prezi.com/',
        'data="​​http://www.dipity.com/',
​        'data="​http://dipity.com/',
        'data="​​http://www.goanimate.com/',
​        'data="​http://goanimate.com/'
    );
    $replace = array(
        'src="https://quizlet.com/',
        'src="https://www.quizlet.com/',
        'src="​https://www.youtube.com/',
        'src="​​https://youtube.com/',
        'src="​​https://vimeo.com/',
​        'src="​https://www.vimeo.com/',
​        'src="​https://glogster.com/',
        'src="​​https://www.glogster.com/',
​        'src="​https://edu.glogengine.com/',
​        'src="​https://vhss-d.oddcast.com/',
​        'src="​https://www.vocaroo.com/',
        'src="​​https://vocaroo.com/',
​        'src="​https://www.slideshare.net/',
​        'src="​https://slideshare.net/',
​        'src="​https://e.issuu.com/',
        'src="​​https://www.prezi.com/',
​        'src="​https://prezi.com/',
        'src="​​https://www.dipity.com/',
​        'src="​https://dipity.com/',
        'src="​​https://www.goanimate.com/',
​        'src="​https://goanimate.com/',
        'data="https://quizlet.com/',
        'data="https://www.quizlet.com/',
        'data="​https://www.youtube.com/',
        'data="​​https://youtube.com/',
        'data="​​https://vimeo.com/',
​        'data="​https://www.vimeo.com/',
​        'data="​https://glogster.com/',
        'data="​​https://www.glogster.com/',
​        'data="​https://edu.glogengine.com/',
​        'data="​https://vhss-d.oddcast.com/',
​        'data="​https://www.vocaroo.com/',
        'data="​​https://vocaroo.com/',
​        'data="​https://www.slideshare.net/',
        'data="​https://slideshare.net/',
​        'data="​https://e.issuu.com/',
        'data="​​https://www.prezi.com/',
​        'data="​https://prezi.com/',
        'data="​​https://www.dipity.com/',
​        'data="​https://dipity.com/',
        'data="​​https://www.goanimate.com/',
​        'data="​https://goanimate.com/'
    );
    
    $repalced_text = str_replace($search,$replace,$text);

    return $repalced_text;
}