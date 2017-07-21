<?php

/**
 * issue Form
 */

class Install_Settings extends Zend_Form {
    
	public function init() {
        $this->setMethod('post');
        $purseLength = $this->createElement('text', 'purseLength', array( 'label' => 'purseLength' ) )        
                       ->addValidator('int')
                       ->addValidator('GreaterThan', false, array ( 'min' => 3 ) )
                       ->setValue(12)
                       ->setRequired(true);
        //  hidden if objects count > 0
        $tObject = new Object;
        $countObjects = count ( $tObject->fetchAll() );
        
		$abbrLength = $this->createElement('text', 'abbrLength', array ( 'label' => 'abbrLength' ) )
                       ->addValidator('int')
                       ->addValidator('Between', false, array ( 'min' => 2, 'max'=>5 ) )
                       ->setValue(3)
                       ->setRequired(true);
		$this->addElement($purseLength);
        if ( !$countObjects ) $this->addElement($abbrLength);
        $this->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Set', array('label' => 'Save', 'class'=>"submit", 'decorators' => decorators::$tableTrButton )  );
	}
	
	public function loadDefaultDecorators() {
		$this->setDecorators( decorators::$formWrapper );
	}
}