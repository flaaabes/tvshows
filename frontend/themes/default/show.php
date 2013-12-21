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