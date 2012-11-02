<?php

class mValidateSanitize {
    
    public $data = null;
    public $valid;
    
    public function setData($data)
    {
        $this->setData = $this->sanitizeStringKeyArray($data);
    }

    public function sanitizeStringKeyArray($array)
    {
        $return = array();
        
        foreach ($array as $key => $str) {
            $newkey = $this->sanitizeString($key);
            if (is_array($str)) {
                $newStr = array();
                foreach ($str as $subStr) {
                    $newSubStr = $this->sanitizeString($subStr);
                    array_push($newStr, $newSubStr);
                }
            } else {
                $newStr = $this->sanitizeString($str);
            }
            $return[$newkey] = $newStr;
        }
        
        return $return;
    }
    
    public function processFormElements($formElements)
    {
        $this->valid = true;
        
        foreach ($formElements as $formElement) {
            if (!$this->processFormElement($formElement)) {
                $this->valid = false;
            }
        }
        return $this->valid;
    }
    
    public function processArrayFormElement($formElement, $arr)
    {
        if ($formElement->required) {
            $formElement->valid = false;
        }
        foreach ($formElement->options as $subFormElement) {
            if (in_array($subFormElement->value, $arr)) {
                $subFormElement->checked = true;
                $formElement->valid = true;
            } else {
                $subFormElement->checked = false;
            }
        }
        
        return $formElement->valid;
    }
    
    public function processFormElement($formElement)
    {
        foreach ($this->data as $key => $str) {
            
            if (strcmp($key, $formElement->name) == 0) {
                
                if (is_array($str)) {
                    return $this->processArrayFormElement($formElement, $str);
                }
                
                $this->sanitizeElement($formElement, $str);
                return $this->validateElement($formElement);
            }
        }
        
        if (!empty($formElement->options) and !$formElement->required) {
            return $formElement->valid;
        }
        
        $formElement->valid = false;
        return $formElement->valid;
    }
    
    public function sanitizeElement($formElement, $str)
    {   
        if (strcmp('full', $formElement->sanitizeAs) == 0) {
            $formElement->value = $this->sanitizeString($str,true);
        } elseif (strcmp('email', $formElement->sanitizeAs) == 0) {
            $formElement->value = $this->sanitizeEmail($str);
        } else {
            $formElement->value = $this->sanitizeString($str);
        }
    }
    
    public function validateElement($formElement)
    {
        if (strcmp('email', $formElement->sanitizeAs) == 0) {
            if (!$this->validateEmail($formElement->value)) {
                $formElement->valid = false;
            }
        }

        if ($formElement->required) {
            if (strcmp($formElement->value, '') == 0) {
                $formElement->valid = false;
            }
        }

        return $formElement->valid;
    }
    
    public function sanitizeString($str, $full = false)
    {
        if ($full) {
            return htmlspecialchars($str);
            #return filter_var($str, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        return filter_var($str, FILTER_SANITIZE_STRING);
    }
    
    public function sanitizeEmail($str)
    {
        return filter_var($str, FILTER_SANITIZE_EMAIL);
    }
    
    public function validateEmail($str)
    {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }
}


?>
