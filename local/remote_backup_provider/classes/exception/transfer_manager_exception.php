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

namespace local_remote_backup_provider\exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Manages exceptions linked to transfer manager
 *
 * @package    local_remote_backup_provider
 * @copyright  2019 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class transfer_manager_exception extends \moodle_exception {
    const CODE_RESTORE_INVALID_BACKUP_FILE = 'exception_tm_restore_error_invalid_backup_file';
    const CODE_RESTORE_PRECHECK_FAILED = 'exception_tm_restore_error_precheck_failed';
    const CODE_RECORD_DOES_NOT_EXIST = 'exception_tm_record_does_not_exist';
    const CODE_PREFLIGHT_FAILED = 'exception_tm_preflight_failed';

    /**
     * {@inheritdoc}
     */
    public function __construct($errorcode, $link='', $a=NULL, $debuginfo=null) {
        return parent::__construct($errorcode, 'local_remote_backup_provider', $link, $a, $debuginfo);
    }
}