<?php

include_once 'm/mTable.php';
include_once 'm/mGetManager.php';

class mDataManager {
    
    public $form;
    public $types = array('table', 'div');
    public $type = 'table';
    public $title = null;
    public $customTemplate = null;
    public $getManager;
    
    public $edit;
    public $delete;
    
    public $editText = 'Edit';
    public $deleteText = 'Delete';
    
    public $getAction = 'action';
    public $getEdit = 'edit';
    public $getDelete = 'delete';
    
    public function __construct($form)
    {
        $this->form = $form;
        $this->getManager = new mGetManager();
    }
    
    public function generate($delete = false, $edit = false)
    {
        $this->edit = $edit;
        $this->delete = $delete;
        
        return $this->selectAction();
    }
    
    public function selectAction()
    {
        if ($this->getManager->isKey('id')) {
            if ($this->getManager->isKeyValue($this->getAction, $this->getEdit)) {
                $dbObject = $this->form->dbTable->queryId($this->getManager->get['id']);
                if (count($dbObject->objects) == 1) {
                    $this->form->setData($dbObject->objects[0]->object);
                    $this->form->editId = $this->getManager->get['id'];
                    
                    $this->getManager->unsetKey('id');
                    $this->getManager->unsetKey($this->getAction);
                    $this->form->redirectUrl = $this->getManager->getAddress();
                    return $this->form->generate();
                }
            }
            
            if ($this->getManager->isKeyValue($this->getAction, $this->getDelete)) {
                $dbObject = $this->form->dbTable->deleteObject($this->getManager->get['id']);
            }
        }
        
        if (in_array($this->type, $this->types)) {
            if (strcmp($this->types[0], $this->type) == 0) {
                return $this->getTable();
            }
            
            if (strcmp($this->types[1], $this->type) == 0) {
                return $this->getDiv();
            }
        }
        
        return $this->getTable();
    }
    
    public function getTable()
    {
        $table = new mTable($this->form->formName);
        
        $dbObject = $this->form->dbTable->queryAll();
        
        foreach ($dbObject->objects as $object) {
            if (!$table->hasHeaderRow) {
                $table->addHeaderRowFromKeys($object->object);
            }
            $tr = $table->addRow($object->object);
            $this->getManager->setKey('id',$object->id);
            
            if ($this->edit) {
                $this->getManager->setKey($this->getAction,$this->getEdit);
                $table->addLinkToTr($tr, $this->getManager->getAddress(), $this->editText);
            }
            if ($this->delete) {
                $this->getManager->setKey($this->getAction,$this->getDelete);
                $table->addLinkToTr($tr, $this->getManager->getAddress(), $this->deleteText);
            }
        }
        
        return $table;
    }
    
    public function setCustomWidget($customTemplate, $title = null)
    {
        $this->customTemplate = $customTemplate;
        $this->type = 'div';
        $this->title = $title;

    }
    
    public function getDiv()
    {
        $wReturn = new mWidget('Data list');
        if ($this->title) {
            $wReturn->title = $this->title;
            $wReturn->showTitle();
        }

        $dbObject = $this->form->dbTable->queryAll();
        
        foreach ($dbObject->objects as $object) {

            $wData = new mWidget('wData', $this->customTemplate, $object->object);
            
            #$wData->show();
            
            $wReturn->add($wData);
        }

        return $wReturn;
    }
    
}

?>
