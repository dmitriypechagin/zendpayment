<?php

class floatNormalizedValidator extends Zend_Validate_Abstract{
	
	const NOT_GREATER = 'notGreaterThan';
    const NOT_LESS = 'notLessThan';
    const NOT_FLOAT = 'notFloat';
    
	protected $_options;
    protected $_min;
    protected $_max;
    
    protected $_messageTemplates = array(
        self::NOT_GREATER => "'%value%' is not greater than '%min%'",
        self::NOT_LESS => "'%value%' is not less than '%max%'",
        self::NOT_FLOAT => "'%value%' does not appear to be a float",
	);
	
    protected $_messageVariables = array(
        'min' => '_min',
        'max' => '_max'
	);

    public function __construct($options)
    {
        $this->_options = $options;
        $this->_min = $options[min];
        $this->_max = $options[max];
    }
	
    public function isValid($value) {
		$filter1 = new Zend_Filter_NormalizedToLocalized();
		$filter2 = new Zend_Filter_LocalizedToNormalized();
		$num = $filter1->filter( $value ); 
		$validator = new Zend_Validate_Float();
		if ( !$validator->isValid( $num ) ) {
			$this->_error(self::NOT_FLOAT);
			return false;	
		}
		$num = $filter2->filter( $num ); 
		if (  isset($this->_min) && $num <= $this->_min )  {
			$this->_error(self::NOT_GREATER);
			return false;	
		}  
		if (  isset($this->_max) && $num >= $this->_max )  {
			$this->_error(self::NOT_LESS);
			return false;	
		}
		return true;  
    }
	
}
