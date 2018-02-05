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
 * Moodle's Clean theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package   theme_elf_bs
 * @copyright 2013 Moodle, moodle.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Returns an object containing HTML for the areas affected by settings.
 *
 * Do not add Clean specific logic in here, child themes should be able to
 * rely on that function just by declaring settings with similar names.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page Pass in $PAGE.
 * @return stdClass An object with the following properties:
 *      - navbarclass A CSS class to use on the navbar. By default ''.
 *      - heading HTML to use for the heading. A logo if one is selected or the default heading.
 *      - footnote HTML to use as a footnote. By default ''.
 */
function theme_elf_bs_get_data_for_banner_settings(renderer_base $output, moodle_page $page) {
    $return = new stdClass;

    $return->teachers_info = (!empty($page->theme->settings->teachers_info))?$page->theme->settings->teachers_info:'';
    $return->students_info = (!empty($page->theme->settings->students_info))?$page->theme->settings->students_info:'';
    $return->sos_info = (!empty($page->theme->settings->sos_info))?$page->theme->settings->sos_info:'';
    
    $return->teachers_info_url = (!empty($page->theme->settings->teachers_info_url))?$page->theme->settings->teachers_info_url:'';
    $return->students_info_url = (!empty($page->theme->settings->students_info_url))?$page->theme->settings->students_info_url:'';
    $return->sos_info_url = (!empty($page->theme->settings->sos_info_url))?$page->theme->settings->sos_info_url:'';

    return $return;
}

function theme_elf_bs_get_data_for_footer_settings(renderer_base $output, moodle_page $page) {
    $return = new stdClass;

    $return->institution = (!empty($page->theme->settings->institution))?$page->theme->settings->institution:'';
	$return->department = (!empty($page->theme->settings->department))?$page->theme->settings->department:'';
	$return->workplace = (!empty($page->theme->settings->workplace))?$page->theme->settings->workplace:'';
    $return->address = (!empty($page->theme->settings->address))?$page->theme->settings->address:'';
    $return->email = (!empty($page->theme->settings->email))?$page->theme->settings->email:'';
    $return->web = (!empty($page->theme->settings->web))?$page->theme->settings->web:'';
    $return->phone = (!empty($page->theme->settings->phone))?$page->theme->settings->phone:'';
    $return->mobile = (!empty($page->theme->settings->mobile))?$page->theme->settings->mobile:'';
    $return->copyright = (!empty($page->theme->settings->copyright))?$page->theme->settings->copyright:'';

    $return->social = array();
    if(!empty($page->theme->settings->twitter_url)) 
		$return->social['twitter'] = $page->theme->settings->twitter_url;
	if(!empty($page->theme->settings->facebook_url)) 
		$return->social['facebook'] = $page->theme->settings->facebook_url;
	if(!empty($page->theme->settings->youtube_url)) 
		$return->social['youtube'] = $page->theme->settings->youtube_url;
	if(!empty($page->theme->settings->instagram_url)) 
		$return->social['instagram'] = $page->theme->settings->instagram_url;
    if(!empty($page->theme->settings->flickr_url)) 
		$return->social['flickr'] = $page->theme->settings->flickr_url;
    if(!empty($page->theme->settings->tumblr_url)) 
		$return->social['tumblr'] = $page->theme->settings->tumblr_url;
    

    if (!empty($page->theme->settings->section_1_name)) {
            $return->sections[0]['name'] = $page->theme->settings->section_1_name;
            if(!empty($page->theme->settings->section_1_first_name) && !empty($page->theme->settings->section_1_first_url)) 
                    $return->sections[0]['items'][] = array(
                                    'url' => $page->theme->settings->section_1_first_url,
                                    'name' => $page->theme->settings->section_1_first_name
                            );
            if(!empty($page->theme->settings->section_1_second_name) && !empty($page->theme->settings->section_1_second_url)) 
                    $return->sections[0]['items'][]  = array(
                                    'url' => $page->theme->settings->section_1_second_url,
                                    'name' => $page->theme->settings->section_1_second_name
                            );
            if(!empty($page->theme->settings->section_1_third_name) && !empty($page->theme->settings->section_1_third_url)) 
                    $return->sections[0]['items'][]  = array(
                                    'url' => $page->theme->settings->section_1_third_url,
                                    'name' => $page->theme->settings->section_1_third_name
                            );
    }

    if (!empty($page->theme->settings->section_2_name)) {
            $return->sections[1]['name'] = $page->theme->settings->section_2_name;
            if(!empty($page->theme->settings->section_2_first_name) && !empty($page->theme->settings->section_2_first_url)) 
                    $return->sections[1]['items'][]  = array(
                                    'url' => $page->theme->settings->section_2_first_url,
                                    'name' => $page->theme->settings->section_2_first_name
                            );
            if(!empty($page->theme->settings->section_2_second_name) && !empty($page->theme->settings->section_2_second_url)) 
                    $return->sections[1]['items'][]  = array(
                                    'url' => $page->theme->settings->section_2_second_url,
                                    'name' => $page->theme->settings->section_2_second_name
                            );
            if(!empty($page->theme->settings->section_2_third_name) && !empty($page->theme->settings->section_2_third_url)) 
                    $return->sections[1]['items'][]  = array(
                                    'url' => $page->theme->settings->section_2_third_url,
                                    'name' => $page->theme->settings->section_2_third_name
                            );
    }

    if (!empty($page->theme->settings->section_3_name)) {
            $return->sections[2]['name'] = $page->theme->settings->section_3_name;
            if(!empty($page->theme->settings->section_3_first_name) && !empty($page->theme->settings->section_3_first_url)) 
                    $return->sections[2]['items'][]  = array(
                                    'url' => $page->theme->settings->section_3_first_url,
                                    'name' => $page->theme->settings->section_3_first_name
                            );
            if(!empty($page->theme->settings->section_3_second_name) && !empty($page->theme->settings->section_3_second_url)) 
                    $return->sections[2]['items'][]  = array(
                                    'url' => $page->theme->settings->section_3_second_url,
                                    'name' => $page->theme->settings->section_3_second_name
                            );
            if(!empty($page->theme->settings->section_3_third_name) && !empty($page->theme->settings->section_3_third_url)) 
                    $return->sections[2]['items'][]  = array(
                                    'url' => $page->theme->settings->section_3_third_url,
                                    'name' => $page->theme->settings->section_3_third_name
                            );
    }
	
    return $return;
}

