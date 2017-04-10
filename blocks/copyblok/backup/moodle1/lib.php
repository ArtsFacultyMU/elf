<?php
defined('MOODLE_INTERNAL') || die();

class moodle1_block_copyblok_handler extends moodle1_block_handler {
	
	public function get_paths() {
		return array(
				new convert_path(
					'copyblok', '/MOODLE_BACKUP/COURSE/BLOCKS/BLOCK/copyblok',
					array(
            			'renamefields' => array(
            					'name' => 'blockname',
            					'pagetype' => 'pagetypepattern',
            					'weight' => 'defaultweight',
            					'pageid' => 'parentcontextid'
            			),
            			'newfields' => array(
            					'block_positions' => ' ',
            					'subpagepattern' => '$@NULL@$',
            					'showinsubcontexts' => '0',
            					'defaultregion' => 'side-pre'
            			),
            			'dropfields' => array(
            					'role_overrides', 'role_assignments', 'visible', 'position'
            			),
            		)
				)
			);
	}
	
	public function process_copyblok($data) {
		global $DB;
		//var_dump($data);
		$block			= $DB->get_record('block', array('name' => $data['blockname']));
		$coursec		= $this->converter->get_contextid(CONTEXT_COURSE, $data['parentcontextid']);
		$contextid      = $this->converter->get_contextid(CONTEXT_BLOCK, $block->id);
		
		// start writing nanogong.xml
		$this->open_xml_writer("course/blocks/copyblok_{$block->id}/block.xml");
		$this->xmlwriter->begin_tag('block', array('id' => $block->id, 'version' => $block->version,
				'contextid' => $contextid));
		$data['parentcontextid'] = $coursec;
		$data['pagetypepattern'] = "course-*";
		
		foreach ($data as $field => $value) {
			if ($field <> 'id') {
				$this->xmlwriter->full_tag($field, $value);
			}
		}
		
		$this->xmlwriter->end_tag('block');
		$this->close_xml_writer();
		
		$this->open_xml_writer("course/blocks/copyblok_{$block->id}/roles.xml");
		$this->xmlwriter->begin_tag('roles');
		$this->xmlwriter->full_tag('role_overrides',' ');
		$this->xmlwriter->full_tag('role_assignments',' ');
		$this->xmlwriter->end_tag('roles');
		$this->close_xml_writer();
		
		$this->open_xml_writer("course/blocks/copyblok_{$block->id}/inforef.xml");
		$this->xmlwriter->full_tag('inforef', ' ');
		$this->close_xml_writer();
		
		return $data;
	}
	
}