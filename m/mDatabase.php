<?php

include_once 'm/mDatabaseTable.php';
include_once 'm/mDatabaseObject.php';

class mDatabase {
    
    public $address;
    public $username;
    public $password;
    public $database;
    public $tables = array();
    public $useDatabase = true;
    
    public function __construct($address, $username, $password, $database)
    {
        $this->address = $address;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }
    
    public function addTable($tableName)
    {
        $table = new mDatabaseTable($tableName);
        $this->addTableObject($tableName, $table);
    }

    public function addTableObject($tableName, $table)
    {
        $this->tables[$tableName] = $table;
    }
    
    public function connect($sql, $exec = true)
    {
        $connection = mysql_connect($this->address,$this->username,$this->password);
        $return = new mDatabaseObject();
        $return->sql = $sql;
        $return->exec = $exec;
        
        if ($connection) {
            if ($this->useDatabase) {
                $db_selected = mysql_select_db($this->database);
                if (!$db_selected) {
                    return $this->createDatabase($sql, $exec);
                }
            }
            

            $result = mysql_query($sql, $connection);
                
            if (!$result) {
                $return->addStatus(false, '!result fail '.mysql_error());
            } else {
                $return->addStatus(true, 'success');
                if ($exec) {
                    while($row = mysql_fetch_array($result)) {
                        $object = new mDatabaseObject();
                        $object->id = $row['id'];
                        $object->mid = $row['mid'];
                        $object->object = unserialize($row['object']);
                        
                        $return->addObject($object);
                    }
                }
            }
            
            mysql_close($connection);
        } else {
            $return->addStatus(false, 'connection fail '.mysql_error());
        }

        return $return;
    }
    
    public function createDatabase($sql, $exec)
    {
        $sql2 = 'CREATE DATABASE IF NOT EXISTS `'.$this->database.'`;';
        $this->useDatabase = false;
        $return = $this->connect($sql2, false);
        $this->useDatabase = true;
        if ($return->success) {
            return $this->connect($sql, $exec);
        }
        return $return;
    }
}

?>
