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
 * Version file for the mod_newassignment remover.
 *
 * @package    local_newassignment_remover
 * @copyright  2022 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_newassignment_remover';
$plugin->version = 2022071300;
$plugin->requires = 2020061500; // MOODLE 3.9 (LTS)
$plugin->maturity	= MATURITY_BETA;
$plugin->release = 'v3.9-r0.3';
$plugin->dependencies = [
    'mod_assign' => ANY_VERSION,
    'mod_newassignment' => ANY_VERSION,
];
