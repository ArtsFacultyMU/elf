<?php
class block_is_links extends block_list {
	
	protected $course;
	protected $need_config;
	
	public function instance_allow_config() {
		true;
	}
	
    public function init() {
        $this->title = get_string('pluginname', 'block_is_links');
    }

    public function specialization() {
        $this->course = NULL;
    	$this->need_config = false;
    	if($this->page->course->id != SITEID) {
    		$this->course = $this->page->course;
    	}
    	if(!isset($this->config))
    		$this->config = new stdClass;
    	if(!isset($this->config->period))
    		$this->need_config = true;
    	if(!isset($this->config->iscodes))
    		$this->need_config = true;
    }
    
    public function get_content() {
    	global $CFG, $DB;
           
    	$this->content = new stdClass;
    	$this->content->items = array();
    	$this->content->icons = array();
    	$this->content->footer = '';
    	
    	if($this->need_config) {
    		$this->content->items[] = get_string('missingconfiguration','block_is_links');
    		return $this->content;
    	}
    	
    	$this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
    
        if($this->course == NULL) { //if we are not inside course
        	return $this->content;
        }
        
        $iscodes = explode(',', $this->config->iscodes);
        $iscodes = array_map('trim', $iscodes);
        
        $context = context_course::instance($this->course->id);
        
        $period = $DB->get_record('is_semesters', array('id' => $this->config->period));
        
	    foreach ($iscodes as $iscode) {
                $obdobi = '';
	       	if($period->period == 'autumn')  
	       		$obdobi = 'podzim';
	       	else 
	       		$obdobi = 'jaro'; 

	     	/// VSICHNI: sylabus
	      	$this->content->items[] = '<strong><a style="margin-bottom:4px; display:block;" href="http://is.muni.cz/predmet/'.$period->faculty.'/'.$obdobi.$period->year.'/'.$iscode.'" target="_blank" alt="'.addslashes($iscode).'" title="'.addslashes($iscode).'">'.get_string("shortsylabus",  "block_is_links").'</a></strong>';
	       
	       	/// UCITEL: Hl. stranka
	       	if (has_capability('block/is_links:teacher_view', $context)) {
	       		$this->content->items[] = '<a style="margin-bottom:4px; display:block;" href="https://is.muni.cz/auth/ucitel/?fakulta='.$period->faculty.';obdobi='.$period->code.';kod='.$iscode.'" target="_blank">'.get_string("mainpage", "block_is_links").'</a>'; 
	       	}
	       		
	       	/// VSICHNI: st. materialy  			
        	$this->content->items[] ='<a style="margin-bottom:4px; display:block;" href="https://is.muni.cz/auth/el/'.$period->faculty.'/'.$obdobi.$period->year.'/'.$iscode.'/" target="_blank">'.get_string("resources", "block_is_links").'</a>'; 
	       		
	       	/// UCITEL: interaktivni osnovy, seznam studentu, zkousky, hodnoceni
	       	if (has_capability('block/is_links:teacher_view', $context)) {       
	       		$this->content->items[] = '<a style="margin-bottom:4px; display:block;" href="https://is.muni.cz/auth/ucitel/seznam.pl?fakulta='.$period->faculty.';obdobi='.$period->code.';kod='.$iscode.'" target="_blank">'.get_string("studentslist", "block_is_links").'</a>';
	       
	       		$this->content->items[] = '<a style="margin-bottom:4px; display:block;" href="https://is.muni.cz/auth/ucitel/znamky.pl?fakulta='.$period->faculty.';obdobi='.$period->code.';kod='.$iscode.';jednotl=1;bez_bloku=1" target="_blank">'.get_string("marks", "block_is_links").'</a>';
	       	}
	       
	       	/// STUDENT: prihlasovani na zkousky
	       	if (has_capability('block/is_links:student_view', $context) && !has_capability('block/is_links:teacher_view', $context)) {
	       		$this->content->items[] = '<a style="margin-bottom:4px; display:block;" href="https://is.muni.cz/auth/student/prihl_na_zkousky.pl?fakulta='.$period->faculty.';obdobi='.$period->code.';kod='.$iscode.'" target="_blank">'.get_string("examsignup", "block_is_links").'</a>';
	       	}
	    }
	       
	       
	        $this->content->footer = '';  
        
    	return $this->content;
    }
    
    public function instance_config_save($data, $nolongerused = false) {
    	global $DB;
    	$semester = explode('_', $data->semester);
    	$period = $DB->get_record('is_semesters', array('year' => $semester[0], 'period' => $semester[1], 'faculty' => $data->faculty));
    	unset($data->semester);
    	unset($data->faculty);
    	$data->period = $period->id;
    	parent::instance_config_save($data, $nolongerused);
    }
}