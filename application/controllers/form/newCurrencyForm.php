<?php

class newCurrencyForm extends Zend_Form {
	
	const NEW_CURRENCY = "Add currency";
	const EDIT_CURRENCY = "Edit currency";
	
	public $_type;
	
	public function __construct($type) {
		$this->_type = $type;
        parent::__construct();
	}
	
    public function init() {
        $this->setMethod('post');
        $abbr = $this->createElement('text', 'abbr', array ( 'label' => 'Abbreviation' ) );
        $abbr->addValidator('StringLength', false, array(Zend_Registry::get('purse')->abbrLength,Zend_Registry::get('purse')->abbrLength))
             ->addValidator('regex', false, array('/^[A-Za-z]/i'))
             ->setRequired(true)
             ->addFilter('StringToUpper');
        if ( $this->_type == self::NEW_CURRENCY ) 
        $abbr->addValidator('Db_NoRecordExists', true, array(Zend_Registry::get('tablePrefix').'object', 'abbr'));
        
        $name = $this->createElement('text', 'name', array ( 'label' => 'Full name' ) );
        $name->addValidator('alnum', false, array('allowWhiteSpace' => true ) )
             ->setRequired(true);
        $exp = $this->createElement('text', 'exp', array ( 'label' => 'Number of signs after a comma' ) );
        $exp->setValue(2)
            ->addValidator('int')
            ->addValidator('between', false, array ( min => 0, max => 9) )
            ->setRequired(true);
        $description = $this->createElement('textarea', 'description', array ( 'label' => 'Description' ) );
        $factor = $this->createElement('hidden', 'factor' );
        $valid_float = new floatNormalizedValidator(  array( min =>0 ) ) ;
		$factor->setValue(1)
                ->addFilter('LocalizedToNormalized')
        		->addValidator( $valid_float )
                ->setRequired(true);
        $course = $this->createElement('text', 'course', array ( 'label' => 'Course concerning base currency' ) );
        $course->setValue(1)
                ->addFilter('LocalizedToNormalized')
                ->addValidator( $valid_float )
                ->setRequired(true);
        $availabilityWithdrawal = $this->createElement('checkbox', 'availabilityWithdrawal', array ( 'label' => 'Availability of a withdrawal' ) );
        $valid_float2 = new floatNormalizedValidator(  array( min =>-0.0001 ) ) ;
        $minWithdrawal = $this->createElement('text', 'minWithdrawal', array ( 'label' => 'Minimum amount for a conclusion ( 0 - without restrictions )' ) );
        $minWithdrawal->setValue(0)
                ->addFilter('LocalizedToNormalized')
                ->addValidator( $valid_float2 )
                ->setRequired(true);
        
        $this->addElement($abbr)
             ->addElement($name)
             ->addElement($exp)
             ->addElement($description)
             ->addElement($factor)
             ->addElement($course)
             ->addElement($availabilityWithdrawal)
             ->addElement($minWithdrawal)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Login', array('label' => 'Save', 'class'=>"submit", 'decorators' => decorators::$tableTrButton));
    }
    
	public function loadDefaultDecorators() {
		$this->setDecorators( decorators::$formWrapper );
	}
    
}

