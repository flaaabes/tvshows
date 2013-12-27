<?php
    
    ini_set('display_errors',1);
    error_reporting(E_ALL && ~E_NOTICE);
    date_default_timezone_set('Europe/Berlin');

    require_once('../config/config.inc.php');
    require_once('../lib/lib_mediaobject.php');
    require_once('../lib/lib_helper.php');
    require_once('../lib/lib_database.php');
    require_once('../lib/lib_directory.php');
    require_once('../lib/lib_options.php');

    $helper      = new LibHelper();
    $db          = new LibDatabase($dbs);
    $options     = new LibOptions();

    # SETUP THEME    
    $theme = $options->get('theme');
    if(empty($theme))
        $theme = 'default';

    $themepath = 'themes/'.$theme.'/';
    
?>