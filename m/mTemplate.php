<?php

class mTemplate {
    
    public $doc;
    public $title = '';
    public $content = '';
    
    public $children = array();
    
    public $userLevel = 0;
    
    public $css = array();
    public $js = array();
    public $headerElements = array();
    
    public function __clone() {
        $this->doc = clone $this->doc;
    }

    public function addTemplateFile($file, $data = null) {
        
        if(!file_exists($file)) {
            echo '<p>'.$this->title.': Ei tiedostoa '.$file.'</p>';
        }
        
        $this->doc = new DOMDocument();
        ob_start();
        include $file;
        $myvar = ob_get_contents();
        ob_end_clean();
        
        #$success = $this->doc->loadHTML($myvar);
        $success = true;
        
        $this->doc->loadHTML($myvar);
        $xpath = new DOMXPath($this->doc);
        $body = $xpath->query('/html/body/*');
        $this->doc->loadXML($this->doc->saveXML($body->item(0)));
        
        #$success = $this->doc->loadXML($myvar);
        
        if (!$success) {
            
            #echo $this->doc->saveXML();
            
            #echo '<p>No luck</p>';
            $this->doc->loadHTML($myvar);
            $xpath = new DOMXPath($this->doc);
            $body = $xpath->query('/html/body/*');
            $this->doc->loadXML($this->doc->saveXML($body->item(0)));
            
            #echo $this->doc->saveXML();
        }
        
        /*
        try {
            $this->doc->loadXML($myvar);
        } catch (Exception $e) {
            echo '<p>catch: '.$e.'</p>';
            $this->doc->loadHTML($myvar);
            $this->doc->loadXML($this->doc->saveXML());
        }
        
        if (!$this->doc->validate()) {
            echo '<p>valid error</p>';
            $this->doc->loadHTML($myvar);
            $this->doc->loadXML($this->doc->saveXML());
        }*/
    }
    
    public function xml2xhtml($xml) {
        return preg_replace_callback('#<(\w+)([^>]*)\s*/>#s', create_function('$m', '
            $xhtml_tags = array("br", "hr", "input", "frame", "img", "area", "link", "col", "base", "basefont", "param");
            return in_array($m[1], $xhtml_tags) ? "<$m[1]$m[2] />" : "<$m[1]$m[2]></$m[1]>";
        '), $xml);
    }

    
    public function show()
    {
        #echo $this->doc->saveXML();
        echo $this->doc->saveHTML();
    }
    
    public function showHTML()
    {
        echo $this->doc->saveHTML();
    }
    
    public function add($child)
    {
        array_push($this->children, $child);
        $childNode = $child->doc->documentElement;
        $childNode = $this->doc->importNode($childNode, true);

        $this->addInClass('div','mInclude',$childNode);
    }

    public function addInClass($elementName, $attrName, $childNode)
    {
        foreach ($this->doc->getElementsByTagName($elementName) as $element) {
            $attr = $element->getAttribute('class');
            if (strcmp($attr, $attrName) == 0) {

                $element->appendChild($childNode);
                return;
            }
        }
        
        $bodies = $this->doc->getElementsByTagName('body');
        
        
        if (is_object($bodies->item(0))) {
            $element = $bodies->item(0);
        } else {
            $element = $this->doc->documentElement;
        }
        
        $node = $element->appendChild($this->doc->createElement($elementName));
        $node->setAttribute('class',$attrName);
        $node->appendChild($childNode);
    }
    
    public function setHeaderElement($location)
    {
        if (!in_array($location, $this->headerElements)) {
            array_push($this->headerElements, $location);
        }
    }
}


?>
