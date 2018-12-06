<?php
$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('bootstrap', 'theme_elf_bs');

// Get the HTML for the settings bits.
if (right_to_left()) {
    $regionbsid = 'region-bs-main-and-post';
} else {
    $regionbsid = 'region-main-box';
}

echo $OUTPUT->doctype() ?>
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
				<div class="hideblocks">
					<a href="#" class="hide-blocks-btn" title="<?= get_string('show-hide-blocks-text', 'theme_elf_bs'); ?>"><img src="<?php echo $OUTPUT->image_url('blocks_hide', 'theme_elf_bs'); ?>" 
						data-show="<?php echo $OUTPUT->image_url('blocks_hide', 'theme_elf_bs'); ?>"
						data-hidden="<?php echo $OUTPUT->image_url('blocks_show', 'theme_elf_bs'); ?>"
						alt=""/></a>
				</div>
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
