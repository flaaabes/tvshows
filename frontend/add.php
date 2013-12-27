<?php include ('../lib/frontend/core.php'); ?>
<?php include ($themepath.'header.php'); ?>

<div class="box full settingsbox">
    <div>
        Enter name of show: 
        <input type="text" id="add_searchShow">
        <input type="button" class="remote_update" value="search" data-form="add_searchShow" data-url="ajax_search.php" data-update="results" data-partial="_search_results">
    </div>
</div>
<div id="results"></div>
<?php include ($themepath.'footer.php'); ?>