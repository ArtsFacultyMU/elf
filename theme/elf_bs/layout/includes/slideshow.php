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
 * The Elegance theme is built upon  Bootstrapbase 3 (non-core).
 *
 * @package    theme
 * @subpackage theme_elegance
 * @author     Julian (@moodleman) Ridden
 * @author     Based on code originally written by G J Bernard, Mary Evans, Bas Brands, Stuart Lamour and David Scotson.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

for($i = 1; $i < 11; $i++) {
    $slides[$i] = new stdClass;
    $slides[$i]->enabled = (!empty($PAGE->theme->settings->{'enablebanner'.$i}));
    $slides[$i]->hasImage = (!empty($PAGE->theme->settings->{'bannerimage'.$i}));
    if ($slides[$i]->hasImage) {
        $slides[$i]->image = $PAGE->theme->setting_file_url('bannerimage'.$i, 'bannerimage'.$i);
    }
    $slides[$i]->hasBackgroundImage = (!empty($PAGE->theme->settings->{'bannerbackgroundimage'.$i}));
    if ($slides[$i]->hasBackgroundImage) {
        $slides[$i]->backgroundImage = $PAGE->theme->setting_file_url('bannerbackgroundimage'.$i, 'bannerbackgroundimage'.$i);
    }
    $slides[$i]->title = (empty($PAGE->theme->settings->{'bannertitle'.$i})) ? false : $PAGE->theme->settings->{'bannertitle'.$i};
    $slides[$i]->titleColor = (empty($PAGE->theme->settings->{'bannertitlecolor'.$i})) ? false : $PAGE->theme->settings->{'bannertitlecolor'.$i};
    $slides[$i]->caption = (empty($PAGE->theme->settings->{'bannertext'.$i})) ? false : $PAGE->theme->settings->{'bannertext'.$i};
    $slides[$i]->captionColor = (empty($PAGE->theme->settings->{'bannertextcolor'.$i})) ? false : $PAGE->theme->settings->{'bannertextcolor'.$i};
    $slides[$i]->url = (empty($PAGE->theme->settings->{'bannerlinkurl'.$i})) ? false : $PAGE->theme->settings->{'bannerlinkurl'.$i};
    $slides[$i]->urlText = (empty($PAGE->theme->settings->{'bannerlinktext'.$i})) ? false : $PAGE->theme->settings->{'bannerlinktext'.$i};
    $slides[$i]->urlTextColor = (empty($PAGE->theme->settings->{'bannerlinktextcolor'.$i})) ? false : $PAGE->theme->settings->{'bannerlinktextcolor'.$i};
    $slides[$i]->urlBackgroundColor = (empty($PAGE->theme->settings->{'bannerlinkbackgroundcolor'.$i})) ? false : $PAGE->theme->settings->{'bannerlinkbackgroundcolor'.$i};
    $slides[$i]->backgroundColor = (empty($PAGE->theme->settings->{'bannercolor'.$i})) ? "transparent" : $PAGE->theme->settings->{'bannercolor'.$i};
}

$hasslideshow = ($slides[1]->enabled || $slides[2]->enabled || 
        $slides[3]->enabled || $slides[4]->enabled || $slides[5]->enabled || 
        $slides[6]->enabled || $slides[7]->enabled || $slides[8]->enabled || 
        $slides[9]->enabled || $slides[10]->enabled) ? true : false;
?>

<?php if ($hasslideshow) { ?>
<div id="slider" class="banner has-dots" style="overflow: hidden; width: 100%; height: 236px;">
	<ul style="width: 400%; position: relative; left: -200%; height: 236px;">
            <?php $i = 0;?>
            <?php foreach($slides as $slide): ?>
                <?php $i++; ?>
		<?php if ($slide->enabled) { ?>
		<li id="slide<?= $i; ?>" style="<?php if($slide->hasBackgroundImage) echo "background-image: url(".$slide->backgroundImage." );"; ?> width: 25%; background-color:<?= $slide->backgroundColor?>;">
			<?php if($slide->hasImage) :?>
				<img src="<?= $slide->image;?>" />
			<?php endif; ?>
			<h1 style="color:<?=$slide->titleColor;?>;"><?php echo $slide->title ?></h1>
			<p style="color:<?=$slide->captionColor;?>;"><?php echo $slide->caption ?></p>
			<?php if ($slide->urlText) { ?>
				<a class="btn" href="<?php echo $slide->url ?>" style="color: <?= $slide->urlTextColor;?>; background-color: <?= $slide->urlBackgroundColor?>;" target="_blank"><?php echo $slide->urlText ?></a>
			<?php } ?>
		</li>
		<?php } ?>
            <?php endforeach;?>
	</ul>
</div>
<?php } ?>
