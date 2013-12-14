<?php

    ini_set('display_errors',1);
    error_reporting(E_ALL);
    date_default_timezone_set('Europe/Berlin');
    
    $basepath = __DIR__;

    $param1 = $argv[1];
    
    require_once($basepath.'/../config/config.inc.php');
    require_once($basepath.'/../lib/lib_mediaobject.php');
    require_once($basepath.'/../lib/lib_helper.php');
    require_once($basepath.'/../lib/lib_database.php');
    require_once($basepath.'/../lib/lib_directory.php');
    require_once($basepath.'/../lib/lib_options.php');

    $helper     = new LibHelper();
    $db         = new LibDatabase($dbs);
    $options    = new LibOptions();
    
    $media_objects = Array();
    $media_directory = $options->get('media_directory');
    
    $md = new LibDirectory($media_directory);
    $md->add_filetype('avi');
    $md->add_filetype('mkv');
    $md->load_all_files();
    $filelist = $md->get_filelist();

    $files_existing = 0;
    $files_new      = 0;

    foreach($filelist as $file){
        $media_object = new LibMediaObject();
        $media_object->set_metadata('filename',$file['basename']);
        $media_object->set_metadata('filetype',$file['extension']);
        $media_object->set_metadata('path',$file['dirname']);
        if($param1 != 'dryrun'){
            if($media_object->save(TRUE))
                $files_new++;
            else
                $files_existing++;
        }else{
            var_dump($media_object);
        }
    }
    
    $helper->log('EXISTING FILES: '.$files_existing);
    $helper->log('NEW FILES:      '.$files_new);

    echo $files_new.'*'.$files_existing;
 
?>