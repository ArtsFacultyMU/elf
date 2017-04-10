<?php $askCookies = !(isset($_COOKIE['cookiepolicy'])&&$_COOKIE['cookiepolicy']);?>
<div class="footer-wrapper">
	<footer id="page-footer" class="<?= ($askCookies)?'allow-cookies':'';?>">
		<div class="container-fluid">
		<?php echo $OUTPUT->footer_html($footer_data); ?>
		</div>
	</footer>
</div>
<a href="#" id="scroll-up" style="display:none;" class="<?= ($askCookies)?'allow-cookies':'';?>"></a>
<?php if($askCookies) :?>
	<div id="allow-cookies">
		<?php echo get_string('accept-cookies-text', 'theme_elf_bs'); ?>
		<a href="#" class="button" title="<?php echo get_string('accept-cookies', 'theme_elf_bs'); ?>"><?php echo get_string('accept-cookies', 'theme_elf_bs'); ?></a>
	</div>
<?php endif; ?>