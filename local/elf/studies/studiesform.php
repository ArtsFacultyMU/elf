<?php
require_once("$CFG->libdir/formslib.php");

class local_elf_studies_form extends moodleform {
	//Add elements to form
	
	protected $_period;
	protected $_year;

	public function __construct($period, $year) {
		$this->_period = $period;
		$this->_year = $year;
		parent::moodleform();
	}
	
	function definition() {
		global $CFG, $DB;

		$mform =& $this->_form; // Don't forget the underscore!
		
		$mform->addElement('header', 'suediessettingsheader', get_string('studiessettingsheader','local_elf').' '.get_string('period_'.$this->_period,'local_elf').' '.$this->_year);
		
		$mform->addElement('hidden','period',$this->_period);
		$mform->addElement('hidden','year',$this->_year);
		
		$mform->addElement('text', 'faculty_phil', get_string('faculty_phil','local_elf')); // Add elements to your form
		$mform->setType('faculty_phil', PARAM_INT);                   //Set type of element
		$mform->addRule('faculty_phil', get_string('required'), 'required');
		$mform->addRule('faculty_phil', get_string('required'), 'nonzero');
		
		$mform->addElement('text', 'faculty_med', get_string('faculty_med','local_elf')); // Add elements to your form
		$mform->setType('faculty_med', PARAM_INT);                   //Set type of element
		$mform->addRule('faculty_med', get_string('required'), 'required');
		$mform->addRule('faculty_med', get_string('required'), 'nonzero');
		
		$mform->addElement('text', 'faculty_law', get_string('faculty_law','local_elf')); // Add elements to your form
		$mform->setType('faculty_law', PARAM_INT);                   //Set type of element
		$mform->addRule('faculty_law', get_string('required'), 'required');
		$mform->addRule('faculty_law', get_string('required'), 'nonzero');
		
		$mform->addElement('text', 'faculty_fss', get_string('faculty_fss','local_elf')); // Add elements to your form
		$mform->setType('faculty_fss', PARAM_INT);                   //Set type of element
		$mform->addRule('faculty_fss', get_string('required'), 'required');
		$mform->addRule('faculty_fss', get_string('required'), 'nonzero');
		
		$mform->addElement('text', 'faculty_sci', get_string('faculty_sci','local_elf')); // Add elements to your form
		$mform->setType('faculty_sci', PARAM_INT);                   //Set type of element
		$mform->addRule('faculty_sci', get_string('required'), 'required');
		$mform->addRule('faculty_sci', get_string('required'), 'nonzero');
		
		$mform->addElement('text', 'faculty_fi', get_string('faculty_fi','local_elf')); // Add elements to your form
		$mform->setType('faculty_fi', PARAM_INT);                   //Set type of element
		$mform->addRule('faculty_fi', get_string('required'), 'required');
		$mform->addRule('faculty_fi', get_string('required'), 'nonzero');
		
		$mform->addElement('text', 'faculty_ped', get_string('faculty_ped','local_elf')); // Add elements to your form
		$mform->setType('faculty_ped', PARAM_INT);                   //Set type of element
		$mform->addRule('faculty_ped', get_string('required'), 'required');
		$mform->addRule('faculty_ped', get_string('required'), 'nonzero');
		
		$mform->addElement('text', 'faculty_econ', get_string('faculty_econ','local_elf')); // Add elements to your form
		$mform->setType('faculty_econ', PARAM_INT);                   //Set type of element
		$mform->addRule('faculty_econ', get_string('required'), 'required');
		$mform->addRule('faculty_econ', get_string('required'), 'nonzero');
		
		$mform->addElement('text', 'faculty_fsps', get_string('faculty_fsps','local_elf')); // Add elements to your form
		$mform->setType('faculty_fsps', PARAM_INT);                   //Set type of element
		$mform->addRule('faculty_fsps', get_string('required'), 'required');
		$mform->addRule('faculty_fsps', get_string('required'), 'nonzero');
		
		$mform->addElement('text', 'cus', get_string('cus','local_elf')); // Add elements to your form
		$mform->setType('cus', PARAM_INT);                   //Set type of element
		$mform->addRule('cus', get_string('required'), 'required');
		$mform->addRule('cus', get_string('required'), 'nonzero');
		
		$this->add_action_buttons(false,get_string('save','local_elf'));
		
		if($studies = $DB->get_records('is_semesters', array('period' => $this->_period, 'year'=>$this->_year))) {
			foreach($studies as $study) {
				switch($study->faculty) {
					case ELF_FACULTY_ECON:
						$mform->setDefault('faculty_econ',$study->code);
						break;
					case ELF_FACULTY_FI:
						$mform->setDefault('faculty_fi',$study->code);
						break;
					case ELF_FACULTY_FSPS:
						$mform->setDefault('faculty_fsps',$study->code);
						break;
					case ELF_FACULTY_FSS:
						$mform->setDefault('faculty_fss',$study->code);
						break;
					case ELF_FACULTY_LAW:
						$mform->setDefault('faculty_law',$study->code);
						break;
					case ELF_FACULTY_MED:
						$mform->setDefault('faculty_med',$study->code);
						break;
					case ELF_FACULTY_PED:
						$mform->setDefault('faculty_ped',$study->code);
						break;
					case ELF_FACULTY_PHIL:
						$mform->setDefault('faculty_phil',$study->code);
						break;
					case ELF_FACULTY_SCI:
						$mform->setDefault('faculty_sci',$study->code);
						break;
					case ELF_CUS:
						$mform->setDefault('cus',$study->code);
						break;
				}
			}
		}
	}
}
