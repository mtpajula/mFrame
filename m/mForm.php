<?php

include_once 'm/mWidget.php';
include_once 'm/mFormElement.php';
include_once 'm/mValidateSanitize.php';
include_once 'm/mDatabaseTable.php';

class mForm {
    
    public $inputTypes = array('submit','text','email','password','textarea','hidden','checkbox','radio','select','button');
    public $widget;
    
    public $buttonTitle = 'Send';
    public $errorMessage = ' | Incorrect input';
    public $returnLink = 'Return';
    
    public $formElements = array();
    public $formName;
    public $method;
    public $valSan;
    public $successWidget;
    public $dbTable = null;
    public $refresh = false;
    public $refreshTime = 0;
    public $address;
    
    public $generateNum = 0;
    public $editId = null;
    public $redirectUrl;
    
    public function __construct($method, $formName)
    {
        $this->formName = $formName;
        $this->method = $method;
        $this->valSan = new mValidateSanitize();
        
        $this->address = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        $this->redirectUrl = $this->address;
        
        $this->successWidget = new mWidget('Form received');
        $this->successWidget->showTitle();
        
        $formElement = $this->addInput('hidden',null,$this->formName);
        $formElement->save = false;
    }
    
    public function addInput($type, $title, $name, $required = true, $value = null)
    {
        $formElement = new mFormElement();
        $formElement->addData($type, $title, $name, $value, $required);
        array_push($this->formElements, $formElement);
        return $formElement;
    }
    
    public function addFormElement($formElement)
    {
        array_push($this->formElements, $formElement);
    }
    
    public function createInputElement($formElement, $parent = null)
    {
        if (in_array($formElement->type, $this->inputTypes)) {
            if (!$parent) {
                $form = $this->widget->doc->getElementsByTagName('form')->item(0);
                if (strcmp('hidden', $formElement->type) == 0) {
                    $div = $form;
                } else {
                    $div = $form->appendChild($this->widget->doc->createElement('div'));
                    $div->setAttribute('class', 'mFormDiv');
                }
            } else {
                $div = $parent->appendChild($this->widget->doc->createElement('div'));
                $div->setAttribute('class', 'mFormSubDiv');
            }
            
            if ($formElement->title and $formElement->listElement == false) {
                $divTitle = $div->appendChild($this->widget->doc->createElement('div'));
                $divTitle->setAttribute('class', 'mFormTitle');
                
                if (!$formElement->valid) {
                    $span = $divTitle->appendChild($this->widget->doc->createElement('span'));
                    $span->setAttribute('class', 'mFormError');
                    $span->appendChild($this->widget->doc->createTextNode($formElement->title.$this->errorMessage));
                } else {
                    $divTitle->appendChild($this->widget->doc->createTextNode($formElement->title));
                }
            }
            
            if (empty($formElement->options)) {
                if (strcmp('textarea', $formElement->type) == 0) {
                    $input = $div->appendChild($this->widget->doc->createElement('textarea'));
                    $input->appendChild($this->widget->doc->createTextNode($formElement->value));
                    $input->setAttribute('id', 'bootstrap-wysihtml5');
                } else if (strcmp('button', $formElement->type) == 0) {
                    $input = $div->appendChild($this->widget->doc->createElement('button'));
                    $input->appendChild($this->widget->doc->createTextNode($formElement->value));
                    $input->setAttribute('type', 'submit');
                    $input->setAttribute('class', 'btn');
                } else {
                    $input = $div->appendChild($this->widget->doc->createElement('input'));
                    $input->setAttribute('type', $formElement->type);
                    if ($formElement->value) {
                        $input->setAttribute('value', $formElement->value);
                    }
                    
                    if ($formElement->placeholder) {
                        $input->setAttribute('placeholder', $formElement->placeholder);
                    }
                }
                
                if ($formElement->name) {
                    $input->setAttribute('name', $formElement->xmlName);
                }
                
                if ($formElement->checked) {
                    $input->setAttribute('checked', 'checked');
                }
                
                if ($formElement->listElement) {
                    $span = $div->appendChild($this->widget->doc->createElement('span'));
                    $span->setAttribute('class', 'mListElementTitle');
                    $span->appendChild($this->widget->doc->createTextNode($formElement->title));
                }
            } else {
                if (strcmp('select', $formElement->type) == 0) {
                    $this->createSelectElement($formElement, $div);
                    return;
                }
                
                foreach ($formElement->options as $childElement) {
                    $this->createInputElement($childElement, $div);
                }
            }
        }
    }
    
