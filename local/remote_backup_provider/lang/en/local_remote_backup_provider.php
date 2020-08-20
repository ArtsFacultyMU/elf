<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language file for local_remote_backup_provider
 *
 * @package    local_remote_backup_provider
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['remove_old_task'] = 'Remove old remote backup files';
$string['create_backup_task'] = 'Create backup on remote';
$string['transfer_backup_task'] = 'Move backup from remote to local instalation';
$string['restore_backup_task'] = 'Restore course from backup';
$string['enrol_teacher_task'] = 'Enrol teacher to the course';
$string['categorize_task'] = 'Categorize the course';

$string['import'] = 'Import from remote';
$string['pluginname'] = 'Remote backup provider';
$string['privacy:metadata'] = 'The Remote backup provider plugin does not store any personal data.';
$string['remotesite'] = 'Remote site';
$string['remotesite_desc'] = 'The fully-qualified domain of the remote site';
$string['wstoken'] = 'Web service token';
$string['wstoken_desc'] = 'Add the web service token from the remote site.';

$string['available_courses_search'] = 'Search available source courses';
$string['available_courses'] = 'Available source courses';
$string['issued_transfers'] = 'Issued transfers';

$string['short_course_name'] = 'Short name';
$string['full_course_name'] = 'Full name';
$string['time_created'] = 'Issued';
$string['status'] = 'Status';
$string['issuer'] = 'Issuer';
$string['actions'] = 'Actions';
$string['no_courses_found'] = 'No courses found';
$string['button_import'] = 'Import';
$string['timestamp'] = 'Timestamp';
$string['notes'] = 'Notes';

$string['back_to_selection'] = 'Back to selection';
$string['continue_to_course'] = 'Continue to transfered course';
$string['courses_issued_for_transfer'] = 'Courses are issued to transfer';

$string['restore_error_invalid_extension'] = 'Restore failed: Invalid file extension.';
$string['exception_tm_restore_error_invalid_backup_file'] = 'Restore failed: Invalid backup file.';
$string['exception_tm_restore_error_precheck_failed'] = 'Restore failed: Precheck failed.';
$string['exception_tm_record_does_not_exist'] = 'Transfer failed: Database record does not exist.';

$string['import_success'] = 'Remote course with ID %s was successfully imported into the course <i><a href="%s" target="_blank">%s</a></i>.';
$string['import_failure'] = 'Remote course with ID %s was not succesfully imported. The following error might be helpful:';

$string['invalid_section'] = 'Invalid section';

$string['no_remote'] = 'No remote found.';
$string['no_token'] = 'Selected remote does not have token filled in.';
$string['no_address'] = 'Selected remote does not have address filled in.';

$string['remote_not_found'] = 'Remote not found.';

$string['admin_remote_list'] = 'List of remotes';
$string['admin_remote_edit'] = 'Edit remote';
$string['admin_remote_add'] = 'Add remote';
$string['admin_transfer_log'] = 'Transfer log';
$string['admin_detailed_log'] = 'Detailed transfer log';


$string['remote_name'] = 'Name';
$string['remote_url'] = 'URL';
$string['remote_token'] = 'Token';
$string['remote_active'] = 'Active';
$string['remote_position'] = 'Position';

$string['hide'] = 'Hide';
$string['show'] = 'Show';
$string['move_up'] = 'Move up';
$string['move_down'] = 'Move down';

$string['remote_not_found'] = 'Remote wasn\'t found.';
$string['remote_added'] = 'Remote was successfully added.';
$string['remote_updated'] = 'Remote was successfully updated.';
$string['transfer_not_found'] = 'Transfer wasn\'t found.';

$string['transfer_status_added'] = 'Issued';
$string['transfer_status_error'] = 'Error';
$string['transfer_status_processing'] = 'Processing';
$string['transfer_status_finished'] = 'Finished';