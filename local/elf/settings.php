<?php
$ADMIN->add('root', new admin_category('elf', get_string('pluginname','local_elf')));
$ADMIN->add('elf', new admin_externalpage('elf_migrate_user', get_string('migrateuser','local_elf'),
		$CFG->wwwroot.'/local/elf/migrate/index.php'));
$ADMIN->add('elf', new admin_externalpage('elf_resources', get_string('repairresources','local_elf'),
		$CFG->wwwroot.'/local/elf/restore/repair_links.php'));
$ADMIN->add('elf', new admin_externalpage('elf_modules', get_string('elfconfig','local_elf'),
		$CFG->wwwroot.'/local/elf/elfconfig/index.php'));
$ADMIN->add('elf', new admin_externalpage('elf_studies', get_string('studiessettings','local_elf'),
		$CFG->wwwroot.'/local/elf/studies/index.php'));