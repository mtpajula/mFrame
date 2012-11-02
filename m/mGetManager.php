<?php

include_once 'm/mValidateSanitize.php';

class mGetManager {
    
    public $valSan;
    public $get;
    
    public function __construct()
    {
        $this->valSan = new mValidateSanitize();
        $this->get = $this->valSan->sanitizeStringKeyArray($_GET);
    }
    
    public function setKey($key, $value)
    {
        $this->get[$key] = $value;
    }
    
    public function unsetKey($key)
    {
        
        unset($this->get[$key]);
    }
    
    public function getAddress()
    {
        $symbol = '?';
        if (count($this->get) > 0) {
            $symbol = '&';
        }
        
        $symbol = '?';
        $address = '';
        foreach ($this->get as $key => $value) {
            $address .= $symbol.$key.'='.$value;
            $symbol = '&';
        }
        return $address;
    }
    
    public function isKeyValue($key, $value)
    {
        if ($this->isKey($key)) {
            if (strcmp($this->get[$key], $value) == 0) {
                return true;
            }
        }
        return false;
    }
    
    public function isKey($key)
    {
        if (array_key_exists($key, $this->get)) {
            return true;
        }
        return false;
    }
}

?>
