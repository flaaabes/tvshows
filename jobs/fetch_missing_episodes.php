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
     
    $shows = $db->load_all_objects('tv_shows');   
    
    foreach($shows as $show){
        $data = $tvdb->getSerieEpisodes($show['tvdb_id'],'de');

        foreach($data['episodes'] as $episode){
            $db_episode = $db->load_episode($show['id'],$episode->season,$episode->number);
            if(!$db_episode){
                $new_episode = Array();
                $new_episode['show_id']     = $show['id'];
                $new_episode['season_num']  = $episode->season;
                $new_episode['episode_num'] = $episode->number;
                $new_episode['episode_name']        = $episode->name;
                $new_episode['airdate']     = $episode->firstAired;
                $db->insert($new_episode,'tv_files');
                echo ".";
            }
        }
    }

?>
