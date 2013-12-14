<?php
    
    class LibOptions{
        var $db;
        
        function __construct(){
            Global $db;
            $this->db = $db;
        }

        public function get($option_key){
            $option_value = $this->db->load_object_by_column('tv_options','option_key',$option_key);
            return($option_value['option_value']);
        }
          
        public function set($option_key,$option_value){
            $db_object = $this->db->load_object_by_column('tv_options','option_key',$option_key);
            if($db_object){
                $db_object['option_value'] = $option_value;
                $this->db->update($db_object,'tv_options');
            }else{
                $db_object['option_key'] = $option_key;
                $db_object['option_value'] = $option_value;
                $this->db->insert($db_object,'tv_options');
            } 
        }
        
        public function rm($option_key){
            # TO BE IMPLEMENTED
        }
    
    }
    
?>