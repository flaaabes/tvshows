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

    $helper  = new LibHelper();
    $db      = new LibDatabase($dbs);
    $options = new LibOptions();

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
        
    $episodes = $db->load_objects_by_sql('SELECT s.tvdb_id, f.id, f.filename FROM tv_files f, tv_shows s WHERE f.show_id = s.id');
    
    foreach($episodes as $episode){
        # FIRST VERSION: EXPECT NAME TO BE LIKE 
        # <SHOWNAME> - <EPISODE SXXEYY> - <OPTIONAL EPISODE-TITLE>.<EXTENSION>
        # SIMPLE / DIRTY
        # EXTRACT HERE AND CREATE METHOD FOR THAT.

        list($show_name,$seasonepisode,$episode_title) = preg_split('/ - /',$episode['filename']);
        $season = intval(substr($seasonepisode,1,2));
        $episode_num = intval(substr($seasonepisode,4,2));
        
        $file = $db->load_object_by_id('tv_files',$episode['id']);    
        $data = $tvdb->getEpisode($episode['tvdb_id'],$season,$episode_num,'de');
        
        $file['season_num'] = $season;
        $file['episode_num'] = $episode_num;    
        $file['episode_name'] = $data->name;
        $file['episode_synopsis'] = $data->overview;
        $file['episode_rating'] = $data->rating;
        $file['episode_thumb'] = $data->thumbnail;
        
        $db->update($file,'tv_files');
        echo ".";
    }
    
?>
