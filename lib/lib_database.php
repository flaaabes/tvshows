<?php

    class LibDatabase{
        
        var $connection;  
        var $helper;  
        var $dbs;
                
        function __construct($dbs){
            Global $helper;
            $this->helper = $helper;
            $this->dbs = $dbs;
            
            if($this->connection == NULL){
                $this->connection = mysql_connect($this->dbs['hostname'],$this->dbs['username'],$this->dbs['password']) or die('Failed to connect to db: '.$this->helper->log(mysql_error()));
                mysql_select_db($this->dbs['database'],$this->connection);
            }
        }
        
        public function get_new_table_id($table){
            $db_obj = mysql_query('SELECT max(`id`) FROM '.$table) or die($this->helper->log(mysql_error()));
            $result = mysql_fetch_row($db_obj);
            return($result[0]+1);
        }
        
        public function load_object_by_id($table,$id){
            $db_obj = mysql_query('SELECT * FROM '.$table.' WHERE `id` = '.$id);
            $result = mysql_fetch_array($db_obj,MYSQL_ASSOC);
            return($result);
        }
        
        public function load_object_by_column($table,$column,$value,$order=FALSE){
            $orderby = '';
            if($order){
                $orderby = ' ORDER BY '.$order;
            }
            $statement = 'SELECT * FROM '.$table.' WHERE `'.$column.'` = "'.$value.'"'.$orderby;
            $db_obj = mysql_query($statement);
            $result = mysql_fetch_array($db_obj,MYSQL_ASSOC);
            return($result);
        }

        public function load_objects_by_column($table,$column,$value,$order=FALSE){
            $orderby = '';
            if($order){
                $orderby = ' ORDER BY '.$order;
            }
            $statement = 'SELECT * FROM '.$table.' WHERE `'.$column.'` = "'.$value.'"'.$orderby;
            $db_obj = mysql_query($statement);
            while($row = mysql_fetch_array($db_obj,MYSQL_ASSOC)){
                $rows[] = $row;
            }
            return($rows);
        }
        
        public function load_objects_by_sql($sql){
            $db_obj = mysql_query($sql) or die($this->helper->log('Error: '.mysql_error()));
            while($row = mysql_fetch_array($db_obj,MYSQL_ASSOC)){
                $rows[] = $row;
            }
            return($rows);
        }
        
        public function load_all_objects($table,$columns=FALSE,$order=FALSE){
            $column = '*';
            $orderby = '';
            if($columns){
                if(is_array($columns)){
                    $column = '';
                    foreach($columns as $col){
                        $column .= ','.$col;
                    }
                    $column = substr($column,1);
                }else{
                    $column = $columns;
                }
            }
            if($order){
                $orderby = ' ORDER BY '.$order;
            }
            $statement = 'SELECT '.$column.' FROM '.$table.$orderby;
            $this->helper->log($statement);
            $db_obj = mysql_query($statement) 
                or die($this->helper->log('Error: '.$statement.' '.mysql_error()));
            while($row = mysql_fetch_array($db_obj,MYSQL_ASSOC))
                $rows[] = $row;
            return($rows);
        }
        
        public function insert($object,$table){
            $update_cols = '';
            $update_vals = '';
            while(list($column,$value) = each($object)) {
                $update_cols .= ',`'.$column.'`';
                $update_vals .= ',\''.mysql_real_escape_string($value).'\'';
            }
            $update_cols = '('.substr($update_cols,1).')';
            $update_vals = '('.substr($update_vals,1).')';
        
            $insert_string = 'INSERT INTO '.$table.' '.$update_cols.' VALUES '.$update_vals;
            
            if(mysql_query($insert_string))
                $this->helper->log('new object inserted');
            else
                $this->helper->log($insert_string.'  -  '.mysql_error());
        }
        
        public function update($object,$table){
            $object = array_filter($object);
            $update_string = '';
            while(list($column,$value) = each($object)) {
                $update_string .= ',`'.$column.'` = \''.mysql_real_escape_string($value).'\'';
            }
            
            $update_string = 'UPDATE '.$table.' SET '.substr($update_string,1).' WHERE id = '.$object['id'];   
            
            if(mysql_query($update_string))
                $this->helper->log('object updated');
            else
                $this->helper->log($update_string.'  -  '.mysql_error());
        }  
        
        public function get_incomplete_shows($limit=FALSE){
            $incomplete_shows = Array();
            $statement = 'SELECT * FROM tv_files WHERE `show_id` IS NULL'; #OR `episode_id` IS NULL
            if($limit){
                $statement .= ' LIMIT '.$limit;
            }
            $db_obj = mysql_query($statement) or die($this->helper->log(mysql_error()));
            while($row = mysql_fetch_array($db_obj,MYSQL_ASSOC))
                $incomplete_shows[] = $row;
            return $incomplete_shows;
        }      
        
    }
    
?>