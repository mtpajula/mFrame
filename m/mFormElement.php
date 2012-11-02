<?php

class mFormElement {
    
    public $id;
    public $mid = '';
    public $type;
    public $title;
    public $name = '';
    public $xmlName;
    public $value;
    public $required;
    public $valid = true;
    public $options = array();
    public $listElement = false;
    public $checked = false;
    public $sanitizeAs = 'text';
    
    public $placeholder = null;
    
    public $save = true;
    
    public function addData($type, $title, $name, $value, $required)
    {
        if (strcmp('email', $type) == 0) {
            $this->sanitizeAs = $type;
        }
        
        $this->type = $type;
        $this->title = $title;
        $this->name = $name;
        $this->xmlName = $name;
        $this->value = $value;
        $this->required = $required;
    }
    
    public function addOption($title, $value)
    {
        $option = new mFormElement();
        $option->addData($this->type,$title,$this->name,$value,true);
        $option->xmlName = $this->name.'[]';
        $option->listElement = true;
        array_push($this->options, $option);
    }
}


?>