    public function createSelectElement($formElement, $div)
    {
        $select = $div->appendChild($this->widget->doc->createElement('select'));
        $select->setAttribute('name', $formElement->name.'[]');
        
        $option = $select->appendChild($this->widget->doc->createElement('option'));
        $option->setAttribute('value', '');
        $option->appendChild($this->widget->doc->createTextNode('---'));
        
        foreach ($formElement->options as $childElement) {
            $option = $select->appendChild($this->widget->doc->createElement('option'));
            $option->setAttribute('value', $childElement->value);
            $option->appendChild($this->widget->doc->createTextNode($childElement->title));
            if ($childElement->checked) {
                $option->setAttribute('selected', 'selected');
            }
        }
    }
    
    public function refreshWidget($url = null)
    {
        $w = new mWidget('mRefresh');
        
        //~ <meta http-equiv="refresh" content="0" />
        $root = $w->doc->documentElement;
        $node = $root->appendChild($w->doc->createElement('meta'));
        $node->setAttribute('http-equiv', 'refresh');
        $node->setAttribute('content', $this->refreshTime);
        if (!$url) {
            $url = $this->redirectUrl;
        }
        $node->setAttribute('url', $url);
        
        return $w;
    }
    
    public function createForm()
    {
        $this->widget = new mWidget('mForm', 'm/template/form.xml');
        $form = $this->widget->doc->getElementsByTagName('form')->item(0);
        $form->setAttribute('method', $this->method);
        $form->setAttribute('action', $this->address);
        
        foreach ($this->formElements as $key => $formElement) {
            
            if (strcmp('mSubmit', $formElement->name) == 0) {
                unset($this->formElements[$key]);
            } else {
                $this->createInputElement($formElement);
            }
        }
        
        #$formElement = $this->addInput('submit',null,'mSubmit',true,$this->buttonTitle);
        $formElement = $this->addInput('button',null,'mSubmit',true,$this->buttonTitle);
        $formElement->save = false;
        
        #<button type="submit" class="btn">Submit</button>
        
        $this->createInputElement($formElement);
    }
    
    public function validate()
    {
        $this->valSan->data = $_POST;
        $valid = $this->valSan->processFormElements($this->formElements);
        
        return $valid;
    }
    
    public function generate()
    {
        $this->generateNum += 1;
        $this->formElements[0]->value = $this->generateNum;
        
        if(isset($_POST[$this->formName])) {
            
            if ($this->validate()) {
                
                if ($this->generateNum == $this->formElements[0]->value) {
                    return $this->onSuccess();
                }
            }
        }
        
        $this->createForm();
        
        return $this->widget;
    }
    
    public function setRefresh($time = 0)
    {
        $this->refresh = true;
        $this->refreshTime = $time;
    }
    
    public function onSuccess()
    {   
        if ($this->dbTable) {
            if ($this->editId) {
                $dbObject = $this->dbTable->updateData($this->editId, $this->getData());
            } else {
                $dbObject = $this->dbTable->insertData($this->getData());
            }
            $this->successWidget->addContentElement($dbObject->message, 'p');
        }
        
        if ($this->refresh) {
            return $this->refreshWidget();
        }
        
        $node = $this->successWidget->doc->createElement('p');
        $link = $node->appendChild($this->successWidget->doc->createElement('a'));
        $link->setAttribute('href', $this->redirectUrl);
        $link->appendChild($this->successWidget->doc->createTextNode($this->returnLink));
        $this->successWidget->setContentNode($node);

        return $this->successWidget;
    }
    
    public function setData($data)
    {
        foreach ($this->formElements as $formElement) {
            foreach ($data as $key => $str) {
                
                if (strcmp($key, $formElement->name) == 0) {
                    
                    if (is_array($str)) {
                        $this->setArrayFormElement($formElement, $str);
                    } else {
                        $formElement->value = $str;
                    }
                }
            }
        }
    }
    
    public function setArrayFormElement($formElement, $arr)
    {
        foreach ($formElement->options as $subFormElement) {
            if (in_array($subFormElement->value, $arr)) {
                $subFormElement->checked = true;
            }
        }
    }

    public function getData()
    {
        $data = array();
        foreach ($this->formElements as $formElement) {
            if ($formElement->save) {
                if (empty($formElement->options)) {
                    $data[$formElement->name] = $formElement->value;
                } else {
                    $arr = array();
                    foreach ($formElement->options as $subFormElement) {
                        if ($subFormElement->checked) {
                            array_push($arr, $subFormElement->value);
                        }
                    }
                    $data[$formElement->name] = $arr;
                }
            }
        }
        return $data;
    }

    public function addDatabase($db)
    {
        $this->dbTable = new mDatabaseTable($this->formName, $db);
    }
}

?>
