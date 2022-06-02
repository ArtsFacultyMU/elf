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
 * Manages exceptions linked to configuration.
 *
 * @package    local_remote_backup_provider
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class configuration_exception extends \moodle_exception {
    const CODE_NO_REMOTE = 'no_remote';
    const CODE_NO_TOKEN = 'no_token';
    const CODE_NO_ADDRESS = 'no_address';

    /**
     * {@inheritdoc}
     */
    public function __construct($errorcode, $link='', $a=NULL, $debuginfo=null) {
        return parent::__construct($errorcode, 'local_remote_backup_provider', $link, $a, $debuginfo);
    }
}