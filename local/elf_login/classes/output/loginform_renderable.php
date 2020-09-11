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
 * Way to render login form specific to ELF.
 *
 * @package    local_elf_login
 * @copyright  2020 Masaryk University
 * @author     Vojtěch Mrkývka <vojtech.mrkyvka@gmail.com>
 * 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_elf_login\output; 

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../config.php');                                                               

use renderer_base;
use context_course;

class loginform_renderable extends \core_auth\output\login {
    const LINK_IS_MUNI_FRONTPAGE = 'https://is.muni.cz';
    const LINK_IS_MUNI_PASSCHANGE = 'https://is.muni.cz/podpora/obnova_pristupu?akce=login';

    const PATH_SHIBBOLETH = '/auth/shibboleth/index.php';

    public function export_for_template(renderer_base $output) {
        // $OUTPUT should not be used here, but rewriting
        // the whole login render code is IMO much, much worse.
        global $OUTPUT, $SITE, $USER; 

        $data = parent::export_for_template($output);

        // Override because rendering is not supported in template yet.
        // (reused from boost/core_renderer/render_login & changed accordingly)
        $data->cookieshelpiconformatted = $OUTPUT->help_icon('cookiesenabled');
        $data->errorformatted = $OUTPUT->error_text($data->error);
        $url = $OUTPUT->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $data->logourl = $url;
        $data->sitename = format_string($SITE->fullname, true,
            ['context' => context_course::instance(SITEID), "escape" => false]);


        // ELF-specific variables for MUNI login.
        $data->shibboleth_path = new \moodle_url(self::PATH_SHIBBOLETH);
        $data->muni_is_front = self::LINK_IS_MUNI_FRONTPAGE . '?lang=en';
        $data->muni_is_pass = self::LINK_IS_MUNI_PASSCHANGE . ';lang=en';
        
        if (current_language() == 'cs') {
            $data->muni_is_front = self::LINK_IS_MUNI_FRONTPAGE . '?lang=cs';
            $data->muni_is_pass = self::LINK_IS_MUNI_PASSCHANGE . ';lang=cs';
        }
        return $data;
    }
}