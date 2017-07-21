<?

class PursesForm extends Zend_Form {

	public $_purse;
	
	public function __construct($purses) {
		$this->_purse = Array();
        if ( count($purses) )
		foreach( $purses as $purse ) {
			$this->_purse[$purse->object_id] = $purse->purseName(); 
		}
        parent::__construct();
	}
	
	public function init() {
		$this->setMethod('post');
        $valid_float = new floatNormalizedValidator(  array( min =>0 ) ) ;
		$purseFrom  = $this->createElement('select', 'objectFrom', array ( 'label' => 'from purse', 'multiOptions' => $this->_purse ) )
                       ->setRequired(true);
		$amount = $this->createElement('text', 'amount', array ( 'label' => 'amount' ) )
                		->addFilter('LocalizedToNormalized')
						->addValidator( $valid_float )
                       	->setRequired(true);
        $purseTo = $this->createElement('select', 'objectTo', array ( 'label' => 'to purse', 'multiOptions' => $this->_purse ) )
                       ->setRequired(true);
        $summa   = $this->createElement('text', 'summa', array ( 'label' => 'total', 'disabled' => '' ) );
        $this->addElement($purseFrom)
             ->addElement($amount)
             ->addElement($purseTo)
             ->addElement($summa)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Send', array('label' => 'convert', 'class'=>"submit", 'decorators' => decorators::$tableTrButton) );
	}

	public function loadDefaultDecorators() {
		$this->setDecorators( decorators::$formWrapper );
	}

}	
