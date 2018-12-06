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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_bootstrapbase
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('renderers/core_course_renderer.php');

class theme_elf_bs_core_renderer extends theme_bootstrapbase_core_renderer {
    
    public function language_menu() {
        global $CFG;
        $menu = new custom_menu();

        $addlangmenu = true;
        $langs = get_string_manager()->get_list_of_translations();
        if (count($langs) < 2
                or empty($CFG->langmenu)
                or ( $this->page->course != SITEID and ! empty($this->page->course->lang))) {
            $addlangmenu = false;
        }

        if ($addlangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $menu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }
        
        $content = '<div class="languagemenu">';
		$content .= '<ul class="nav">';
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }
        $content . '</ul>';
        return $content . '</div>';
    }

    public function mobile_menu() {
        global $USER, $CFG;

        $custommenu = new custom_menu('', current_language());
        
        // TODO: eliminate this duplicated logic, it belongs in core, not
        // here. See MDL-39565.
        $addlangmenu = true;
        $langs = get_string_manager()->get_list_of_translations();
        if (count($langs) < 2
            or empty($CFG->langmenu)
            or ($this->page->course != SITEID and !empty($this->page->course->lang))) {
            $addlangmenu = false;
        }

        if ($addlangmenu) {
            $strlang =  get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $language = $custommenu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }
  
        if ($USER->id != 0) {
            $person = $custommenu->add(fullname($USER));
            $person->add(get_string("myhome"), new moodle_url("/my/"), get_string("myhome"));
            $person->add(get_string("profile"), new moodle_url("/user/profile.php",array("id"=>$USER->id)), get_string("profile"));
            $person->add(get_string("grades","grades"), new moodle_url("/grade/report/overview/index.php"), get_string("grades","grades"));
            $person->add(get_string("messages", "message"), new moodle_url("/message/"), get_string("messages", "message"));
            $person->add(get_string("preferences", "moodle"), new moodle_url("/user/preferences.php"), get_string("preferences", "moodle"));
            $person->add(get_string("logout"), new moodle_url("/login/logout.php", array('sesskey' => sesskey())), get_string("logout"));
        }
        
        $content = '<ul class="nav">';
        foreach ($custommenu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }

        return $content.'</ul>';
    }


    public function footer_html($footer_data) {
        global $OUTPUT;

        $r = '<div class="row-fluid">';
        $r .= '<div class="span4">';
        $r .= '<h3>' . get_string('contacts', 'theme_elf_bs') . '</h3>';
        $r .= '<div>' . format_text($footer_data->workplace) . '</div>';
		    $r .= '<div>' . format_text($footer_data->department) . '</div>';
		    $r .= '<div>' . format_text($footer_data->institution) . '</div>';
        $r .= '<div>' . $footer_data->address . '</div>';
        $r .= '<div><a href="mailto:' . $footer_data->email . '" title="' . get_string('email', 'theme_elf_bs') . '">' . $footer_data->email . '</a></div>';
        $r .= '<div><a href="http://'.$footer_data->web.'" >' . $footer_data->web . '</a></div>';
        $r .= '<div>'.get_string('phone','theme_elf_bs').': ' . $footer_data->phone . '</div>';
        $r .= '<div>'.get_string('mobile','theme_elf_bs').': ' . $footer_data->mobile . '</div>';
        $r .= '</div>';

        $r .= '<div class="span8">';
        $r .= '<div>';
		    $r .= '<div class="span6">';
		    if(isset($footer_data->sections[0])) {
			    $r .= '<h3>' . format_text($footer_data->sections[0]['name']) . '</h3>';
			    foreach($footer_data->sections[0]['items'] as $item)
					  $r .= '<div><a href="'.$item['url'].'">'.format_text($item['name']).'</a></div>';
		    }
		    $r .= '</div>';
		    $r .= '<div class="span6">';
		    if(isset($footer_data->sections[2])) {
		    $r .= '<h3>' . format_text($footer_data->sections[2]['name']) . '</h3>';
			  foreach($footer_data->sections[2]['items'] as $item)
				$r .= '<div><a href="'.$item['url'].'">'.format_text($item['name']).'</a></div>';
		    }
		    $r .= '</div>';
		    $r .= '<div class="clearfix"></div>';
		    $r .= '</div>';
		
        $r .= '<div>';
        $r .= '<div class="span6">';
        if(isset($footer_data->sections[1])) {
          $r .= '<h3>' . format_text($footer_data->sections[1]['name']) . '</h3>';
          foreach($footer_data->sections[1]['items'] as $item)
              $r .= '<div><a href="'.$item['url'].'">'.format_text($item['name']).'</a></div>';
        }
        $r .= '</div>';
        $r .= '<div class="span6">';
        $r .= '<div class="social-links">';
        foreach($footer_data->social as $social => $url)
          $r .= '<div><a href="'.$url.'" target="_blank"><img src="'.$OUTPUT->image_url('social_'.$social, 'theme_elf_bs').'" /></a></div>';
        $r .= '</div>';
        $r .= '</div>';
        $r .= '<div class="clearfix"></div>';
        $r .= '</div>';
		
        $r .= '</div>';

        return $r;
    }

    /*
     * This code renders the custom menu items for the
     * bootstrap dropdown menu.
     */

    protected function render_custom_menu_item(custom_menu_item $menunode, $level = 0) {
        static $submenucount = 0;

        if ($menunode->has_children()) {

            if ($level == 1) {
                $class = 'dropdown';
            } else {
                $class = 'dropdown-submenu';
            }

            if ($menunode === $this->language) {
                $class .= ' langmenu';
            }
            $content = html_writer::start_tag('li', array('class' => $class));
            // If the child has menus render it as a sub menu.
            $submenucount++;
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#cm_submenu_' . $submenucount;
            }

            $content .= html_writer::start_tag('a', array('href' => $url, 'class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-target' => '.dropdown-menu-container', 'title' => $menunode->get_title()));
            $content .= $menunode->get_text();
            if ($level == 1) {
                $content .= '<b class="caret"></b>';
            }
            $content .= '</a>';
            $content .= '<div class="dropdown-menu-container"><ul class="dropdown-menu">';
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode, 0);
            }
            $content .= '</ul></div>';
        } else {
            $content = '<li>';
            // The node doesn't have children so produce a final menuitem.
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#';
            }
            $content .= html_writer::link($url, $menunode->get_text(), array('title' => $menunode->get_title()));
        }
        return $content;
    }

    public function search_form() {
        $formid = 'coursesearchmain';
        $inputid = 'shortsearchbox';
        $inputsize = 12;

        $strsearchcourses = get_string("searchcourses");
        $searchurl = new moodle_url('/course/search.php');

        $output = html_writer::start_tag('form', array('id' => $formid, 'action' => $searchurl, 'method' => 'get'));
        $output .= html_writer::tag('label', $strsearchcourses . ': ', array('for' => $inputid));
        $output .= html_writer::empty_tag('input', array('type' => 'text', 'id' => $inputid, 'class' => 'text',
                    'size' => $inputsize, 'name' => 'search', 'value' => ''));
        $output .= html_writer::empty_tag('input', array('type' => 'submit', 'class' => 'submit',
                    'value' => get_string('go')));
        $output .= html_writer::end_tag('form');

        return $output;
    }
}
