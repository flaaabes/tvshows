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
            <div class="box full"><strong>TV-SHOWS</strong></div>
            <?php if (isset($_GET['show'])) { ?>
                <div class="box one_third">
                    <?php echo $tv_show['show_name']; ?>
                </div>
                <div class="box one_third">
                    <?php echo $tv_show['poster']; ?>
                </div>
                <div class="box two_third">
                    <strong>Synopsis:</strong><br>
                   <?php echo $tv_show['synopsis']; ?>
                </div>
            <?php } else { ?>
                <div clasS="box full">
                    <?php                   
                        $i=1;
                        foreach($tv_shows as $tv_show){
                            ?><a href="?show=<?php echo $tv_show['id']; ?>"><?php
                            if($i%3==0){
                                ?><img src="/tvshows/frontend/media_images/banners/<?php echo $tv_show['banner']; ?>" class="last"><?php                            
                            }else{
                                ?><img src="/tvshows/frontend/media_images/banners/<?php echo $tv_show['banner']; ?>"><?php
                            }
                            $i++;
                            ?></a><?php
                        }                    
                    ?>
                </div>
            <?php } ?>
        </div>
    </body>
</html>