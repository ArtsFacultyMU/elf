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
 * Subplugin version information.
 *
 * @package    remotebppost_glossary_datatransfer
 * @copyright  2022 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'remotebppost_glossary_datatransfer';
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = 'v0.0.2';
$plugin->requires  = 2018051700; // MOODLE 3.5
$plugin->version   = 2022080800;
$plugin->dependencies = [
  'mod_glossary' => ANY_VERSION,
];