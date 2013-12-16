<?php
    
    ini_set('display_errors',1);
    error_reporting(E_ALL && ~E_NOTICE);
    date_default_timezone_set('Europe/Berlin');

    require_once('config/config.inc.php');
    require_once('lib/lib_mediaobject.php');
    require_once('lib/lib_helper.php');
    require_once('lib/lib_database.php');
    require_once('lib/lib_directory.php');
    require_once('lib/lib_options.php');

    $helper      = new LibHelper();
    $db          = new LibDatabase($dbs);
    $options     = new LibOptions();
    $img_basedir = $options->get('banner_directory');
    $tvdb_url    = $options->get('tvdb_url');
    
    # GET BANNER FOR SHOW
    $banner = $tvdb->getBanners($tvshow['tvdb_id'],'poster');
    $tvshow['poster'] = $banner[0]->path;
    $db->update($tvshow,'tv_shows');
    
    $db_images = $db->load_all_objects('tv_shows',array('banner'));
    
    foreach($db_images as $image){
        $image_path = $img_basedir.$image['banner'];
        if(!file_exists($image_path)){
            $img_url = $tvdb_url.'/banners/_cache/'.$image['banner'];
            echo $img_url."\n";
            file_put_contents($image_path, fopen($img_url, 'r'));
        }
    }
    
?> 