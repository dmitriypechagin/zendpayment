<?php

class blockedForm extends Zend_Form {
	
	public function init() {
        $this->setMethod('post');
    	$tBalance = new Balance();
        $this->loadDefaultDecorators( );
        $this->addDecorator('htmlTag', array('tag' => 'div', 'class' => 'formwrapper_small'));
    	$blocked = $this->createElement('radio', 'blocked', array ( 'label' => 'Status', 'multiOptions' => $tBalance->strBlocked ) )
        				->setValue(0);
		$note   = $this->createElement('text', 'note', array ( 'label' => 'Comment', 'size' => 50 ) )
					   ->setDecorators( array(
						    'ViewHelper',
		    				'Errors',
					   		array('HtmlTag', array('tag' => 'td')),
		    				array('Label', array('tag' => 'td')) 
    				   ));
		$purse = $this->createElement('hidden', 'purse');
        				
		$this->addElement($blocked)
		     ->addElement($purse)
		     ->addElement($note)
		     ->addElement('submit', 'Set', array('label' => 'Set status', 'class'=>"submit", 'decorators' => array('ViewHelper') ) );
	}
	
}
