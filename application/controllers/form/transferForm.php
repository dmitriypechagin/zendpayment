<?

class TransferForm extends Zend_Form {

	public $_type;
	
	public function __construct($type) {
		$this->_type = $type;
        parent::__construct();
	}
	
	public function init() {
        $this->setMethod('post');
        $validPurseName = new Zend_Validate_Callback(array ( callback => array ('BalanceRow', 'isValidUser' ) ) );
		$validPurseName->setMessage( BalanceRow::NO_VALID_PURSE , Zend_Validate_Callback::INVALID_VALUE);
		$valid_float = new floatNormalizedValidator(  array( min =>0 ) ) ;
		
		$tUser = new User();
		$user = Zend_Auth::getInstance()->getIdentity();
		$user = $tUser->find( $user->user_id )->current();
		
        $abbr  = $this->createElement('select', 'abbr', array ( 'label' => 'From purse', 'class' => "form-control", 'multiOptions' => array( 
			'RUB' => 'RUB'. $user->user_id,
			'USD' => 'USD'. $user->user_id,
			'GLD' => 'GLD'. $user->user_id,
			'EUR' => 'EUR'. $user->user_id,
			'UAH' => 'UAH'. $user->user_id,
			'GBP' => 'GBP'. $user->user_id,
			'JPY' => 'JPY'. $user->user_id,
			'CNY' => 'CNY'. $user->user_id,
			'SPM' => 'SPM'. $user->user_id
		) ) )
                       ->setRequired(true);
		$purse = $this->createElement('text', 'purse', array ( 'label' => 'Addressee ID' ) )
                       ->addValidator('regex', false, array('/^[A-Z0-9]/i'))
                       ->addFilter('StringToUpper')
		               ->setRequired(true)
                       ->addValidator( $validPurseName );
		$amount = $this->createElement('text', 'amount', array ( 'label' => 'amount' ) )
        			        	->addFilter('LocalizedToNormalized')
        						->addValidator( $valid_float )
		                       	->setRequired(true);
        $note   = $this->createElement('text', 'note', array ( 'label' => 'note', 'size' => 50, 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Укажите информацию для получателя' ) )
        ;   
        $this->addElement($abbr)
			 ->addElement($purse)
             ->addElement($amount)
             ->addElement($note);
        if ($this->_type) {
        	$commission   = $this->createElement('text', 'commission', array ( 'label' => 'commission' ) );
        } else {
        	$commission   = $this->createElement('hidden', 'commission', array ( 'label' => 'commission', 'disabled' => '' ) );
        }
		$commission->addValidator('float');
        $summa   = $this->createElement('text', 'summa', array ( 'label' => 'total', 'disabled' => '' ) )
	                    ->addValidator('float');
        $this->addElement($commission)
             ->addElement($summa)
             ->setElementDecorators( decorators::$tableTrElement )
             ;
        $this->addElement('submit', 'Send', array('label' => 'send', 'class'=>"btn mw-md btn-primary", 'decorators' => decorators::$tableTrButton ) );
	}

	public function loadDefaultDecorators() {
		$this->setDecorators( decorators::$formWrapper );
	}

}	
