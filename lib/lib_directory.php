<?php
    
    class LibDirectory{
        var $basedir;
        var $filelist = Array();
        var $filetypes = Array();
        var $helper;
        
        function __construct($basedir){
            Global $helper;
            $this->helper = $helper;
            $this->basedir = $basedir;
            $this->filetypes[] = '';
        }
        
        public function add_filetype($filetype){
            $this->filetypes[] = $filetype;
        }
        
        public function load_all_files($path=FALSE){
            if(!$path)
                $path = $this->basedir;
            
            if($handle = opendir($path)){
                while (false !== ($entry = readdir($handle))) {
                    if($entry != '.' && $entry != '..'){             
                        if(is_file($path.$entry)){
                            $newfile = preg_replace('/\/\//','/',$path.$entry);
                            $pathinfo = pathinfo($newfile);
                            if(array_search($pathinfo['extension'],$this->filetypes)){
                                $this->filelist[] = $pathinfo;
                            }
                        }else{
                            $this->load_all_files($path.'/'.$entry.'/');
                        }
                    }
                }
            }else{
                $this->helper->log('failed to open dir '.$path);
            }
        }
        
        public function get_filelist(){
            return($this->filelist);
        }
        
        public function dump_files(){
            var_dump($this->filelist);
        }
    }
    
?>