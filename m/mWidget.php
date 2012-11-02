<?php

include_once 'mTemplate.php';

class mWidget extends mTemplate {

    public function __construct($title, $file = 'm/template/widget.xml', $data = Null) {
        $this->title = $title;
        $this->addTemplateFile($file, $data);
    }
    
    public function setId($id)
    {
        $root = $this->doc->documentElement;
        $root->setAttribute('id', $id);
    }
    
    public function setContent($content) {
        $this->content = $this->content.$content;
        $this->addInClass('div','mContent',$this->doc->createTextNode($content));
    }
    
    public function setContentNode($node) {
        $this->addInClass('div','mContent',$node);
    }

    public function addContentElement($content, $element, $class = null) {
        $this->content = $this->content.$content;

        $node = $this->doc->createElement($element);
        $node->appendChild($this->doc->createTextNode($content));

        if ($class) {
            $node->setAttribute('class', $class);
        }

        $this->addInClass('div','mContent',$node);
    }
    
    public function showTitle() {
        $root = $this->doc->documentElement;

        $node = $this->doc->createElement('h3');
        $node->appendChild($this->doc->createTextNode($this->title));

        $this->addInClass('div','mTitle',$node);
    }
}

?>
