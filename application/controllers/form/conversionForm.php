<?

class ConversionForm extends Zend_Form {

	public $_purse;
	
	public function __construct($purses) {
		$this->_purse = Array();
        if ( count($purses) )
		foreach( $purses as $purse ) {
			$this->_purse[$purse->object_id] = substr($purse->purseName(), 0, 3); 
		}
        parent::__construct();
	}
	
	public function init() {
		$this->setMethod('post');
        $valid_float = new floatNormalizedValidator(  array( min =>0 ) ) ;
		$purseFrom  = $this->createElement('select', 'objectFrom', array ( 'label' => 'from purse', 'class' => "form-control", 'multiOptions' => $this->_purse ) )
                       ->setRequired(true);
		$amount = $this->createElement('text', 'amount', array ( 'label' => 'amount', 'class' => "form-control", 'placeholder' => '0' ) )
                		->addFilter('LocalizedToNormalized')
						->addValidator( $valid_float )
                       	->setRequired(true);
        $purseTo = $this->createElement('select', 'objectTo', array ( 'label' => 'to purse', 'class' => "form-control", 'multiOptions' => $this->_purse ) )
                       ->setRequired(true);
        $summa   = $this->createElement('text', 'summa', array ( 'label' => 'Total', 'class' => "form-control", 'disabled' => '' ) );
        $this->addElement($purseFrom)
             ->addElement($amount)
             ->addElement($purseTo)
             ->addElement($summa)
			 ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Send', array('label' => 'convert', 'class'=>"btn mw-md btn-primary", 'decorators' => decorators::$tableTrButton) );
	}

}	
