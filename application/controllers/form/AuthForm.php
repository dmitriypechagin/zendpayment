<?php

class AuthForm extends Zend_Form {
   
    public function init() {
        $this->setMethod('post');
        $username = $this->createElement('text', 'email', array('label' => 'Login', 'placeholder'=>"Email"));
		$confirmCode = $this->createElement('text', 'auth_code', array('label' => 'Code', 'placeholder'=>"Code"));
        $validator = new Zend_Validate_Callback(array ( callback => array ('UserRow', 'isValidUserName' ) ) );
        $username->addValidator( $validator )
                 ->setRequired(true)
                 ->addFilter('StringToLower');
        $password = $this->createElement('password', 'password', array('label' => 'Password', 'placeholder'=>"Password"));
        $password->addValidator('StringLength', false, array(6))
                 ->setRequired(true);
        $this->addElement($username)
             ->addElement($password)
			 ->addElement($confirmCode)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Login', array('label' => 'Login', 'class'=>"btn btn-primary", 'decorators' => decorators::$tableTrButton));
    }
    
	
    
}