function theme_elf_bs_get_login_form_username() {
    return get_moodle_cookie();
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */

function theme_elf_bs_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        $theme = theme_config::load('elf_bs');
        if ($filearea === 'bannerimage1') {
            return $theme->setting_file_serve('bannerimage1', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerimage2') {
            return $theme->setting_file_serve('bannerimage2', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerimage3') {
            return $theme->setting_file_serve('bannerimage3', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerimage4') {
            return $theme->setting_file_serve('bannerimage4', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerimage5') {
            return $theme->setting_file_serve('bannerimage5', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerimage6') {
            return $theme->setting_file_serve('bannerimage6', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerimage7') {
            return $theme->setting_file_serve('bannerimage7', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerimage8') {
            return $theme->setting_file_serve('bannerimage8', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerimage9') {
            return $theme->setting_file_serve('bannerimage9', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerimage10') {
            return $theme->setting_file_serve('bannerimage10', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerbackgroundimage1') {
            return $theme->setting_file_serve('bannerbackgroundimage1', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerbackgroundimage2') {
            return $theme->setting_file_serve('bannerbackgroundimage2', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerbackgroundimage3') {
            return $theme->setting_file_serve('bannerbackgroundimage3', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerbackgroundimage4') {
            return $theme->setting_file_serve('bannerbackgroundimage4', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerbackgroundimage5') {
            return $theme->setting_file_serve('bannerbackgroundimage5', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerbackgroundimage6') {
            return $theme->setting_file_serve('bannerbackgroundimage6', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerbackgroundimage7') {
            return $theme->setting_file_serve('bannerbackgroundimage7', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerbackgroundimage8') {
            return $theme->setting_file_serve('bannerbackgroundimage8', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerbackgroundimage9') {
            return $theme->setting_file_serve('bannerbackgroundimage9', $args, $forcedownload, $options);
        } else if ($filearea === 'bannerbackgroundimage10') {
            return $theme->setting_file_serve('bannerbackgroundimage10', $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}