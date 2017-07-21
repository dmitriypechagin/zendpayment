<?php

class reportsForm extends Zend_Form {
    
	public function init() {
		$this->setMethod('post');
        $validPurseName = new Zend_Validate_Callback(array ( callback => array ('BalanceRow', 'isValidAllPurseName' ) ) );
		$validPurseName->setMessage( BalanceRow::NO_VALID_PURSE , Zend_Validate_Callback::INVALID_VALUE);
        $date1 = $this->createElement('text', 'date1', array ( 'label' => 'the period of transactions' ) )
        				->addValidator('date');
        $date2 = $this->createElement('text', 'date2', array ( 'label' => ' - ' ) )
        				->addValidator('date');
		$tTransaction = new Transaction();
        $type   = $this->createElement('select', 'type', array ( 'label' => 'type', 'multiOptions' => $tTransaction->typeById() ) )
        				->setValue(0);  
		$this->addElement($date1)
             ->addElement($date2)
             ->addElement($type)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Seach', array('label' => 'Seach', 'class'=>"submit", 'decorators' => decorators::$tableTrButton) );
	}
	
	public function loadDefaultDecorators() {
		$this->setDecorators( decorators::$formWrapper );
	}

}