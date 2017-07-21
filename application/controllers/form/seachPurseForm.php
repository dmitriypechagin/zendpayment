<?php

class seachPurseForm extends Zend_Form {
	
        public  $buttonDecorators = array(
            'ViewHelper',
            array(array('data' => 'HtmlTag'),  array('tag' => 'td' ))
        );
	
	public function init() {
        $this->setMethod('post');
        $validPurseName = new Zend_Validate_Callback(array ( callback => array ('BalanceRow', 'isValidPurseName' ) ) );
		$validPurseName->setMessage( BalanceRow::NO_VALID_PURSE , Zend_Validate_Callback::INVALID_VALUE);
        $purse = $this->createElement('text', 'purse', array ( 'label' => 'purse' ) )
                       ->addValidator( $validPurseName )
					   ->setDecorators( decorators::$tableTdElement );
		$this->addElement($purse)
             ->addElement('submit', 'Seach', array('class'=>"submit_small", 'decorators' => decorators::$tableTdButton ) );
	}
	
	public function loadDefaultDecorators() {
		$this->setDecorators(array(
	        'FormElements',
	        array(array('tr' => 'htmlTag'), array('tag' => 'tr')),
	        array(array('table' => 'htmlTag'), array('tag' => 'table', 'class'=>'form_table', 'cellspacing'=>"0", 'cellpadding'=>"0", 'border'=>"0" )),
	        'Form',
	        array(array('div' =>'htmlTag'), array('tag' => 'div', 'class' => 'formwrapper_small')),
	    ));
	}
		
}
