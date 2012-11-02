<?php

include_once 'm/mWidget.php';

class mMenu {
    
    public $pages = array();
    public $defaultPage;
    public $skeleton = null;
    
    public function add($title, $page, $default = false)
    {
        if ($default or count($this->pages) == 0) {
            $this->defaultPage = $title;
        }
        $this->pages[$title] = $page;
    }
    
    public function manageShow()
    {
        $viewPage = $this->defaultPage;
        
        if (isset($_GET['page'])) {
            if (array_key_exists($_GET['page'], $this->pages)) {
                $viewPage = $_GET['page'];
            }
        }
        
        if ($this->skeleton) {
            $this->skeleton->add($this->pages[$viewPage]);
            
            $this->iterateChildren($this->skeleton);
            
            $this->skeleton->show();
        } else {
            $this->pages[$viewPage]->show();
        }
    }
    
    public function iterateChildren($template)
    {
        foreach ($template->headerElements as $location) {
            $this->skeleton->setHeaderElement($location);
        }
        
        foreach ($template->children as $child) {
            $this->iterateChildren($child);
        }
    }
    
    public function generate()
    {
        $widget = new mWidget('mMenu', 'm/template/menu.xml');
        
        $ul = $widget->doc->getElementsByTagName('ul')->item(0);
        
        foreach ($this->pages as $title => $page) {
            $li = $ul->appendChild($widget->doc->createElement('li'));
            $li->setAttribute('class', 'btn btn-large btn-primary');
            $a = $li->appendChild($widget->doc->createElement('a'));
            $a->setAttribute('href', '?page='.$title);
            $a->appendChild($widget->doc->createTextNode($title));
        }
        
        return $widget;
    }
    
    public function addSkeleton($skeleton)
    {
        $this->skeleton = $skeleton;
    }
}


?>
