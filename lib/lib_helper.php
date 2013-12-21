<?php
    
    class LibHelper{
        const GENERAL_ERROR_DIR = '/Users/rmoedder/Projekte/tvshows/log/debug.log'; 

        function __construct(){
            Global $db;
            $this->db = $db;
        }

        public function log($msg) { 
            $date = date('d.m.Y h:i:s'); 
            error_log($date.'   |   '.$msg."\n", 3, self::GENERAL_ERROR_DIR); 
        } 
        
        public function parse($code,$values){
            while (list($key, $val) = each($values)) {
                $key = '#'.$key.'#';
                $code = preg_replace('/'.$key.'/',$val,$code);
            }
            return($code);
        }
    } 

?>