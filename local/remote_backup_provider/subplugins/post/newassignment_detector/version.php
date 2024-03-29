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
 * @package    remotebppost_newassignment_detector
 * @copyright  2023 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'remotebppost_newassignment_detector';
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = 'v0.0.1';
$plugin->requires  = 2018051700; // MOODLE 3.5 (LTS).
$plugin->version   = 2022040502;