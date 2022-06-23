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
 * Legacy support for subplugin definition (MOODLE < 3.6).
 *
 * @package   local_remote_backup_provider
 * @copyright 2022 Masaryk University
 * @author    Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/remote_backup_provider/classes/plugininfo/remotebppost.php');

class plugininfo_remotebppost extends \local_remote_backup_provider\plugininfo\remotebppost {
}