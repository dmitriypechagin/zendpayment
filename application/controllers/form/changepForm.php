<?php

 
class ChangepForm extends Zend_Form {
   
    public function init() {

        $this->setMethod('post');

        $oldpassword = $this->createElement('password', 'oldpassword', array ( 'label' => 'Old password'))
        					->addValidator('StringLength', false, array(6))
                 			->setRequired(true);
        
        $newpassword = $this->createElement('password', 'newpassword', array ( 'label' => 'New password'))
        					->addValidator('StringLength', false, array(6))
                 			->setRequired(true);
                 			
        $confirmpassword = $this->createElement('password', 'confirmpassword', array ( 'label' => 'Confirm new password'))
        					->addValidator('StringLength', false, array(6))
        					->addValidator('Identical', false, array('token' => 'newpassword') )
        					->setRequired(true);
                 			
        $this->addElement($oldpassword)
             ->addElement($newpassword)
             ->addElement($confirmpassword)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Send', array('label' => 'Change', 'class'=>"btn mw-md btn-primary", 'decorators' => decorators::$tableTrButton));        
    	
    }

    public function loadDefaultDecorators() {
		$this->setDecorators( decorators::$formWrapper );
	}
    
}