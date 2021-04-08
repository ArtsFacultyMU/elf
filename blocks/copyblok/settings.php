<?php
 
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext(
    'copyblok/holders', 
    get_string ( 'labelholders' , 'block_copyblok' ) , 
    get_string ( 'descholders' , 'block_copyblok' ) , 
    get_string ( 'holders' , 'block_copyblok' ) ));

    $settings->add(new admin_setting_configtext(
    'copyblok/holdersplace', 
    get_string ( 'labelholdersplace' , 'block_copyblok' ) , 
    get_string ( 'descholdersplace' , 'block_copyblok' ) , 
    get_string ( 'holdersplace' , 'block_copyblok' ) ));

    $settings->add(new admin_setting_configtext(
    'copyblok/webname', 
    get_string ( 'labelwebname' , 'block_copyblok' ) , 
    get_string ( 'descwebname' , 'block_copyblok' ) , 
    'PROEFES' ));
}

