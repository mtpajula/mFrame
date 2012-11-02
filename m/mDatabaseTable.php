<?php

include_once 'm/mDatabaseObject.php';

class mDatabaseTable {
    
    public $table;
    public $database;
    public $tableCreated = false;
    
    public function __construct($table, $database)
    {
        $this->table = $table;
        $this->database = $database;

    }

    public function connect($sql, $exec = true)
    {
        if ($this->tableCreated) {
            return $this->database->connect($sql, $exec);
        } else {
            $dbObject = $this->createTable();
            if ($dbObject->success) {
                $this->tableCreated = true;
                return $this->database->connect($sql, $exec);
            }
            return $dbObject;
        }
    }
    
    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` ( ';
        $sql .= '`id` int(11) NOT NULL AUTO_INCREMENT, ';
        $sql .= '`mid` int(11), ';
        $sql .= '`object` TEXT, ';
        $sql .= '`time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ';
        $sql .= 'PRIMARY KEY (`id`)';
        $sql .= ');';
        return $this->database->connect($sql, false);
    }
    
    public function dropTable()
    {
        $sql = 'DROP TABLE IF EXISTS '.$this->table.';';
        return $this->database->connect($sql, false);
    }
    
    public function queryAll()
    {
        $sql = 'SELECT * FROM `'.$this->table.'`;';
        return $this->connect($sql);
    }
    
    public function queryId($id)
    {
        $sql = 'SELECT * FROM `'.$this->table.'` ';
        $sql .= 'WHERE `id` = '.$id.'';
        return $this->connect($sql);
    }
    
    public function updateObject($dbObject)
    {
        $sql = 'UPDATE `'.$this->table.'` ';
        $sql .= 'SET `object` = \''.serialize($dbObject->object).'\', ';
        $sql .= '`mid` = \''.$dbObject->mid.'\' ';
        $sql .= 'WHERE `id` = '.$dbObject->id.';';
        return $this->connect($sql, false);
    }
    
    public function deleteObject($id)
    {
        $sql = 'DELETE FROM `'.$this->table.'` ';
        $sql .= 'WHERE `id` = '.$id.';';
        return $this->connect($sql, false);
    }
    
    public function insertObject($dbObject)
    {
        $sql = "INSERT INTO `".$this->table."` (
        `id` ,
        `mid` ,
        `object` ,
        `time`)
        VALUES (
        NULL, ".$dbObject->mid.", '".serialize($dbObject->object)."',
        CURRENT_TIMESTAMP
        );";
        
        return $this->connect($sql, false);
    }

    public function insertData($object)
    {
        $dbObject = new mDatabaseObject();
        $dbObject->object = $object;
        return $this->insertObject($dbObject);
    }
    
    public function updateData($id, $object)
    {
        $dbObject = new mDatabaseObject();
        $dbObject->id = $id;
        $dbObject->object = $object;
        return $this->updateObject($dbObject);
    }
}

?>
