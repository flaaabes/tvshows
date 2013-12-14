<?php
    
    ###########################################################################
    #
    #   CLASS TO CREATE MEDIA-LIBRARY-OBJECT
    #
    ###########################################################################
    #
    #   METHODS:
    #       - set_metadata($metakey,$metavalue,$group=FALSE)
    #         -> Adds media-meta-data to object-properties
    #
    #       - load($id)
    #         -> loads media-library-object from database (by id)   
    #
    #       - load_by_name($filename)
    #         -> loads media-library-object from database (by filename)
    #
    #       - save()
    #         -> saves object to library 
    #         ->(insert or update depending on existing or not-existing id)
    #
    #       - dump_metadata
    #         -> var_dumps object
    #
    ###########################################################################

    
    class LibMediaobject{
        
        var $metadata = Array();
        var $db,$helper;
        var $table = 'tv_files';
    
        function __construct($query=FALSE){
            Global $db,$helper;
            $this->db = $db;
            $this->helper = $helper;
            
            if($query){
                if(gettype($query) == 'integer')
                    $this->load($query);
                elseif(gettype($query) == 'string')
                    $this->load_by_name($query);
            }
        }
        
        function __destruct(){
        
        }
        
        public function set_metadata($metakey, $metavalue, $group=FALSE){
            if($group)
                $this->metadata[$group][$metakey] = $metavalue;
            else
                $this->metadata[$metakey] = $metavalue;
        }
        
        public function load($id){
            $obj = $this->db->load_object_by_id($this->table,$id);
            if(empty($obj)){
                $this->helper->log('Object with id '.$id.' does not exist.');
            }else{
                $this->metadata = $obj;
            }
        }
        
        public function load_by_name($filename){
            $obj = $this->db->load_object_by_column($this->table,'filename',$filename);
            if(empty($obj)){
                $this->helper->log('Object with filename '.$filename.' does not exist.');
            }else{
                $this->metadata = $obj;
            }
        }
        
        public function save($check_existence=TRUE){
            if(isset($this->metadata['id'])){
                if ($this->check_minimum_requirements()){
                    $this->db->update($this->metadata,$this->table);
                    return TRUE;
                }
                else
                    $this->helper->log('can not save object: minimum requirements');
            }
            else{
                $this->get_new_object_id();
                if ($this->check_minimum_requirements()){
                    if($check_existence){
                        if(!$this->check_existence()){
                            $this->db->insert($this->metadata,$this->table);
                            return TRUE;
                        }
                    }
                }else{
                    $this->helper->log('can not save object: minimum requirements');
                }
            }
            return FALSE;                
        }
        
        public function check_existence(){
            if($this->db->load_object_by_column($this->table,'filename',$this->metadata['filename']))
                return TRUE;
            return FALSE;
        }        
        
        public function dump_metadata(){
            var_dump($this->metadata);
        }
        
        # PRIVATE METHODS
        
        private function get_new_object_id(){
            $this->set_metadata('id',$this->db->get_new_table_id($this->table));
        }
        
        private function check_minimum_requirements(){
            if (!empty($this->metadata['id']) && !empty($this->metadata['filename']))
                return TRUE;
            return FALSE;
        }
        
    }

?>