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
 * The secure layout.
 *
 * @package   theme_elf_bs
 * @copyright 2013 Moodle, moodle.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('bootstrap', 'theme_elf_bs');
$footer_data = theme_elf_bs_get_data_for_footer_settings($OUTPUT, $PAGE);
 
echo $OUTPUT->doctype(); ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body <?php echo $OUTPUT->body_attributes(); ?> data-header="compact">

<?php echo $OUTPUT->standard_top_of_body_html() ?>
<div class="main-wrapper">
		<div class="content-wrapper">
<header role="banner" class="navbar navbar-fixed-top moodle-has-zindex">
    <nav role="navigation" class="navbar-inner-fixed navbar-inner-docked">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"></a>
            
            <div class="nav-hide docked-upper-menu">
				<?php echo $OUTPUT->user_menu(); ?>
				<?php echo $OUTPUT->language_menu(); ?>
			</div>

			<div class="clearfix page-title-container">
				<a class="logo-mini brand" href="<?php echo $CFG->wwwroot;?>"><img src="<?php echo $OUTPUT->image_url('logo_mini', 'theme_elf_bs'); ?>" alt="" /></a>
				<div class="h1container brand">
					<?php if($PAGE->course->id > 1) : ?>
					<a href="<?php echo $CFG->wwwroot;?>/course/view.php?id=<?= $PAGE->course->id;?>" title="<?= $PAGE->course->fullname;?>">
						<?php echo $OUTPUT->page_heading(); ?>
					</a>
					<?php else : ?>
						<?php echo $OUTPUT->page_heading(); ?>
					<?php endif; ?>
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
</header>

<div id="page" class="container-fluid">
    <div id="page-content" class="row-fluid">
        <div id="region-bs-main" class="span4 offset4 login">
            <div class="row-fluid">
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
                    </div>
                    <div id="login-content-manual" hidden="hiddent" style="display: none;">
                        <?php echo $OUTPUT->main_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
		<div class="footer-wrapper">
<footer id="page-footer">
	<div class="container-fluid">
		<?php echo $OUTPUT->footer_html($footer_data); ?>
	</div>
</footer>
</div></div>

<a href="#" id="scroll-up" style="display:none;">
</a>

<?php echo $OUTPUT->standard_end_of_body_html() ?>

</body>
</html>
