
<?php

class mDatabaseObject {
    
    public $id;
    public $mid = 0;
    
    public $success;
    public $message = 'No message';
    
    public $object;
    public $objects = array();
    
    public $sql;
    public $exec;
    
    public function addObject($object)
    {
        array_push($this->objects, $object);
    }
    
    public function addStatus($success, $message)
    {
        $this->success = $success;
        $this->message = $message;
    }
}

?>
    
