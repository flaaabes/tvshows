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
    }elseif(isset($_GET['show']) && isset($_GET['season'])){
        $tv_show    = $db->load_object_by_id('tv_shows',$_GET['show']);
        $seasons    = $db->load_seasons($_GET['show']);
        $episodes   = $db->load_season($_GET['show'],$_GET['season']);
        #$episodes   = $db->load_objects_by_column('tv_files','show_id',$_GET['show'],'season_num,episode_num');
    }else{
        $tv_shows   = $db->load_all_objects('tv_shows',array('id','banner'));
    }
?>
<html>
    <head>
        <title>TVSHOWS</title>
        <link rel="stylesheet" href="/tvshows/frontend/css/base.css" type="text/css" media="all" />
        <link rel="javascript" href="/tvshows/frontend/js/jquery-1.10.2.min.js" type="text/js">
    </head>
    <body>
        <div class="wrapper">
            <div class="box full"><h1><a href="/tvshows/frontend/" style="text-decoration: none; color: #000;">TV-SHOWS</a></h1></div>
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
                        <p><?php echo $tv_show['synopsis']; ?></p>
                    </div>

                    <div class="box"><h2>Staffel:</h2></div>
                    <?php foreach($seasons as $season) { ?>
                        <?php if($season['season_num'] != 0){ ?>
                            <div class="season_link"><a href="?show=<?php echo $_GET['show']; ?>&season=<?php echo $season['season_num']; ?>"><?php echo $season['season_num']; ?></a></div>
                        <?php } ?>
                    <?php } ?>
                    <div class="season_link" style="width: 100px;"><a href="?show=<?php echo $_GET['show']; ?>">ALLE</a></div>
                    
                    <div class="box"><h2>Episoden:</h2></div>
                    <?php foreach ($episodes as $episode) { ?>
                        <?php 
                            $subclass = ""; 
                            $subline  = $episode['filename'];
                            if(!$episode['filename']) { 
                                $subline = $episode['airdate']; 
                                $subclass = "na"; 
                            }
                        ?>
                        <?php if($episode['season_num'] != 0){ ?>
                        <div class="boxsmall episodebox">
                            <div class="season <?php echo $subclass; ?>"><?php echo $episode['season_num']; ?></div>
                            <div class="episodeinfo">
                                <p><?php echo $episode['episode_name']; ?><br>
                                <small><?php echo $subline ?></small>
                                <?php if($episode['filetype'] == "mkv"){ ?>
                                    <span style="float: right;"><small>HD</small></span>
                                <?php } ?>
                                </p>
                            </div>
                            <div class="episodenum <?php echo $subclass; ?>"><?php echo $episode['episode_num']; ?></div>
                        </div>
                        <?php } ?>
                    <?php } ?>
                    <div class="box"><h2>&nbsp;</h2></div>
                </div>
            <?php } else { ?>
                <div clasS="box full showlist">
                    <?php foreach($tv_shows as $tv_show){ ?><a href="?show=<?php echo $tv_show['id']; ?>"><img src="/tvshows/frontend/media_images/banners/<?php echo $tv_show['banner']; ?>"></a><?php } ?>
                </div>
            <?php } ?>
        </div>
    </body>
</html>