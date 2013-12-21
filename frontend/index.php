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
    
    if(isset($_GET['show']) && !isset($_GET['season'])){
        $tv_show    = $db->load_object_by_id('tv_shows',$_GET['show']);
        $seasons    = $db->load_seasons($_GET['show']);
        $episodes   = $db->load_objects_by_column('tv_files','show_id',$_GET['show'],'season_num,episode_num');
        $page       = 'show.php';
    }elseif(isset($_GET['show']) && isset($_GET['season'])){
        $tv_show    = $db->load_object_by_id('tv_shows',$_GET['show']);
        $seasons    = $db->load_seasons($_GET['show']);
        $episodes   = $db->load_season($_GET['show'],$_GET['season']);
        $page       = 'show.php';
    }else{
        $tv_shows   = $db->load_all_objects('tv_shows',array('id','banner'),'show_name');
        $page       = 'dashboard.php';
    }
    
    # SETUP THEME    
    $theme = $options->get('theme');
    if(empty($theme))
        $theme = 'default';
    
    $themepath = 'themes/'.$theme.'/';
    include($themepath.'index.php');
    
?>