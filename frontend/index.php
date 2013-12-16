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
    
    if(isset($_GET['show'])){
        $tv_show = $db->load_object_by_id('tv_shows',$_GET['show']);
        $episodes = $db->load_objects_by_column('tv_files','show_id',$_GET['show'],'season_num,episode_num');
    }else{
        $tv_shows = $db->load_all_objects('tv_shows',array('id','banner'));
    }
?>
<html>
    <head>
        <title>TVSHOWS</title>
        <link rel="stylesheet" href="/tvshows/frontend/css/base.css" type="text/css" media="all" />
    </head>
    <body>
        <div class="wrapper">
            <div class="box full"><h1>TV-SHOWS</h1></div>
            <?php if (isset($_GET['show'])) { ?>
                <div class="one_third">
                    <div class="box">
                        <h2><?php echo $tv_show['show_name']; ?></h2>
                    </div>
                    <div class="box">
                        <img src="/tvshows/frontend/media_images/banners/<?php echo $tv_show['poster']; ?>">
                    </div>
                </div>
                <div class="two_third">
                    <div class="box">
                        <h2>Inhalt:</h2>
                        <p><?php echo $tv_show['synopsis']; ?></p>
                    </div>
                    <div class="box">
                        <h2>Episoden:</h2>
                    </div>
                    <?php foreach ($episodes as $episode) { ?>
                        <div class="boxsmall episodebox">
                            <div class="season"><?php echo $episode['season_num']; ?></div>
                            <div class="episodeinfo">
                                <p><?php echo $episode['episode_name']; ?><br>
                                <small><?php echo $episode['filename']; ?></small></p>
                            </div>
                            <div class="episodenum"><?php echo $episode['episode_num']; ?></div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div clasS="box full showlist">
                    <?php foreach($tv_shows as $tv_show){ ?><a href="?show=<?php echo $tv_show['id']; ?>"><img src="/tvshows/frontend/media_images/banners/<?php echo $tv_show['banner']; ?>"></a><?php } ?>
                </div>
            <?php } ?>
        </div>
    </body>
</html>