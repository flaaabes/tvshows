<?php
    ini_set('display_errors',1);
    error_reporting(E_ALL && ~E_NOTICE);
    date_default_timezone_set('Europe/Berlin');

    require_once('../lib_helper.php');
    
    require_once('../../config/config.inc.php');
    require_once('../lib_mediaobject.php');
    require_once('../lib_helper.php');
    require_once('../lib_database.php');
    require_once('../lib_directory.php');
    require_once('../lib_options.php');

    $helper  = new LibHelper();
    $db      = new LibDatabase($dbs);
    $options = new LibOptions();

    # 3RD PARTY MODULE SETUP: TVDB #####################################
    define('TVDB_URL',$options->get('tvdb_url'));
    define('TVDB_API_KEY',$options->get('tvdb_api_key'));
    include __DIR__ . '/../../3rd_party/Moinax/TvDb/CurlException.php';
    include __DIR__ . '/../../3rd_party/Moinax/TvDb/Client.php';
    include __DIR__ . '/../../3rd_party/Moinax/TvDb/Serie.php';
    include __DIR__ . '/../../3rd_party/Moinax/TvDb/Banner.php';
    include __DIR__ . '/../../3rd_party/Moinax/TvDb/Episode.php';
    use Moinax\TvDb\Client;
    $tvdb = new Client(TVDB_URL, TVDB_API_KEY);
    $serverTime = $tvdb->getServerTime();
    #####################################################################

    $query = $_POST['q'];    
    
    $theme   = $options->get('theme');
    $basedir = $options->get('base_directory');
    
    $themepath = $basedir.'frontend/themes/'.$theme;
    $partial = $themepath.'/partials/'.$_POST['p'].'.php';

    $partial_code = file_get_contents($partial);

    $helper->log($query);
    $data = $tvdb->getSeries($query,'de');
    
    $values = Array();
    
    foreach($data as $show){
        $values['name'] = $show->name;
        $values['description'] = $show->overview;
        if($show->banner)
        $values['banner'] = $show->banner;
        echo $helper->parse($partial_code,$values);
    }
?>