<?php

class RegForm extends Zend_Form {
   
    public function init() {

        $this->setMethod('post');
        $username = $this->createElement('text', 'email', array ( 'label' => 'Login name', 'placeholder'=>"E-mail адрес"));
        $username->addValidator('EmailAddress',true, array('domain' => TRUE, 'allow' => Zend_Validate_Hostname::ALLOW_DNS  ))
				 ->addValidator('Db_NoRecordExists', true, array(Zend_Registry::get('tablePrefix').'user', 'e_mail'))
                 ->setRequired(true)
                 ->addFilter('StringToLower');
        $password = $this->createElement('password', 'password', array ( 'label' => 'Password', 'placeholder'=>"Пароль"));
        $password->addValidator('StringLength', false, array(6))
                 ->setRequired(true);
        $this->addElement($username)
             ->addElement($password)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Send', array('label' => 'Submit', 'class'=>"btn btn-primary", 'decorators' => decorators::$tableTrButton));        
    	
    }
    
}