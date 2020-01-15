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

namespace local_remote_backup_provider\output\course_list;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base {
    /**
     * Renders the list of courses.
     * 
     * @return string Container with form or empty string.
     */
    public function render_course_list(renderable $renderable, array $courses, string $remote_url) {
        
        
        $rendered = $renderable->render($remote_url, $courses);

        $output = \html_writer::div(\html_writer::tag('b', get_string('source_remote', 'local_remote_backup_provider') . ': ') . $remote_url);

        if ($rendered === null) {
            $output .= \html_writer::div(\html_writer::tag('i', get_string('no_courses_found', 'local_remote_backup_provider')) . '.', "", ['style' => 'margin-bottom: 20px']);
        } else {
            $button_url = new \moodle_url('/local/remote_backup_provider/process.php');
            
            $button = new \single_button($button_url, get_string('button_import', 'local_remote_backup_provider'), 'POST', true);
            $button->formid = 'remote_form';
            $button->disabled = true;

            $output .= $rendered . $this->render($button)
                    . \html_writer::script('', new \moodle_url('/local/remote_backup_provider/js/course_list.js'));
        }

        return $output;
    }
}