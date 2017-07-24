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
 * Social breadth indicator - data.
 *
 * @package   mod_data
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_data\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Social breadth indicator - data.
 *
 * @package   mod_data
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class social_breadth extends activity_base {

    /**
     * get_name
     *
     * @return string
     */
    public static function get_name() {
        return get_string('indicator:socialbreadthdata', 'mod_data');
    }

    /**
     * get_indicator_type
     *
     * @return string
     */
    protected function get_indicator_type() {
        return self::INDICATOR_SOCIAL;
    }

    /**
     * get_social_breadth_level
     *
     * @param \cm_info $cm
     * @return int
     */
    protected function get_social_breadth_level(\cm_info $cm) {
        return 1;
    }
}
