<?php
require_once($CFG->dirroot.'/local/elf/elfconfig/locallib.php');
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
 * The modchooser renderable.
 *
 * @package    core_course
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\output\chooser;
use core\output\chooser_section;
use context_course;
use lang_string;
use moodle_url;
use pix_icon;
use renderer_base;
use stdClass;

/**
 * The modchooser renderable class.
 *
 * @package    core_course
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_elf_bs_modchooser extends chooser {

    /** @var stdClass The course. */
    public $course;

    /**
     * Constructor.
     *
     * @param stdClass $course The course.
     * @param stdClass[] $modules The modules.
     */
    public function __construct(stdClass $course, array $modules) {
        $this->course = $course;
        $sections = [];
        $context = context_course::instance($course->id);

	    //ELF-FF -- FB array filtering experimental modules
        $experimental = array();  
        $extramods = elf_get_experimental_modules();

        foreach($extramods as $extramod)
            if(isset($modules[$extramod]))
        	$experimental[$extramod] = $modules[$extramod];
        //ELF-FF end

        // Activities.
        $activities = array_filter($modules, function($mod) {
            return ($mod->archetype !== MOD_ARCHETYPE_RESOURCE && $mod->archetype !== MOD_ARCHETYPE_SYSTEM);
        });

	    //ELF -- FF -- FILTERING FROM ACTIVITIES
        foreach($extramods as $extramod)
            if(isset($activities[$extramod]))
        	unset($activities[$extramod]);
        //ELF - -FF - end

        if (count($activities)) {
            $sections[] = new chooser_section('activities', new lang_string('activities'),
                array_map(function($module) use ($context) {
                    return new theme_elf_bs_modchooser_item($module, $context);
                }, $activities)
            );
        }	
        $resources = array_filter($modules, function($mod) {
            return ($mod->archetype === MOD_ARCHETYPE_RESOURCE);
        });

	    //ELF -- FF -- FILTERING FROM RESOURCES
        foreach($extramods as $extramod)
            if(isset($resources[$extramod]))
        	unset($resources[$extramod]);
        //ELF - -FF - end

        if (count($resources)) {
            $sections[] = new chooser_section('resources', new lang_string('resources'),
                array_map(function($module) use ($context) {
                    return new theme_elf_bs_modchooser_item($module, $context);
                }, $resources)
            );
        }

        //ELF -- FF DISPLAYING EXPERIMENTAL MODULES
        //experimental
        if (count($experimental)) {
            $sections[] = new chooser_section('experimental', new lang_string('experimental', 'local_elf'),
                array_map(function($module) use ($context) {
                    return new theme_elf_bs_modchooser_item($module, $context);
                }, $experimental)
            );
        }
        //ELF -- FF - END


        $actionurl = new moodle_url('/course/jumpto.php');
        $title = new lang_string('addresourceoractivity');
        parent::__construct($actionurl, $title, $sections, 'jumplink');

        $this->set_instructions(new lang_string('selectmoduletoviewhelp'));
        $this->add_param('course', $course->id);
    }

    /**
     * Export for template.
     *
     * @param renderer_base  The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = parent::export_for_template($output);
        $data->courseid = $this->course->id;
        return $data;
    }

}
