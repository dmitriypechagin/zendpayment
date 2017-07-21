<?php

class issueForm extends Zend_Form {
    
	public $_purse;
	
	public function __construct($purses) {
		$this->_purse = array();
        if ( count($purses) )
		foreach( $purses as $purse ) {
			$this->_purse[$purse->object_id] = $purse->purseName(); 
		}
        parent::__construct();
	}
	
	public function init() {
        $this->setMethod('post');
        $tTransaction = new Transaction();
        $valid_float = new floatNormalizedValidator(  array( min =>0 ) ) ;
		$oper   = $this->createElement('select', 'oper', array ( 'label' => 'operation', 
		         												 'multiOptions' => $tTransaction->issueOperation 
						) )
                       ->setValue(1);
        $purse  = $this->createElement('select', 'purse', array ( 'label' => 'purse', 'multiOptions' => $this->_purse ) )
                       ->setValue(1);
        $amount = $this->createElement('text', 'amount', array ( 'label' => 'amount' ) )
                	   ->addFilter('LocalizedToNormalized')
        				->addValidator( $valid_float )
                       ->setRequired(true);
        $note   = $this->createElement('textarea', 'note', array ( 'label' => 'note', 'size' => 50 ) )
		;   
        $this->addElement($oper)
             ->addElement($purse)
             ->addElement($amount)
             ->addElement($note)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Send', array('label' => 'Save', 'class'=>"submit", 'decorators' => decorators::$tableTrButton ) );
	}
	
	public function loadDefaultDecorators() {
		$this->setDecorators( decorators::$formWrapper );
	}

}