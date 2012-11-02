<?php

include_once 'm/mForm.php';
include_once 'm/mDataManager.php';
include_once 'm/mDatabaseTable.php';

class mUserManager {
    
    public $registerForm;
    public $loginForm;
    public $dManager;
    public $tableName = 'mUsers';
    
    public function __construct($db)
    {

        $this->registerForm = new mForm('post',$this->tableName);
        $this->registerForm->addInput('email','Anna sposti','sposti');
        $this->registerForm->addInput('password','Salasana','passu1');
        $this->registerForm->addInput('password','Salasana uudelleen','passu2');
        $this->registerForm->addInput('textarea','Kerro itsestäsi','area');
        
        $this->dManager = new mDataManager($this->registerForm);
        
        $this->registerForm->addDatabase($db);
        
        $this->loginForm = new mForm('post','mLoginForm');
        $this->loginForm->addInput('email','Sähköpostiosoite','sposti');
        $this->loginForm->addInput('password','Salasana','passu');
    }
    
    public function generateRegisterForm()
    {
        return $this->registerForm->generate();
    }
    
    public function generateLoginForm()
    {
        return $this->loginForm->generate();
    }
    
    public function generateUserAdmin()
    {
        return $this->dManager->generate(true, true);
    }
}


?>
