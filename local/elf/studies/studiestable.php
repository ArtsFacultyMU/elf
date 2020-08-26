<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/local/elf/elflib.php');

class local_elf_studiestable extends table_sql {
	
	protected $_period;
	protected $_year;
	
	protected $_faculties;
	
	public function __construct($period, $year) {
		parent::__construct('local_elf_studies');
		
		$this->_period = $period;
		$this->_year = $year;
		$this->_faculties = get_faculties();
		
		$this->define_baseurl(new moodle_url('/local/elf/studies/index.php',array('period' => $this->_period, 'year' => $this->_year)));
		
		$this->prepare_sql();
		$this->construct_headers();
		
		$this->setup();
	}
	
	public function get_row_class($row) {
		return 'semester'.$row->id;
	}
	
	public function col_id(stdClass $row) {
		return $row->code;
	}
	
	public function col_faculty(stdClass $row) {
		return $this->_faculties[$row->faculty];
	}
	
	protected function prepare_sql() {
		$params = array(
				'period' => $this->_period,
				'year' => $this->_year);
		$fields = 's.id, s.faculty, s.code';
		$from = '{is_semesters} s';
		$where = 's.period = :period AND s.year = :year';
		$this->set_sql($fields, $from, $where, $params);
	}
	
	protected function construct_headers() {
		$columns = array();
		$headers = array();
		
		$columns[] = 'id';
		$headers[] = get_string('semesterid','local_elf');
		
		$columns[] = 'faculty';
		$headers[] = get_string('faculty','local_elf');
		
		
		$this->define_columns($columns);
		$this->define_headers($headers);
	}
	
	
	
}