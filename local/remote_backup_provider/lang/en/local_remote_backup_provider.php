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

$string['search_all'] = 'Search all courses';
$string['transfer_as'] = 'Transfer as:';
$string['user_not_found'] = 'User wasn\'t found.';
$string['issued_by_other_user'] = 'Transfer was issued by other user.';
$string['course_id'] = 'Course ID';
$string['issued_by'] = 'issued by';

$string['import'] = 'Import from remote';
$string['pluginname'] = 'Remote backup provider';
$string['privacy:metadata'] = 'The Remote backup provider plugin does not store any personal data.';
$string['remotesite'] = 'Remote site';
$string['remotesite_desc'] = 'The fully-qualified domain of the remote site';
$string['wstoken'] = 'Web service token';
$string['wstoken_desc'] = 'Add the web service token from the remote site.';

$string['remote_backup_provider:access'] = 'Course transfer – basic access';
$string['remote_backup_provider:searchall'] = 'Search all courses on remote';
$string['remote_backup_provider:transferasother'] = 'Transfer as different person';
$string['remote_backup_provider:multitransfer'] = 'Override one issue per course limitation';
$string['remote_backup_provider:managetransfers'] = 'Manage transfers';

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
$string['remote_course'] = 'Course on remote';

$string['back_to_selection'] = 'Back to selection';
$string['continue_to_course'] = 'Continue to transfered course';
$string['courses_issued_for_transfer'] = 'Courses are issued to transfer';
$string['courses_issued_for_transfer_error'] = 'Following courses were already issued earlier therefore skipped:';
$string['courses_issued_for_transfer_error'] = '<p>The following courses cannot be issued to transfer as they were issued to transfer to this ELF version in the past:</p>
{$a->errors}
<p>If you need a new version of the course within current ELF instalation, you can create its copy (read the <a href="{$a->link}">instructions</a>).</p>';

$string['remote_set_as_hidden'] = 'Remote was successfully hidden.';
$string['remote_set_as_visible'] = 'Remote was successfully shown.';
$string['remote_already_hidden'] = 'Remote was already hidden.';
$string['remote_already_visible'] = 'Remote was already visible.';

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
$string['course_not_found'] = 'Course not found.';

$string['admin_general_settings'] = "General settings";
$string['admin_remote_list'] = 'List of remotes';
$string['admin_remote_edit'] = 'Edit remote';
$string['admin_remote_add'] = 'Add remote';
$string['admin_transfer_log'] = 'Transfer log';
$string['admin_detailed_log'] = 'Detailed transfer log';
$string['admin_manual_cancel'] = 'Cancel transfer manually';
$string['admin_manual_finish'] = 'Finish transfer manually';

$string['remote_name'] = 'Name';
$string['remote_url'] = 'URL';
$string['remote_token'] = 'Token';
$string['remote_active'] = 'Active';
$string['remote_position'] = 'Position';

$string['task_maximum_transfer_time'] = 'Maximum transfer time';
$string['task_maximum_transfer_time_description'] = 'After a given time the transfer will be automatically canceled. Zero means no limit.';

$string['hide'] = 'Hide';
$string['show'] = 'Show';
$string['move_up'] = 'Move up';
$string['move_down'] = 'Move down';

$string['remote_not_found'] = 'Remote wasn\'t found.';
$string['remote_added'] = 'Remote was successfully added.';
$string['remote_updated'] = 'Remote was successfully updated.';
$string['transfer_not_found'] = 'Transfer wasn\'t found.';
$string['transfer_already_canceled'] = 'Transfer was already canceled.';
$string['transfer_already_finished'] = 'Transfer was already finished.';
$string['transfer_manualcancel_areyousure'] = 'Are you sure to cancel the following course?';
$string['transfer_canceled_successfully'] = 'Transfer was successfully canceled.';
$string['transfer_finished_successfully'] = 'Transfer was successfully finished.';

$string['transfer_as_self'] = 'Import as myself';
$string['transfer_as_other'] = 'Import as other user';

$string['transfer_status_added'] = 'Issued';
$string['transfer_status_error'] = 'Error';
$string['transfer_status_processing'] = 'Processing';
$string['transfer_status_finished'] = 'Finished';
$string['transfer_status_canceled'] = 'Canceled';

$string['transfer_fullstatus_added'] = 'Added.';
$string['transfer_fullstatus_conf_noremote'] = 'Configuration error: No remote address.';
$string['transfer_fullstatus_conf_notoken'] = 'Configuration error: No remote token.';
$string['transfer_fullstatus_backup_started'] = 'Remote backup started.';
$string['transfer_fullstatus_backup_usernotfound'] = 'Remote backup: User not found.';
$string['transfer_fullstatus_backup_invalidhttpcode'] = 'Remote backup wrong HTTP code.';
$string['transfer_fullstatus_backup_invalidurlstart'] = 'Remote backup URL not starting with remote address.';
$string['transfer_fullstatus_backup_ended'] = 'Remote backup ended successfully.';
$string['transfer_fullstatus_transfer_started'] = 'Transfering backup started.';
$string['transfer_fullstatus_transfer_missingurl'] = 'Transfering backup failed on missing remote backup URL.';
$string['transfer_fullstatus_transfer_failedfilecreation'] = 'Transfering backup failed on creating file.';
$string['transfer_fullstatus_transfer_ended'] = 'Transfering backup ended successfully.';
$string['transfer_fullstatus_restore_started'] = 'Restoration started.';
$string['transfer_fullstatus_restore_invalidfile'] = 'Restoration file invalid.';
$string['transfer_fullstatus_restore_prechecksfailed'] = 'Restoration prechecks failed.';
$string['transfer_fullstatus_restore_existingcourse'] = 'Found local course for restore. Data will be replaced.';
$string['transfer_fullstatus_restore_newcourse'] = 'No local course found for restore. It will be created.';
$string['transfer_fullstatus_restore_newcoursefinished'] = 'Empty course for restore was created.';
$string['transfer_fullstatus_restore_itself'] = 'Restoration itself started.';
$string['transfer_fullstatus_restore_ended'] = 'Restoration ended successfully.';
$string['transfer_fullstatus_teacherenrol_started'] = 'Enroling teacher to the course.';
$string['transfer_fullstatus_teacherenrol_ended'] = 'Teacher enroled successfully.';
$string['transfer_fullstatus_categorization_started'] = 'Categorizing the course.';
$string['transfer_fullstatus_categorization_gettingremotecatid'] = 'Getting category id from remote.';
$string['transfer_fullstatus_categorization_ended'] = 'Categorization finished successfully.';
$string['transfer_fullstatus_categorization_lookingforlocalcat'] = 'Looking for corresponding local category.';
$string['transfer_fullstatus_categorization_catfound'] = 'Category found.';
$string['transfer_fullstatus_categorization_remotenotfoundlocally'] = 'Remote category not found locally, creating.';
$string['transfer_fullstatus_categorization_lookingforparent'] = 'Looking for parent category.';
$string['transfer_fullstatus_categorization_creatingnewcat'] = 'Creating a new local category.';
$string['transfer_fullstatus_categorization_savingforlater'] = 'Saving link to newly created category for later use in transfers.';
$string['transfer_fullstatus_cancelled_timeout'] = 'Transfer canceled on timeout.';
$string['transfer_fullstatus_cancelled_manually'] = 'Transfer canceled manually.';
$string['transfer_fullstatus_finished_manually'] = 'Transfer finished manually.';