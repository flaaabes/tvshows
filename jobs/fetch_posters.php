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

    # 3RD PARTY MODULE SETUP: TVDB #####################################
    define('TVDB_URL',$options->get('tvdb_url'));
    define('TVDB_API_KEY',$options->get('tvdb_api_key'));
    include __DIR__ . '/../3rd_party/Moinax/TvDb/CurlException.php';
    include __DIR__ . '/../3rd_party/Moinax/TvDb/Client.php';
    include __DIR__ . '/../3rd_party/Moinax/TvDb/Serie.php';
    include __DIR__ . '/../3rd_party/Moinax/TvDb/Banner.php';
    include __DIR__ . '/../3rd_party/Moinax/TvDb/Episode.php';
    use Moinax\TvDb\Client;
    $tvdb = new Client(TVDB_URL, TVDB_API_KEY);
    $serverTime = $tvdb->getServerTime();
    #####################################################################

    $img_basedir = $options->get('banner_directory');
    $tvdb_url    = $options->get('tvdb_url');
    
    $db_images = $db->load_all_objects('tv_shows');
    
    foreach($db_images as $image){
        $banner = $tvdb->getBanners($image['tvdb_id'],'poster');
        $image['poster'] = $banner[0]->path;
        $db->update($image,'tv_shows');
        echo ".";
        
        $image_path = $img_basedir.$image['poster'];
        echo $image_path."\n";
        if(!file_exists($image_path)){
            $img_url = $tvdb_url.'/banners/_cache/'.$image['poster'];
            echo $img_url."\n";
            file_put_contents($image_path, fopen($img_url, 'r'));
        }
    }
     
?> 