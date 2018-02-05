<?php
class newassign_grade_rubric {
	
	protected $controller;
	protected $assignment;
	
	public function __construct($controller, $assignment) {
		$this->controller = $controller;
		$this->assignment = $assignment;
	}
	
	public function display_grade($gradeid) {
		global $PAGE;
		$criteria = $this->controller->get_definition()->rubric_criteria;
		if (has_capability('mod/newassignment:grade', $this->assignment->get_context())) 
			$mode = gradingform_rubric_controller::DISPLAY_REVIEW;
		else 
			$mode = gradingform_rubric_controller::DISPLAY_VIEW;
		$options = $this->controller->get_options();
		$value = $this->get_rubric_filling($gradeid);
		return $this->controller->get_renderer($PAGE)->display_rubric($criteria, $options, $mode, null, $value);
	}
	
	protected function get_rubric_filling($gradeid) {
		global $DB;
        $records = $DB->get_records('newassign_rubric_fillings', array('gradeid' => $gradeid));
        $fillings = array('criteria' => array());
        foreach ($records as $record) {
            $fillings['criteria'][$record->criterionid] = (array)$record;
        }
        return $fillings;
	}
	
	public function update_grade($data, $gradeid) {
		global $DB;
        $currentgrade = $this->get_rubric_filling($gradeid);
        foreach ($data['criteria'] as $criterionid => $record) {
            if (!array_key_exists($criterionid, $currentgrade['criteria'])) {
                $newrecord = array('gradeid' => $gradeid, 'criterionid' => $criterionid,
                    'levelid' => $record['levelid'], 'remarkformat' => FORMAT_MOODLE);
                if (isset($record['remark'])) {
                    $newrecord['remark'] = $record['remark'];
                }
                $DB->insert_record('newassign_rubric_fillings', $newrecord);
            } else {
                $updaterecord = array('id' => $currentgrade['criteria'][$criterionid]['id']);
                foreach (array('levelid', 'remark'/*, 'remarkformat' TODO */) as $key) {
                    if (isset($record[$key]) && $currentgrade['criteria'][$criterionid][$key] != $record[$key]) {
                        $updaterecord[$key] = $record[$key];
                    }
                }
                if (count($updaterecord) > 1) {
                    $DB->update_record('newassign_rubric_fillings', $updaterecord);
                }
            }
        }
        foreach ($currentgrade['criteria'] as $criterionid => $record) {
            if (!array_key_exists($criterionid, $data['criteria'])) {
                $DB->delete_records('newassign_rubric_fillings', array('id' => $record['id']));
            }
        }
	}
}