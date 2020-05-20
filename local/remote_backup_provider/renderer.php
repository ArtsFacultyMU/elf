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

defined('MOODLE_INTERNAL') || die();

class local_remote_backup_provider_renderer extends plugin_renderer_base {
    /**
     * Renders the list of courses.
     * 
     * @return string Container with form or empty string.
     */
    public function render_front_course_list(
        local_remote_backup_provider\output\renderables\front_course_list_renderable $renderable
    ) {

        $rendered = $renderable->render();

        if ($renderable->hasCourses()) {
            $button_url = new \moodle_url('/local/remote_backup_provider/index.php', ['section' => 'process', 'remote' => $renderable->getRemote()->id]);
            
            $button = new \single_button($button_url, get_string('button_import', 'local_remote_backup_provider'), 'POST', true);
            $button->formid = $renderable::FORM_ID;
            $button->disabled = true;

            $rendered .= $this->render($button)
                    . \html_writer::script('', new \moodle_url('/local/remote_backup_provider/js/course_list.js'));
        }

        return $rendered;
    }

    public function render_admin_remote_list(array $remotes) {
        $table = new \flexible_table('local_remote_backup_provider__remote_list');
            $table->define_columns(['name', 'address', 'active', /*'position'*/]);
            $table->define_headers([
                get_string('remote_name', 'local_remote_backup_provider'),
                get_string('remote_url', 'local_remote_backup_provider'),
                get_string('remote_active', 'local_remote_backup_provider'),
                // @todo Implement move action
                //get_string('remote_position', 'local_remote_backup_provider'),
            ]);
            $table->set_attribute('class', 'admintable generaltable');
            $table->setup();
    
            foreach ($remotes as $key => $remote) {    
                $row = [];
                $class = '';
            
                $row[] = $remote->name;
                $row[] = \html_writer::link($remote->address, $remote->address);
            
                if ($remote->active) {
                    $row[] = $this->pix_icon('i/hide', get_string('hide', 'local_remote_backup_provider'));
                } else {
                    $row[] = $this->pix_icon('i/show', get_string('show', 'local_remote_backup_provider'));
                    $class = 'dimmed_text';
                }
            
                /* @todo Implement move action
                $position = '';
                reset($remotes);
                if ($key !== key($remotes)) {
                    $position .= $this->pix_icon('i/up', get_string('move_up', 'local_remote_backup_provider'));
                }
                end($remotes);
                if ($key !== key($remotes)) {
                    $position .= $this->pix_icon('i/down', get_string('move_down', 'local_remote_backup_provider'));
                }
                $row[] = $position;
                */
    
                $table->add_data($row, $class);
            }
            return $table->finish_output();
      }
}