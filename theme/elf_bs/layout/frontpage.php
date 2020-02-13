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
// Get the HTML for the settings bits.

$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('bootstrap', 'theme_elf_bs');
$PAGE->requires->jquery_plugin('unslider', 'theme_elf_bs');

$banner_data = theme_elf_bs_get_data_for_banner_settings($OUTPUT, $PAGE);
$footer_data = theme_elf_bs_get_data_for_footer_settings($OUTPUT, $PAGE);

$slidespeed = '600';
$hasslidespeed = (empty($PAGE->theme->settings->slidespeed)) ? false : $PAGE->theme->settings->slidespeed;
if ($hasslidespeed) {
    $slidespeed = $hasslidespeed;
}

echo $OUTPUT->doctype()
?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
    <head>
        <title><?php echo $OUTPUT->page_title(); ?></title>
        <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
        <?php echo $OUTPUT->standard_head_html() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body <?php echo $OUTPUT->body_attributes(); ?> data-header="full">

        <?php echo $OUTPUT->standard_top_of_body_html() ?>

	<div class="main-wrapper">
		<div class="content-wrapper">
        <header role="banner">
            <div id="header">
                <a class="logo" href="<?php echo $CFG->wwwroot; ?>">
                    <img src="<?php echo $OUTPUT->image_url('logo', 'theme_elf_bs'); ?>" alt="" />
                    <div class="content">
                        <?php echo format_string($SITE->fullname, true, array('context' => context_course::instance(SITEID))); ?>
                    </div>
                </a>
                <?php echo $OUTPUT->user_menu(); ?>
                <?php echo $OUTPUT->language_menu(); ?>
				<div class="hideblocks">
					<a href="#" class="hide-blocks-btn" title="<?= get_string('show-hide-blocks-text', 'theme_elf_bs'); ?>"><img src="<?php echo $OUTPUT->image_url('blocks_hide', 'theme_elf_bs'); ?>" 
						data-show="<?php echo $OUTPUT->image_url('blocks_hide', 'theme_elf_bs'); ?>"
						data-hidden="<?php echo $OUTPUT->image_url('blocks_show', 'theme_elf_bs'); ?>"
						alt=""/></a>
				</div>
            </div>

            <div class="navbar navbar-static-top moodle-has-zindex">
                <nav role="navigation" class="navbar-inner">
                    <div class="container-fluid">
                        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        </a>
                        <a class="logo-mini brand" href="<?php echo $CFG->wwwroot; ?>">
                            <img src="<?php echo $OUTPUT->image_url('logo_mini', 'theme_elf_bs'); ?>" alt="" />
                            <?php echo format_string($SITE->fullname, true, array('context' => context_course::instance(SITEID))); ?>
                        </a>
                        <div class="nav-hide"> 

                            <div class="site-search">
                                <?php echo $OUTPUT->search_form(); ?>
                            </div>
                            <div class="nav-hide docked-upper-menu">
                                <?php echo $OUTPUT->user_menu(); ?>
                                <?php echo $OUTPUT->language_menu(); ?>
								<div class="hideblocks">
									<a href="#" class="hide-blocks-btn"><img src="<?php echo $OUTPUT->image_url('blocks_hide', 'theme_elf_bs'); ?>" 
										data-show="<?php echo $OUTPUT->image_url('blocks_hide', 'theme_elf_bs'); ?>"
										data-hidden="<?php echo $OUTPUT->image_url('blocks_show', 'theme_elf_bs'); ?>"
										alt=""/></a>
								</div>
                            </div>
                        </div>
                        <div class="nav-collapse collapse">
                            <ul class="nav pull-right">  
                                <li><?php echo $OUTPUT->mobile_menu(); ?></li>
                                <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </header>

        <div id="page" class="container-fluid">

            <header id="page-header" class="clearfix">
                <div id="page-navbar" class="clearfix">
                    <nav class="breadcrumb-nav"><?php echo $OUTPUT->navbar(); ?></nav>
                    <div class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></div>
                </div>
                <div id="course-header">
                    <?php echo $OUTPUT->course_header(); ?>
                </div>
            </header>

            <div class="row-fluid">
                <?php if ($USER->id != 0) : ?>
                    <div id="upper-banner" class="banner">
                        <div id="banner-menu" class="span3">
                            <a href="#" id="banner-menu-news" class="banner-menu-item banner-menu-news banner-menu-item-selected"><?php echo get_string('news', 'theme_elf_bs'); ?></a>
                            <a href="<?php echo $banner_data->teachers_info_url; ?>" id="banner-menu-teachers" class="banner-menu-item banner-menu-teachers" target="_blank"><?php echo get_string('forteachers', 'theme_elf_bs'); ?></a>
                            <a href="<?php echo $banner_data->students_info_url; ?>" id="banner-menu-students" class="banner-menu-item banner-menu-students" target="_blank"><?php echo get_string('forstudents', 'theme_elf_bs'); ?></a>
                            <a href="<?php echo $banner_data->sos_info_url; ?>" id="banner-menu-sos" class="banner-menu-item banner-menu-sos" target="_blank"><?php echo get_string('sos', 'theme_elf_bs'); ?></a>
                        </div>
                        <div id="banner-content">
                            <div id="banner-content-news"><?php require_once(dirname(__FILE__).'/includes/slideshow.php'); ?></div>
                            <div id="banner-content-teachers" hidden="hidden" style="display: none;"><?php echo $banner_data->teachers_info; ?></div>
                            <div id="banner-content-students" hidden="hidden" style="display: none;"><?php echo $banner_data->students_info; ?></div>
                            <div id="banner-content-sos" hidden="hidden" style="display: none;"><?php echo $banner_data->sos_info; ?></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                <?php else : ?>
                    <div id="upper-banner" class="has-login">
                        <div id="banner-menu" class="span4">
                            <div class="login-menu">
                                <div id="login-menu-shibboleth" class="login-menu-item login-menu-selected"><?php echo get_string('teachers_students_login', 'theme_elf_bs'); ?></div>
                                <div id="login-menu-manual" class="login-menu-item"><?php echo get_string('others_guests_login', 'theme_elf_bs'); ?></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="login-content">
                                <div id="login-content-shibboleth">
                                    <img src="<?php echo $OUTPUT->image_url('mu_logo', 'theme_elf_bs'); ?>" alt="" /><br />
                                    <a href="<?php echo $CFG->httpswwwroot; ?>/auth/shibboleth/index.php" title="<?php echo get_string('login', 'moodle'); ?>" class="button"><?php echo get_string('login', 'moodle'); ?></a><br />
                                    <a href="#" id="login-help" title="<?php echo get_string('cannot-login', 'theme_elf_bs'); ?>"><?php echo get_string('cannot-login', 'theme_elf_bs'); ?></a>
									<div class="signuppanel"><?= format_text($CFG->auth_instructions);?></div>
                                </div>
                                <div id="login-content-manual" hidden="hiddent" style="display: none;">
                                    <?php $username = theme_elf_bs_get_login_form_username(); ?>
                                    <form action="<?php echo $CFG->httpswwwroot; ?>/login/index.php" method="post" id="login">
                                        <div class="loginform">
                                            <div>
                                                <span><label for="username"><img src="<?php echo $OUTPUT->image_url('login_username', 'theme_elf_bs'); ?>" alt="" /></label><input type="text" name="username" id="username" size="15" value="<?php p($username) ?>" /></span>
                                            </div>
                                            <div>
                                                <span><label for="password"><img src="<?php echo $OUTPUT->image_url('login_password', 'theme_elf_bs'); ?>" alt="" /></label><input type="password" name="password" id="password" size="15" value="" /></span>
                                                <input type="submit" id="loginbtn" value="<?php print_string("login") ?>" />
                                            </div>
                                        </div>
                                        <?php if (isset($CFG->rememberusername) and $CFG->rememberusername == 2) { ?>
                                            <div class="rememberpass">
                                                <input type="checkbox" name="rememberusername" id="rememberusername" value="1" <?php if ($username) {
                                        echo 'checked="checked"';
                                    } ?> />
                                                <label for="rememberusername"><?php print_string('rememberusername', 'admin') ?></label>
                                            </div>
    <?php } ?>
                                    </form>

                                    <div class="login-manual-desc">
                                        <div class="desc">
    <?php print_string("someallowguest") ?>
                                        </div>
                                        <form action="<?= $CFG->wwwroot;?>/login/index.php" method="post" id="guestlogin">
                                            <div class="guestform">
                                                <input type="hidden" name="username" value="guest" />
                                                <input type="hidden" name="password" value="guest" />
                                                <input type="submit" id="guestloginbtn" value="<?php print_string("loginguest") ?>" />
                                            </div>
                                        </form>

                                        <a href="" title="<?php echo get_string('cannot-login', 'theme_elf_bs'); ?>"><?php echo get_string('cannot-login', 'theme_elf_bs'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="banner-content">
                            <?php require_once(dirname(__FILE__).'/includes/slideshow.php'); ?>
                        </div>

                        <div class="clearfix"></div>
                    </div>
<?php endif; ?>
            </div>

            <div id="page-content" class="row-fluid">
                <div class="span9">
                    <div class="row-fluid">
                        <section id="region-main" class="span12 pull-left">
                            <?php if ($USER->id != 0) : ?>
                                <?php echo $OUTPUT->main_content(); ?>
<?php else: ?>
    <?php echo $OUTPUT->main_content(); ?>
                                <div class="row-fluid">
                                    <a href="<?php echo $banner_data->teachers_info_url; ?>" class="span4 container banner-links banner-links-teachers" target="_blank"><?php echo get_string('forteachers', 'theme_elf_bs'); ?></a>
                                    <a href="<?php echo $banner_data->students_info_url; ?>" class="span4 container banner-links banner-links-students" target="_blank"><?php echo get_string('forstudents', 'theme_elf_bs'); ?></a>
                                    <a href="<?php echo $banner_data->sos_info_url; ?>" class="span4 container banner-links banner-links-sos" target="_blank"><?php echo get_string('sos', 'theme_elf_bs'); ?></a>
                                </div>
                        <?php endif; ?>
                        </section>
                <?php //echo $OUTPUT->blocks('side-pre', 'span4 desktop-first-column');  ?>
                    </div>
                </div>
<?php echo $OUTPUT->blocks('side-pre', 'span3'); ?>
            </div>
        </div>

		</div>
		
		<?php require_once(dirname(__FILE__).'/includes/footer.php'); ?>
	</div>
	

<?php echo $OUTPUT->standard_end_of_body_html() ?>
<script>
    $('#slider').unslider({
				fluid: true,
        dots: true,
		autoplay: true,
        delay: <?php echo $slidespeed; ?>,
				keys: true,
				arrows: true,
    			prev: '<',
    			next: '>'
	});
</script>
    </body>
</html>
