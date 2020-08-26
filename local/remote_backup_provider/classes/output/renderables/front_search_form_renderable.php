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

namespace local_remote_backup_provider\output\renderables;

use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/outputcomponents.php');


/**
 * Tab view of the available remotes.
 *
 * @package   local_remote_backup_provider
 * @copyright 2020 Masaryk University
 * @author    Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class front_search_form_renderable implements \renderable, \templatable {
    /**
     * Target of the form.
     * 
     * @var string
     */
    protected $action = null;

    /**
     * Search value.
     * 
     * @var string
     */
    protected $value = null;

    /**
     * Changes the form action (e.g. target).
     * 
     * @param string $action New action.
     */
    public function setAction(string $action) {
        $this->action = $action;
    }

    /**
     * Changes the search value.
     * 
     * @param string $value New search value.
     */
    public function setValue(string $value) {
        $this->value = $value;
    }

    public function export_for_template(\renderer_base $output) {
        $output = new stdClass();

        $output->action = $this->action;
        $output->value = $this->value;

        return $output;
    }
}