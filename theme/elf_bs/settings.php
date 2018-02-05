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

defined('MOODLE_INTERNAL') || die;
$settings = null;
global $PAGE;

$ADMIN->add('themes', new admin_category('theme_elf_bs', 'Elf Bootstrap'));

$ADMIN->add('theme_elf_bs', new admin_category('theme_elf_bs_banner_section', get_string('banner', 'theme_elf_bs')));

$temp = new admin_settingpage('theme_elf_bs_slideshow',  get_string('slideshowsettings', 'theme_elf_bs'));

$temp->add(new admin_setting_heading('theme_elf_bs_banner', get_string('slideshowsettingssub', 'theme_elf_bs'),
            format_text(get_string('slideshowsettingsdesc' , 'theme_elf_bs'), FORMAT_MARKDOWN)));

    // Set Number of Slides.
    $name = 'theme_elf_bs/slidenumber';
    $title = get_string('slidenumber' , 'theme_elf_bs');
    $description = get_string('slidenumberdesc', 'theme_elf_bs');
    $default = '1';
    $choices = array(
		'0'=>'0',
    	'1'=>'1',
    	'2'=>'2',
    	'3'=>'3',
    	'4'=>'4',
    	'5'=>'5',
    	'6'=>'6',
    	'7'=>'7',
    	'8'=>'8',
    	'9'=>'9',
    	'10'=>'10');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Set the Slide Speed.
    $name = 'theme_elf_bs/slidespeed';
    $title = get_string('slidespeed' , 'theme_elf_bs');
    $description = get_string('slidespeeddesc', 'theme_elf_bs');
    $default = '600';
    $setting = new admin_setting_configtext($name, $title, $description, $default );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $hasslidenum = (!empty($PAGE->theme->settings->slidenumber));
    $slidenum = '1';
    if ($hasslidenum) {
        $slidenum = $PAGE->theme->settings->slidenumber;
    } 

    $bannertitle = array('Slide One', 'Slide Two', 'Slide Three','Slide Four','Slide Five','Slide Six','Slide Seven', 'Slide Eight', 'Slide Nine', 'Slide Ten');

    foreach (range(1, $slidenum) as $bannernumber) {

    	// This is the descriptor for the Banner Settings.
    	$name = 'theme_elf_bs/banner';
        $title = get_string('bannerindicator', 'theme_elf_bs');
    	$information = get_string('bannerindicatordesc', 'theme_elf_bs');
    	$setting = new admin_setting_heading($name.$bannernumber, $title.$bannernumber, $information);
    	$setting->set_updatedcallback('theme_reset_all_caches');
    	$temp->add($setting);

        // Enables the slide.
        $name = 'theme_elf_bs/enablebanner' . $bannernumber;
        $title = get_string('enablebanner', 'theme_elf_bs', $bannernumber);
        $description = get_string('enablebannerdesc', 'theme_elf_bs', $bannernumber);
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $temp->add($setting);

        // Slide Title.
        $name = 'theme_elf_bs/bannertitle' . $bannernumber;
        $title = get_string('bannertitle', 'theme_elf_bs', $bannernumber);
        $description = get_string('bannertitledesc', 'theme_elf_bs', $bannernumber);
        $default = $bannertitle[$bannernumber - 1];
        $setting = new admin_setting_configtext($name, $title, $description, $default );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $temp->add($setting);
        
        // Slide Title color
    	$name = 'theme_elf_bs/bannertitlecolor' . $bannernumber;
    	$title = get_string('bannertitlecolor', 'theme_elf_bs', $bannernumber);
    	$description = get_string('bannertitlecolordesc', 'theme_elf_bs', $bannernumber);
    	$default = '#000';
    	$previewconfig = null;
    	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    	$setting->set_updatedcallback('theme_reset_all_caches');
    	$temp->add($setting);

        // Slide text.
        $name = 'theme_elf_bs/bannertext' . $bannernumber;
        $title = get_string('bannertext', 'theme_elf_bs', $bannernumber);
        $description = get_string('bannertextdesc', 'theme_elf_bs', $bannernumber);
        $default = 'Bacon ipsum dolor sit amet turducken jerky beef ribeye boudin t-bone shank fatback pork loin pork short loin jowl flank meatloaf venison. Salami meatball sausage short loin beef ribs';
        $setting = new admin_setting_configtextarea($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $temp->add($setting);

        // Slide Text color
    	$name = 'theme_elf_bs/bannertextcolor' . $bannernumber;
    	$title = get_string('bannertextcolor', 'theme_elf_bs', $bannernumber);
    	$description = get_string('bannertextcolordesc', 'theme_elf_bs', $bannernumber);
    	$default = '#000';
    	$previewconfig = null;
    	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    	$setting->set_updatedcallback('theme_reset_all_caches');
    	$temp->add($setting);

        // Text for Slide Link.
        $name = 'theme_elf_bs/bannerlinktext' . $bannernumber;
        $title = get_string('bannerlinktext', 'theme_elf_bs', $bannernumber);
        $description = get_string('bannerlinktextdesc', 'theme_elf_bs', $bannernumber);
        $default = 'Read More';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $temp->add($setting);
        
        // Destination URL for Slide Link
        $name = 'theme_elf_bs/bannerlinkurl' . $bannernumber;
        $title = get_string('bannerlinkurl', 'theme_elf_bs', $bannernumber);
        $description = get_string('bannerlinkurldesc', 'theme_elf_bs', $bannernumber);
        $default = '#';
        $previewconfig = null;
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $temp->add($setting);
        
        // Text color for Slide Link.
    	$name = 'theme_elf_bs/bannerlinktextcolor' . $bannernumber;
    	$title = get_string('bannerlinktextcolor', 'theme_elf_bs', $bannernumber);
    	$description = get_string('bannerlinktextcolordesc', 'theme_elf_bs', $bannernumber);
    	$default = '#000';
    	$previewconfig = null;
    	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    	$setting->set_updatedcallback('theme_reset_all_caches');
    	$temp->add($setting);
        
        // Background color for Slide Link.
    	$name = 'theme_elf_bs/bannerlinkbackgroundcolor' . $bannernumber;
    	$title = get_string('bannerlinkbackgroundcolor', 'theme_elf_bs', $bannernumber);
    	$description = get_string('bannerlinkbackgroundcolordesc', 'theme_elf_bs', $bannernumber);
    	$default = '#000';
    	$previewconfig = null;
    	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    	$setting->set_updatedcallback('theme_reset_all_caches');
    	$temp->add($setting);

        // Slide Image.
    	$name = 'theme_elf_bs/bannerimage' . $bannernumber;
    	$title = get_string('bannerimage', 'theme_elf_bs', $bannernumber);
    	$description = get_string('bannerimagedesc', 'theme_elf_bs', $bannernumber);
    	$setting = new admin_setting_configstoredfile($name, $title, $description, 'bannerimage'.$bannernumber);
    	$setting->set_updatedcallback('theme_reset_all_caches');
    	$temp->add($setting);
        
        // Slide Background Image.
    	$name = 'theme_elf_bs/bannerbackgroundimage' . $bannernumber;
    	$title = get_string('bannerbackgroundimage', 'theme_elf_bs', $bannernumber);
    	$description = get_string('bannerbackgroundimagedesc', 'theme_elf_bs', $bannernumber);
    	$setting = new admin_setting_configstoredfile($name, $title, $description, 'bannerbackgroundimage'.$bannernumber);
    	$setting->set_updatedcallback('theme_reset_all_caches');
    	$temp->add($setting);

    	// Slide Background Color.
    	$name = 'theme_elf_bs/bannercolor' . $bannernumber;
    	$title = get_string('bannercolor', 'theme_elf_bs', $bannernumber);
    	$description = get_string('bannercolordesc', 'theme_elf_bs', $bannernumber);
    	$default = '#000';
    	$previewconfig = null;
    	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    	$setting->set_updatedcallback('theme_reset_all_caches');
    	$temp->add($setting);

    }
$ADMIN->add('theme_elf_bs_banner_section', $temp);
    
// "geneicsettings" settingpage
$temp = new admin_settingpage('theme_elf_bs_banner',  get_string('bannersettings', 'theme_elf_bs'));

    // News info settings
    $name = 'theme_elf_bs/news_info_url';
    $title = get_string('news_info_url', 'theme_elf_bs');
    $description = get_string('news_info_url_desc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
	
    // Teachers info settings
    $name = 'theme_elf_bs/teachers_info';
    $title = get_string('teachers_info', 'theme_elf_bs');
    $description = get_string('teachers_info_desc', 'theme_elf_bs');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // Teachers info settings
    $name = 'theme_elf_bs/teachers_info_url';
    $title = get_string('teachers_info_url', 'theme_elf_bs');
    $description = get_string('teachers_info_url_desc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
	
    // Students info settings
    $name = 'theme_elf_bs/students_info';
    $title = get_string('students_info', 'theme_elf_bs');
    $description = get_string('students_info_desc', 'theme_elf_bs');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Students info settings
    $name = 'theme_elf_bs/students_info_url';
    $title = get_string('students_info_url', 'theme_elf_bs');
    $description = get_string('students_info_url_desc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
	
    // SOS info settings
    $name = 'theme_elf_bs/sos_info';
    $title = get_string('sos_info', 'theme_elf_bs');
    $description = get_string('sos_info_desc', 'theme_elf_bs');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // SOS info settings
    $name = 'theme_elf_bs/sos_info_url';
    $title = get_string('sos_info_url', 'theme_elf_bs');
    $description = get_string('sos_info_url_desc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
	
$ADMIN->add('theme_elf_bs_banner_section', $temp);

$ADMIN->add('theme_elf_bs', new admin_category('theme_elf_bs_footer', get_string('footer', 'theme_elf_bs')));

$temp = new admin_settingpage('theme_elf_bs_contact',  get_string('contactsettings', 'theme_elf_bs'));

    $name = 'theme_elf_bs/workplace';
    $title = get_string('workplace', 'theme_elf_bs');
    $description = get_string('workplacedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
    
    $name = 'theme_elf_bs/department';
    $title = get_string('department', 'theme_elf_bs');
    $description = get_string('departmentdesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
    
    $name = 'theme_elf_bs/institution';
    $title = get_string('institution', 'theme_elf_bs');
    $description = get_string('institutiondesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/address';
    $title = get_string('address', 'theme_elf_bs');
    $description = get_string('addressdesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/email';
    $title = get_string('email', 'theme_elf_bs');
    $description = get_string('emaildesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/web';
    $title = get_string('web', 'theme_elf_bs');
    $description = get_string('webdesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/phone';
    $title = get_string('phone', 'theme_elf_bs');
    $description = get_string('phonedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/mobile';
    $title = get_string('mobile', 'theme_elf_bs');
    $description = get_string('mobiledesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
   
	
$ADMIN->add('theme_elf_bs_footer', $temp);

$temp = new admin_settingpage('theme_elf_bs_social',  get_string('socialsettings', 'theme_elf_bs'));
	
    $name = 'theme_elf_bs/youtube_url';
    $title = get_string('youtube', 'theme_elf_bs');
    $description = get_string('youtubedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/twitter_url';
    $title = get_string('twitter', 'theme_elf_bs');
    $description = get_string('twitterdesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/flickr_url';
    $title = get_string('flickr', 'theme_elf_bs');
    $description = get_string('flickrdesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/facebook_url';
    $title = get_string('facebook', 'theme_elf_bs');
    $description = get_string('facebookdesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/tumblr_url';
    $title = get_string('tumblr', 'theme_elf_bs');
    $description = get_string('tumblrdesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/instagram_url';
    $title = get_string('instagram', 'theme_elf_bs');
    $description = get_string('instagramdesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
$ADMIN->add('theme_elf_bs_footer', $temp);


$ADMIN->add('theme_elf_bs_footer', new admin_category('theme_elf_bs_footer_sections', get_string('footersectionsettings', 'theme_elf_bs')));
	
$temp = new admin_settingpage('theme_elf_bs_footer_sections_first',  get_string('firstsection', 'theme_elf_bs'));
	
    $name = 'theme_elf_bs/section_1_name';
    $title = get_string('sectionname', 'theme_elf_bs');
    $description = get_string('sectionnamedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/section_1_first_name';
    $title = get_string('sectionfirstitemname', 'theme_elf_bs');
    $description = get_string('sectionfirstitemnamedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
    
    $name = 'theme_elf_bs/section_1_first_url';
    $title = get_string('sectionfirstitemurl', 'theme_elf_bs');
    $description = get_string('sectionfirstitemurldesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/section_1_second_name';
    $title = get_string('sectionseconditemname', 'theme_elf_bs');
    $description = get_string('sectionseconditemnamedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
    
    $name = 'theme_elf_bs/section_1_second_url';
    $title = get_string('sectionseconditemurl', 'theme_elf_bs');
    $description = get_string('sectionseconditemurldesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/section_1_third_name';
    $title = get_string('sectionthirditemname', 'theme_elf_bs');
    $description = get_string('sectionthirditemnamedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
    
    $name = 'theme_elf_bs/section_1_third_url';
    $title = get_string('sectionthirditemurl', 'theme_elf_bs');
    $description = get_string('sectionthirditemurldesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
$ADMIN->add('theme_elf_bs_footer_sections', $temp);

$temp = new admin_settingpage('theme_elf_bs_footer_sections_second',  get_string('secondsection', 'theme_elf_bs'));
	
    $name = 'theme_elf_bs/section_2_name';
    $title = get_string('sectionname', 'theme_elf_bs');
    $description = get_string('sectionnamedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/section_2_first_name';
    $title = get_string('sectionfirstitemname', 'theme_elf_bs');
    $description = get_string('sectionfirstitemnamedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
    
    $name = 'theme_elf_bs/section_2_first_url';
    $title = get_string('sectionfirstitemurl', 'theme_elf_bs');
    $description = get_string('sectionfirstitemurldesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/section_2_second_name';
    $title = get_string('sectionseconditemname', 'theme_elf_bs');
    $description = get_string('sectionseconditemnamedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
    
    $name = 'theme_elf_bs/section_2_second_url';
    $title = get_string('sectionseconditemurl', 'theme_elf_bs');
    $description = get_string('sectionseconditemurldesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/section_2_third_name';
    $title = get_string('sectionthirditemname', 'theme_elf_bs');
    $description = get_string('sectionthirditemnamedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
    
    $name = 'theme_elf_bs/section_2_third_url';
    $title = get_string('sectionthirditemurl', 'theme_elf_bs');
    $description = get_string('sectionthirditemurldesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
$ADMIN->add('theme_elf_bs_footer_sections', $temp);

$temp = new admin_settingpage('theme_elf_bs_footer_sections_third',  get_string('thirdsection', 'theme_elf_bs'));
	
    $name = 'theme_elf_bs/section_3_name';
    $title = get_string('sectionname', 'theme_elf_bs');
    $description = get_string('sectionnamedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/section_3_first_name';
    $title = get_string('sectionfirstitemname', 'theme_elf_bs');
    $description = get_string('sectionfirstitemnamedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
    
    $name = 'theme_elf_bs/section_3_first_url';
    $title = get_string('sectionfirstitemurl', 'theme_elf_bs');
    $description = get_string('sectionfirstitemurldesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/section_3_second_name';
    $title = get_string('sectionseconditemname', 'theme_elf_bs');
    $description = get_string('sectionseconditemnamedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
    
    $name = 'theme_elf_bs/section_3_second_url';
    $title = get_string('sectionseconditemurl', 'theme_elf_bs');
    $description = get_string('sectionseconditemurldesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
    $name = 'theme_elf_bs/section_3_third_name';
    $title = get_string('sectionthirditemname', 'theme_elf_bs');
    $description = get_string('sectionthirditemnamedesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
    
    $name = 'theme_elf_bs/section_3_third_url';
    $title = get_string('sectionthirditemurl', 'theme_elf_bs');
    $description = get_string('sectionthirditemurldesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
    
$ADMIN->add('theme_elf_bs_footer_sections', $temp);

$temp = new admin_settingpage('theme_elf_bs_footer_settings',  get_string('footersettings', 'theme_elf_bs'));
	
    $name = 'theme_elf_bs/copyright';
    $title = get_string('copyright', 'theme_elf_bs');
    $description = get_string('copyrightdesc', 'theme_elf_bs');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $temp->add($setting);
	
$ADMIN->add('theme_elf_bs_footer', $temp);

$temp = new admin_settingpage('theme_elf_bs_contact_form_settings',  get_string('contactformsettings', 'theme_elf_bs'));
	
	$name = 'theme_elf_bs/contactemail';
    $title = get_string('contactemail', 'theme_elf_bs');
    $description = get_string('contactemaildesc', 'theme_elf_bs');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $temp->add($setting);
	
	$name = 'theme_elf_bs/contactsuccesfullmessage';
    $title = get_string('contactsuccesfullmessage', 'theme_elf_bs');
    $description = get_string('contactsuccesfullmessagedesc', 'theme_elf_bs');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $temp->add($setting);
	
$ADMIN->add('theme_elf_bs_footer', $temp);
