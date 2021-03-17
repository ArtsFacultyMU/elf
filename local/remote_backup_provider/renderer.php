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
    public function render_admin_remote_list(array $remotes) {
        global $CFG;

        require_once($CFG->libdir . '/tablelib.php');

        $table = new \flexible_table('local_remote_backup_provider__remote_list');
        $table->define_baseurl(new \moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_remote_list']));
        $table->define_columns(['name', 'address', 'edit', /*'position'*/]);
        $table->define_headers([
            get_string('remote_name', 'local_remote_backup_provider'),
            get_string('remote_url', 'local_remote_backup_provider'),
            get_string('actions', 'local_remote_backup_provider'),
        ]);
        $table->set_attribute('class', 'admintable generaltable');
        $table->setup();

        foreach ($remotes as $key => $remote) {    
            $row = [];
            $class = '';
        
            $row[] = $remote->name;
            $row[] = \html_writer::link($remote->address, $remote->address);
        
            $actions = [];
            if ($remote->active) {
                $actions[] = \html_writer::link(
                    new \moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_remote_hide', 'remote' => $remote->id]), 
                    $this->pix_icon('i/show', get_string('hide', 'local_remote_backup_provider'))
                );
            } else {
                $actions[] = \html_writer::link(
                    new \moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_remote_show', 'remote' => $remote->id]), 
                    $this->pix_icon('i/hide', get_string('show', 'local_remote_backup_provider'))
                );
                $class = 'dimmed_text';
            }
            $actions[] = \html_writer::link(
                new \moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_remote_edit', 'remote' => $remote->id]), 
                $this->pix_icon('t/edit', get_string('edit'))
            );
            $row[] = implode(' ', $actions);
        
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

    public function render_front_transfer_list(int $remote_id, array $transfers) {
        global $CFG;

        require_once($CFG->libdir . '/tablelib.php');
        require_once($CFG->libdir . '/moodlelib.php');
        
        $table = new \flexible_table('local_remote_backup_provider__transfer_list');
        $table->define_baseurl(new moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'status', 'remote' => $remote_id]));
        $table->define_columns(['name', 'added', 'status', 'actions']);
        $table->define_headers([
            get_string('full_course_name', 'local_remote_backup_provider'),
            get_string('time_created', 'local_remote_backup_provider'),
            get_string('status', 'local_remote_backup_provider'),
            get_string('actions', 'local_remote_backup_provider'),
        ]);
        $table->set_attribute('class', 'admintable generaltable');
        $table->setup();

        foreach ($transfers as $transfer) {    
            $row = [];
        
            $row[] = $transfer->remotecoursename . (($transfer->userid != $transfer->issuer) ? ' ' . $this->pix_icon('i/user', get_string('issued_by_other_user', 'local_remote_backup_provider')) : '');
            $row[] = userdate($transfer->timecreated);
            $row[] = '<span class="badge badge-' . \local_remote_backup_provider\helper\transfer_manager::LABEL_FOR_STATUS[$transfer->status] . '">'
                    . get_string('transfer_status_' . $transfer->status, 'local_remote_backup_provider')
                    . '</span> ' 
                    . $this->pix_icon('i/scheduled', userdate($transfer->timemodified));
            
            $actions = [];
            if (has_capability('local/remote_backup_provider:managetransfers',\context_system::instance())) {
                $actions[] = \html_writer::link(
                    new \moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_detailed_log', 'id' => $transfer->id]),
                    $this->pix_icon('i/info', get_string('admin_detailed_log', 'local_remote_backup_provider'))
                );
            }
            if ($transfer->courseid !== null
                    && $transfer->status === \local_remote_backup_provider\helper\transfer_manager::STATUS_FINISHED) {
                $actions[] = \html_writer::link(
                        new \moodle_url('/course/view.php', ['id' => $transfer->courseid]),
                        $this->pix_icon('t/right', get_string('continue_to_course', 'local_remote_backup_provider'))
                );
            }
            $row[] = implode(' ', $actions);
        
            $table->add_data($row);
        }
        return $table->finish_output();
    }

    public function render_admin_transfer_log(array $transfers, int $remote) {
        global $CFG, $DB;

        require_once($CFG->libdir . '/tablelib.php');
        require_once($CFG->libdir . '/moodlelib.php');

        $table = new \flexible_table('local_remote_backup_provider__transfer_list');
        $table->define_baseurl(new moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_transfer_log', 'remote' => $remote]));
        $table->define_columns(['name', 'added', 'issuer', 'status', 'actions']);
        $table->define_headers([
            get_string('full_course_name', 'local_remote_backup_provider'),
            get_string('time_created', 'local_remote_backup_provider'),
            get_string('issuer', 'local_remote_backup_provider'),
            get_string('status', 'local_remote_backup_provider'),
            get_string('actions', 'local_remote_backup_provider'),
        ]);
        $table->set_attribute('class', 'admintable generaltable');
        $table->setup();

        $issuers = [];

        foreach ($transfers as $transfer) {    
            $row = [];
        
            $row[] = $transfer->remotecoursename . (($transfer->userid != $transfer->issuer) ? ' ' . $this->pix_icon('i/user', get_string('issued_by_other_user', 'local_remote_backup_provider')) : '');
            $row[] = userdate($transfer->timecreated);
            
            if (!isset($issuers[$transfer->userid])) {
                $user = $DB->get_record('user', ['id' => $transfer->userid], 'id, firstname, lastname');
                $issuers[$user->id] = mb_substr($user->firstname, 0, 1, "UTF-8") . '. ' . $user->lastname;
            }
            if (!isset($issuers[$transfer->issuer])) {
                $user = $DB->get_record('user', ['id' => $transfer->issuer], 'id, firstname, lastname');
                $issuers[$user->id] = mb_substr($user->firstname, 0, 1, "UTF-8") . '. ' . $user->lastname;
            }
            $row[] = $issuers[$transfer->userid]. (($transfer->userid != $transfer->issuer) ? '<br />(' . $issuers[$transfer->issuer] . ')' : '');

            $row[] = '<span class="badge badge-' . \local_remote_backup_provider\helper\transfer_manager::LABEL_FOR_STATUS[$transfer->status] . '">'
                    . get_string('transfer_status_' . $transfer->status, 'local_remote_backup_provider')
                    . '</span> ' 
                    . $this->pix_icon('i/scheduled', userdate($transfer->timemodified));
            
            $actions = [];
            $actions[] = \html_writer::link(
                new \moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_detailed_log', 'id' => $transfer->id]),
                $this->pix_icon('i/info', get_string('admin_detailed_log', 'local_remote_backup_provider'))
            );


            if (
                $transfer->status !== \local_remote_backup_provider\helper\transfer_manager::STATUS_CANCELED
                && $transfer->status !== \local_remote_backup_provider\helper\transfer_manager::STATUS_FINISHED
            ) {
                $actions[] = \html_writer::link(
                    new \moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_manual_cancel', 'id' => $transfer->id]),
                    $this->pix_icon('t/block', get_string('admin_manual_cancel', 'local_remote_backup_provider'))
                );

                $actions[] = \html_writer::link(
                    new \moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_manual_finish', 'id' => $transfer->id]),
                    $this->pix_icon('t/check', get_string('admin_manual_finish', 'local_remote_backup_provider'))
                );
            }

            if ($transfer->courseid !== null) {
                $actions[] = \html_writer::link(
                        new \moodle_url('/course/view.php', ['id' => $transfer->courseid]),
                        $this->pix_icon('t/right', get_string('continue_to_course', 'local_remote_backup_provider'))
                );
            }
                    $row[] = implode(' ', $actions);
        
            $table->add_data($row);
        }
        return $table->finish_output();
    }

    public function render_admin_detailed_log(int $id, array $logs) {
        global $CFG, $DB;

        require_once($CFG->libdir . '/tablelib.php');
        require_once($CFG->libdir . '/moodlelib.php');
        $table = new \flexible_table('local_remote_backup_provider__transfer_log');
        $table->define_baseurl(new moodle_url('/local/remote_backup_provider/index.php', ['section'=> 'admin_detailed_log', 'id' => $id]));
        $table->define_columns(['timestamp', 'status', 'notes']);
        $table->define_headers([
            get_string('timestamp', 'local_remote_backup_provider'),
            get_string('status', 'local_remote_backup_provider'),
            get_string('notes', 'local_remote_backup_provider'),
        ]);
        $table->set_attribute('class', 'admintable generaltable');
        $table->setup();

        foreach ($logs as $log) {
            $row = [];
            $row[] = userdate($log->timemodified);
            $row[] = '<span class="badge badge-' . \local_remote_backup_provider\helper\transfer_manager::LABEL_FOR_STATUS[$log->status] . '">'
                    . get_string('transfer_fullstatus_' . $log->fullstatus, 'local_remote_backup_provider')
                    . '</span> ';
            $row[] = $log->notes;
            $table->add_data($row);
        }
        
        return $table->finish_output();
    }
}