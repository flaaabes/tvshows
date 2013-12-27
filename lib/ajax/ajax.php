<?php
    ini_set('display_errors',1);
    error_reporting(E_ALL && ~E_NOTICE);
    date_default_timezone_set('Europe/Berlin');

    require_once('../../config/config.inc.php');
    require_once('../lib_helper.php');
    require_once('../lib_database.php');
    require_once('../lib_mediaobject.php');
    require_once('../lib_options.php');
    
    $helper         = new LibHelper;
    $db             = new LibDatabase($dbs);
    $options        = new LibOptions();
    
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
    ####################################################################
        
    $method         = $_POST['m'];
    $paramString    = $_POST['p'];

    $params         = preg_split('/\*/',$paramString);
    
    call_user_func($method,$params[0]);
    
    function add_show($showName){
        GLOBAL $helper, $db, $options, $tvdb;
        # CHECK IF SHOW EXISTS
        if(!$db->load_objects_by_column('tv_shows','show_name',$showName)){
            $helper->log('not found');
            $img_basedir = $options->get('banner_directory');
            $tvdb_url    = $options->get('tvdb_url');
            
            $serverTime = $tvdb->getServerTime();
            $data = $tvdb->getSeries($showName,'de');
            if(!empty($data)){
                # ADD SHOW, UPDATE MEDIA-OBJECT
                $tvshow['id'] = $db->get_new_table_id('tv_shows');
                
                $tvshow['show_name'] = $data[0]->name;
                if($data[0]->firstAired)
                    $tvshow['date']      = $data[0]->firstAired->format('Y-m-d');
                $tvshow['tvdb_id']   = $data[0]->id;
                $tvshow['imdb_id']   = $data[0]->imdbId;
                $tvshow['banner']    = $data[0]->banner;
                $tvshow['synopsis']  = $data[0]->overview;
                
                # LOAD SHOW-BANNER
                $image_path = $img_basedir.$tvshow['banner'];
                $helper->log($image_path);
                if(!file_exists($image_path)){
                    $img_url = $tvdb_url.'/banners/_cache/'.$tvshow['banner'];
                    $helper->log($img_url);
                    file_put_contents($image_path, fopen($img_url, 'r'));
                }
                
                # GET & LOAD SHOW-POSTER
                $poster = $tvdb->getBanners($tvshow['tvdb_id'],'poster');
                $tvshow['poster'] = $poster[0]->path;
        
                $image_path = $img_basedir.$tvshow['poster'];
                $helper->log($image_path);
                if(!file_exists($image_path)){
                    $img_url = $tvdb_url.'/banners/_cache/'.$tvshow['poster'];
                    $helper->log($img_url);
                    file_put_contents($image_path, fopen($img_url, 'r'));
                }
                
                $db->insert($tvshow,'tv_shows');
                
                # GET EPISODES
                $data = $tvdb->getSerieEpisodes($tvshow['tvdb_id'],'de');

                foreach($data['episodes'] as $episode){
                    $db_episode = $db->load_episode($tvshow['id'],$episode->season,$episode->number);
                    if(!$db_episode){
                        $new_episode = Array();
                        $new_episode['show_id']     = $tvshow['id'];
                        $new_episode['season_num']  = $episode->season;
                        $new_episode['episode_num'] = $episode->number;
                        $new_episode['episode_name']        = $episode->name;
                        $new_episode['airdate']     = $episode->firstAired;
                        $db->insert($new_episode,'tv_files');
                    }
                }
            }
            

            
        } else{
            $helper->log('found - not adding');
        }
    }

?>