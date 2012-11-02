<?php

include_once 'mTemplate.php';

class mTable extends mTemplate {
    
    public $hasHeaderRow = false;
    
    public function __construct($title, $file = 'm/template/table.xml', $data = Null) {
        $this->title = $title;
        $this->addTemplateFile($file, $data);
    }
    
    public function addHeaderRow($row)
    {
        $this->hasHeaderRow = true;
        return $this->addRow($row,'th');
    }
    
    public function addHeaderRowFromKeys($row)
    {
        $newRow = array();
        
        foreach ($row as $key => $cell) {
            array_push($newRow, $key);
        }
        
        $this->hasHeaderRow = true;
        return $this->addRow($newRow,'th');
    }
    
    public function addRow($row, $type = 'td')
    {
        $table = $this->doc->getElementsByTagName('table')->item(0);
        $tr = $table->appendChild($this->doc->createElement('tr'));
        
        foreach ($row as $cell) {
            if (is_array($cell)) {
                $cell = implode(", ", $cell);
            }
            $td = $tr->appendChild($this->doc->createElement($type));
            $td->appendChild($this->doc->createTextNode($cell));
        }
        return $tr;
    }
    
    public function addLinkToTr($tr, $address, $title, $type = 'td')
    {
        $td = $tr->appendChild($this->doc->createElement($type));
        $a = $td->appendChild($this->doc->createElement('a'));
        $a->setAttribute('href', $address);
        $a->appendChild($this->doc->createTextNode($title));
    }
}

?>
