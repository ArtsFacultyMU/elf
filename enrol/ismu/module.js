M.enrol_ismu = M.enrol_ismu || {};

M.enrol_ismu.init = function(Y) {
	Y.use('node', function(Y) {
		if((/^\s*$/).test(Y.one('#id_enrol_ismu_course_codes').get("value")))
			Y.one("#id_enrol_ismu_enrol_status").set("disabled","disabled");
		Y.one('#id_enrol_ismu_course_codes').on('keyup',function(e) {
			if(!(/^\s*$/).test(Y.one('#id_enrol_ismu_course_codes').get("value"))) { 
				Y.one("#id_enrol_ismu_enrol_status").set("disabled","");
			} else {
				Y.one("#id_enrol_ismu_enrol_status").set("disabled","disabled");
				Y.one("#id_enrol_ismu_create_seminars").set("disabled","disabled");
			}
		});
	});
};
