<?php
class newassign_grade_guide {
	
	protected $controller;
	protected $assignment;
	
	public function __construct($controller, $assignment) {
		$this->controller = $controller;
		$this->assignment = $assignment;
	}
	
	public function display_grade($gradeid) {
		global $PAGE;
		$criteria = $this->controller->get_definition()->guide_criteria;
		$comments = $this->controller->get_definition()->guide_comment;
		if (has_capability('mod/newassignment:grade', $this->assignment->get_context())) 
			$mode = gradingform_guide_controller::DISPLAY_REVIEW;
		else 
			$mode = gradingform_guide_controller::DISPLAY_VIEW;
		$options = $this->controller->get_options();
		$value = $this->get_guide_filling($gradeid);
		return $this->controller->get_renderer($PAGE)->display_guide($criteria, $comments, $options, $mode,
			null, $value);
	}
	
	protected function get_guide_filling($gradeid) {
		global $DB;
		$records = $DB->get_records('newassign_guide_fillings', array('gradeid' => $gradeid));
		$fillings = array('criteria' => array());
		foreach ($records as $record) {
			$record->score = (float)$record->score; // Strip trailing 0.
			$fillings['criteria'][$record->criterionid] = (array)$record;
		}
		return $fillings;
	}
	
	public function update_grade($data, $gradeid) {
		global $DB;
		$currentgrade = $this->get_guide_filling($gradeid);
		foreach ($data['criteria'] as $criterionid => $record) {
			if (!array_key_exists($criterionid, $currentgrade['criteria'])) {
				$newrecord = array('gradeid' => $gradeid, 'criterionid' => $criterionid,
						'score' => $record['score'], 'remarkformat' => FORMAT_MOODLE);
				if (isset($record['remark'])) {
					$newrecord['remark'] = $record['remark'];
				}
				$DB->insert_record('newassign_guide_fillings', $newrecord);
			} else {
				$newrecord = array('id' => $currentgrade['criteria'][$criterionid]['id']);
				foreach (array('score', 'remark'/*, 'remarkformat' TODO */) as $key) {
					if (isset($record[$key]) && $currentgrade['criteria'][$criterionid][$key] != $record[$key]) {
						$newrecord[$key] = $record[$key];
					}
				}
				if (count($newrecord) > 1) {
					$DB->update_record('newassign_guide_fillings', $newrecord);
				}
			}
		}
		foreach ($currentgrade['criteria'] as $criterionid => $record) {
			if (!array_key_exists($criterionid, $data['criteria'])) {
				$DB->delete_records('newassign_guide_fillings', array('id' => $record['id']));
			}
		}
	}
}