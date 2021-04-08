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
class front_remote_tabs_renderable implements \renderable, \templatable {
    /**
     * List of all available remotes.
     * 
     * @var array Array of stdClasses.
     */
    protected $remotes = [];

    /**
     * Currently chosen remote.
     * 
     * @var int
     */
    protected $remote = null;

    /**
     * Base url for the remote switcher.
     * 
     * @var \moodle_url
     */
    protected $url = null;

    /**
     * Alters the list of available remotes.
     * 
     * @param array $remotes List of stdClasses.
     */
    public function setRemotes(array $remotes) {
        $this->remotes = $remotes;
    }

    /**
     * Changes current remote to a new value.
     * 
     * @param int $remote New remote ID.
     */
    public function setRemote(int $remote) {
        $this->remote = $remote;
    }

    /**
     * Sets the base url for the tabs.
     * 
     * @param \moodle_url Url.
     */
    public function setUrl(\moodle_url $url) {
        $this->url = $url;
    }

    public function export_for_template(\renderer_base $output) {
        // Iterate over remotes.
        $remotes = array_values($this->remotes);
        foreach ($remotes as $remote) {
            // Build an URL for the remote.
            $remote_url = clone $this->url;
            $remote_url->param('remote', $remote->id);
            $remote->url = $remote_url;

            // Add "selected" atribute if it is the currently selected remote.
            if ($remote->id == $this->remote) {
                $remote->selected = true;
            }
        }
        
        // Create and return the output.
        $output = new stdClass();
        $output->remotes = $remotes;
        $output->remote = $this->remote;
        return $output;
    }
}