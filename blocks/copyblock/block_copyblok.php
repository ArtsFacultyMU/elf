<?php
/**
 * CITE block instances.
 *
 * @package   block_copyblock
 * @copyright 2015 Jiøí Bilík
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_copyblok extends block_base {
	
	protected $course;
	
	public function init() {
        $this->title = get_string('pluginname', 'block_copyblok');
    }
    
    public function specialization() {
    	global $COURSE;
    	
    	if(!isset($this->config))
    		$this->config = new stdClass;
    	
    	//if (empty($this->config->authors)) {
    	//	$this->config->authors = get_string('default_authors','block_copyblok');
    	//}
    	
    	if (empty($this->config->coursename)) {
    		$this->config->coursename = $COURSE->fullname;
    	}
    	
    	if (empty($this->config->published)) {
    		$this->config->published = time();
    	}
    	
    	if (empty($this->config->issn)) {
    		$this->config->issn = 'XXXX-XXXXX-XX';
    	}
    }
    
    public function get_content() {
    	if ($this->content !== NULL) {
    		return $this->content;
    	}
      
    	
    	$this->content = new stdClass;
    	$this->content->footer = '';
      if (empty($this->config->authors)) {
      	$this->content->text = $this->fill_noautors_content();
      }else{
    	  $this->content->text = $this->fill_content();
      }
    	
    	return $this->content;
    }
    
    public function instance_allow_config() {
    	return true;
    }
    
    public function instance_config_save($data, $nolongerused = false) {
    	//create published timestamp
    	//$data->published = make_timestamp($data->published);
    	      
      $issn = trim($data->issn);
      $data->issn = "";
      for($i=0; $i<(strlen($issn)); $i++){ 
         if(( $issn[$i] >= '0' ) && ( $issn[$i] <= '9' ))
           $data->issn .= $issn[$i];
      }
      $last = strlen($issn) - 1;
      if(($issn[$last] == 'x')||($issn[$last] == 'X'))
        $data->issn .= 'X';
      $issn = $data->issn;
      $data->issn = "";
      for($i=0; $i<(strlen($issn)); $i++){ 
         $data->issn .= $issn[$i];
         if(( $i % 4 ) == 3)
           $data->issn .= '-';
      }      

    	require_once('locallib.php');
    	
    	$cite = create_cite($data->coursename, $data->authors, $data->published, get_config ( 'copyblok' , 'holders' ));
    	
    	if(isset($data->insert_page)) {
			append_page($cite);
    		unset($data->insert_page);
    	}
    	
    	if(isset($data->insert_book)) {
			append_book($cite);
    		unset($data->insert_book);
    	}
    	
    	if(isset($data->insert_lesson)) {
			append_lesson($cite);
    		unset($data->insert_lesson);
    	}
		
    	parent::instance_config_save($data, $nolongerused);
    }
        
    protected function fill_content() {
    	global $COURSE, $CFG;
    	
      $com = $this->config->lcommercial;
      $share = $this->config->ladaptations; 
      $cc='<a rel="license" href="http://creativecommons.org/licenses/by';
      if($com == 0) $cc.='-nc';
      if($share == 0) $cc.='-nd';
      if($share == 2) $cc.='-sa';      
      $cc.='/4.0/"><img alt="Licence Creative Commons" style="border-width:0" src="https://i.creativecommons.org/l/by';
      if($com == 0) $cc.='-nc';
      if($share == 0) $cc.='-nd';
      if($share == 2) $cc.='-sa';      
      $cc.='/4.0/88x31.png" /></a>';
      
    	$output ='<div style="font-size:0.9em">';
    	
    	$output .= '<ul style="list-style: none;margin:0px;padding:0px;">';
    	$output .= '<li><b>'.get_string("title","block_copyblok").'</b></li>';
    	$output .= '<li>'.$this->config->coursename.'</li>';
    	$output .= '<li><b>'.get_string("authors","block_copyblok").'</b></li>';
    	$output .= '<li>'.$this->config->authors.'</li>';
    	$output .= '<li><b>'.get_string("published","block_copyblok").'</b></li>';
    	$output .= '<li>'.date('j. n. Y',$this->config->published).'</li>';
    	//$output .= '<li>'.$this->config->day.'. '.$this->config->month.'. '.$this->config->year.'</li>';
      if($this->config->lcc == 1)
    	     $output .= '<li>'.$cc.'</li>';
    	$output .= '<li>&copy; '.get_config ( 'copyblok' , 'holders' ).' '.date('Y').'</li>';
    	$output .= '</ul>';
    	$output .= '<hr style="color:#777;"/>';
    	$output .= '<div style="text-align:center"><a href="'.$CFG->wwwroot.'/blocks/copyblok/cite.php?course='.$COURSE->id.'&cite='.$this->instance->id.'">'.get_string("howcitate","block_copyblok").'</a></div>';
    	
    	$output .= '</div>';
    	
    	return $output;
    }    
    
    protected function fill_noautors_content() {
    	global $COURSE, $CFG;
    	
    	$output ='<div style="font-size:0.9em">';
    	
    	$output .= '<b>'.get_string("noauthors","block_copyblok").'</b>';
    	$output .= '</div>';
    	
    	return $output;
    }
    
    function has_config() {return true;}    
}