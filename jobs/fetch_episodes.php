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
    
    /*
    $i=0;
    foreach($files as $file){

      
       $media_obj = $db->load_object_by_column('tv_shows','show_name',$show_name);
       $file['show_id'] = $media_obj['id'];
       
       if(!$media_obj){
           $data = $tvdb->getSeries($show_name,'de');
           if(empty($data)){
               # NOT FOUND, SET TO -1 AND CONTINUE
               $file['show_id'] = -1;
               $db->update($file,'tv_files');
               continue;
           }
           # ADD SHOW, UPDATE MEDIA-OBJECT
           $tvshow['id']        = $db->get_new_table_id('tv_shows');
           $tvshow['show_name'] = $data[0]->name;
           if($data[0]->firstAired)
               $tvshow['date']      = $data[0]->firstAired->format('Y-m-d');
           $tvshow['tvdb_id']   = $data[0]->id;
           $tvshow['imdb_id']   = $data[0]->imdbId;
           $tvshow['banner']    = $data[0]->banner;
           $tvshow['synopsis']  = $data[0]->overview;
           $db->insert($tvshow,'tv_shows');
           $file['show_id'] = $tvshow['id'];
       }
       
       # UPDATE MEDIA-OBJECT
       $db->update($file,'tv_files');
       echo ".";
       if($i%50==0){
           echo $i."\n";
       }
       $i++;
    }
    */
    
?>