<?php

include_once 'mTemplate.php';

class mSkeleton extends mTemplate {
    
    public function __construct($title, $file = 'm/template/skeleton.xml') {
        
        $this->title = $title;
        $this->addTemplateFile($file);
        
        $head = $this->doc->getElementsByTagName('head')->item(0);
        $node = $head->appendChild($this->doc->createElement('title'));
        $node->appendChild($this->doc->createTextNode($title));
        
        $node2 = $head->appendChild($this->doc->createElement('meta'));
        $node2->setAttribute('charset','UTF-8');
        
    }
    
    public function addCSS($location)
    {
        $head = $this->doc->getElementsByTagName('head')->item(0);
        $node = $head->appendChild($this->doc->createElement('link'));
        
        $node->setAttribute('rel','stylesheet');
        $node->setAttribute('type','text/css');
        $node->setAttribute('href',$location);
    }
    
    public function setDefaultTheme()
    {
        $this->setHeaderElement('m/themes/default/main.css');
    }
    
    public function setBootstrapTheme()
    {
        $this->setHeaderElement('m/addons/bootstrap/libs/css/bootstrap.min.css');
        $this->setHeaderElement('m/themes/bootstrap/main.css');
        $this->setHeaderElement('m/addons/jquery-1.8.2.min.js');
        $this->setHeaderElement('m/addons/bootstrap/libs/js/bootstrap.min.js');
        
        $this->setHeaderElement('m/addons/bootstrap/bootstrap-wysihtml5-0.0.2.css');
        $this->setHeaderElement('m/addons/bootstrap/libs/js/wysihtml5-0.3.0_rc2.js');
        $this->setHeaderElement('m/addons/bootstrap/bootstrap-wysihtml5-0.0.2.js');
        $this->setHeaderElement('m/addons/bootstrap/default.js');
    }
    
    public function addJS($location)
    {
        $head = $this->doc->getElementsByTagName('head')->item(0);
        $node = $head->appendChild($this->doc->createElement('script'));
        $node->appendChild($this->doc->createTextNode(''));
        
        $node->setAttribute('src',$location);
    }
    
    public function show()
    {
        
        foreach ($this->headerElements as $location) {
            
            if (strcmp('DEFAULT', $location) == 0) {
                $this->setDefaultTheme();
            }
            
            $pieces = explode(".", $location);
            $fileType = $pieces[(count($pieces)-1)];
            
            if (strcmp('css', $fileType) == 0) {
                $this->addCSS($location);
            }
            
            if (strcmp('js', $fileType) == 0) {
                $this->addJS($location);
            }
        }
        
        parent::show();
    }
}


?>
