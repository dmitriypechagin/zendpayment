<?php

/**
 * Model Transaction
 *  
 * @author Lepaysys
 * @version 1.0
 */

class Transaction extends Zend_Db_Table_Abstract 
{
	
	const YUOR_PURSE = "You indicated your purse as a destination";
	const NOT_WITHDARWAL = "Not well for a withdrawal";
	const MIN_AMOUNT = "It is not enough amount for a withdrawal";
	const CON_UNAVAILBLE = "Such conversion is unavailable";
	
	/**
 	 * @see Zend_Db_Table_Abstract
     * @var string
     */
	protected $_rowClass = 'TransactionRow';
	
	/**
 	 * @see Zend_Db_Table_Abstract
     * @var array
     */
    protected $_referenceMap = array(
        'user' => array(
            'columns'         => array('user_id'),
            'refTableClass'   => 'User',            
            'refColumns'      => array('user_id')
	    ),
        'touser' => array(
            'columns'         => array('to_user_id'),
            'refTableClass'   => 'User',            
            'refColumns'      => array('user_id')
	    ),
	    'object' => array(
            'columns'         => array('object_id'),
            'refTableClass'   => 'Object',            
            'refColumns'      => array('object_id')
	    ),
	    'sell_object' => array(
            'columns'         => array('sell_object_id'),
            'refTableClass'   => 'Object',            
            'refColumns'      => array('object_id')
	    ),
	    'sell_owner' => array(
            'columns'         => array('sell_owner_id'),
            'refTableClass'   => 'User',            
            'refColumns'      => array('user_id')
	    ),
	    'buy_object' => array(
            'columns'         => array('buy_object_id'),
            'refTableClass'   => 'Object',            
            'refColumns'      => array('object_id')
	    ),
	    'buy_owner' => array(
            'columns'         => array('buy_owner_id'),
            'refTableClass'   => 'User',            
            'refColumns'      => array('user_id')
	    )
	);

	/**
 	 * Type of operation
     * @var array
     */
	public $type = array( 
		''=>0, 
		'issue' => 1, 
		'repayment' => 2, 
		'transfer' => 3, 
		'commission' =>4, 
		'withdrawal' => 5, 
		'conversion' => 6, 
		'storage' => 7 
	);
	
	/**
 	 * The status of the request for withdrawal
     * @var array
     */
	public $status = array( 
		'in handling'=>0, 
		'executed' => 1, 
		'cancel' => 2 
	);

	/**
 	 * Type of creation or removal of means in system
     * @var array
     */
	public $issueOperation = array( 
		'1'=>'creation of means', 
		'2'=>'removal of means' 
	);
	
	/**
 	 * @see Zend_Db_Table_Abstract
     * @return void
     */
    protected function _setupTableName()
    {
    	$this->_name = $this->_name = Zend_Registry::get('tablePrefix').'transaction';
        parent::_setupTableName();
    }    
	
	/**
 	 * if set parameter Id returned string type operation, else returned array $type
 	 * @see $type  
     * @param  int $id 
     * @return array|string
     */
    public function typeById( $id = null ) {
		$a = array_combine( array_values($this->type), array_keys($this->type) );
		if ( $id===null ){
			return $a;			
		} else {
			return $a[$id];			
		}
	}
	
	/**
 	 * Creation of new transaction
 	 * 
     * @param  	array $data 
     * 			int $data['user_id'] Sender user id 
     * 			int $data['to_user_id'] Receiver user id 
     * 			int $data['object_id'] Object id 
     * 			int $data['amount'] Ammount transaction 
     * 			int $data['type'] Type of transaction (see array $type)
     * 			int $data['description'] Description transaction 
     * @return void
     */
	public function create( array $data ) {
		if ( $data['user_id'] == $data['to_user_id'] ) { throw new Zend_Db_Adapter_Exception( self::YUOR_PURSE ); } 
		$tBalance = new Balance();
		if ( $data[user_id] ) $userBalance = $tBalance->findBalanceByUserObject( $data['user_id'], $data['object_id'] );
		if ( $data[to_user_id] ) $to_userBalance = $tBalance->findBalanceByUserObject( $data['to_user_id'], $data['object_id'] );
        if ($userBalance) $userBalance->change( -$data['amount'] ); 
        if ($to_userBalance) $to_userBalance->change( $data['amount'] );
        $this->insert( $data ); 
	}
	
	/**
 	 * Transaction canceling
 	 * 
     * @param  int $id transaction   
     * @return void
     */
	public function cancel( $id ) {
		$tr = $this->find( $id )->current();
		$data = $tr->toArray();
		$user_id = $data['user_id'];
		$to_user_id = $data['to_user_id'];
		$data['description'].= "(cancel #".$data['transaction_id'].")";
		$data['transaction_id'] = NULL;
		$data['to_user_id'] = $user_id;
		$data['user_id'] = $to_user_id;
		$this->issue( $data );
		$tr->status = $this->status['cancel'];
		$tr->save(); 
	}
	
	/**
 	 * Creation of means
 	 * 
     * @param  array $data - see function create
     * @return void
     */
	public function issue( $data ) {
		$data['type'] = $this->type['issue'];
		$this->create( $data );
	}
	
	/**
 	 * Deletion of means
 	 * 
     * @param  array $data - see function create
     * @return void
     */
	public function repayment( $data ) {
		$data['type'] = $this->type['repayment'];
		$this->create( $data );
	}
	
	/**
 	 * Transfer of means
 	 * 
     * @param  array $data - see function create
     * 		   int $data['mode'] : 1 -  without the commission, 0 - with the commission 
     * 		   int $data['commission']  Commission procentre 
     * @return void
     */
	public function transfer( $data ) {
		if ( $data['mode'] ) {
		    // admin - no commission transaction
		    $data['amount'] = $data['amount']*(1-$data['commission']/100);
		    unset($data['mode']); unset($data['commission']);
		    $data['type'] = $this->type['transfer'];
		    $this->create( $data );
		} else {
			// transfer transaction
			$tCommissions = new Commissions();
		    unset($data['mode']); unset($data['commission']);
			/* $commission = $tCommissions->getTransferCommissionByObject( $data['object_id'] )->commission; */
			$tUser = new User();
			$user = Zend_Auth::getInstance()->getIdentity();
			$user = $tUser->find( $user->user_id )->current();
			if ( $user->account_type == 0 ) {
				$commission = 1.7;
			}
			else if ( $user->account_type == 1 ) {
				$commission = 0.5;
			}
			else if ( $user->account_type == 2 ) {
				$commission = 0.4;
			}
			else if ( $user->account_type == 3 ) {
				$commission = 0.3;
			}
		    $data['type'] = $this->type['transfer'];
			$this->create( $data );
			// commission transaction
			$tUser = new User();
			$amount = $data['amount'];
			$data['amount'] = $amount*($commission/100);
			$data['description'] = $data['description']."(commission)";
			$data['to_user_id'] = $tUser->getAdmin()->user_id; 
		    $data['type'] = $this->type['commission'];
			$this->create( $data );
		}
	}

	/**
 	 * Withdrawal of means
 	 * 
     * @param  array $data - see function create
     * @return void
     */
	public function withdrawal( $data ) {
		$tObject = new Object();
		$object = $tObject->find( $data['object_id'] )->current();
		if ( !$object->availabilityWithdrawal ) { throw new Zend_Db_Adapter_Exception( self::NOT_WITHDARWAL ); } 
		if ( $object->minWithdrawal > $data['amount'] ) { throw new Zend_Db_Adapter_Exception( self::MIN_AMOUNT ); } 
		$tUser = new User();
		$data['type'] = $this->type['withdrawal'];
		$data['to_user_id'] = NULL; 
		$this->create( $data );
	}
	
	/**
 	 * Conversion of means
 	 * 
     * @param  array $data - see function create
     * @param  boolean $mode : 1 -  without the commission, 0 - with the commission
     * @return void
     */
	public function conversion( $data, $mode ) {
		$obj1 = $data['objectFrom']; unset( $data['objectFrom'] );
		$obj2 = $data['objectTo']; unset( $data['objectTo'] );
		$tObject = new Object();
		$course = $tObject->getConversionCourse( $obj1, $obj2 );
		$tCommissions = new Commissions();
		$commissionRow = $tCommissions->getConversionCommission( $obj1, $obj2 );
		$data['type'] = $this->type['conversion'];
		if ( $mode ) {
			if ( !$commissionRow->available ) return;
		    // admin - no commission transaction
		    // -
		    $data['object_id'] = $obj1;
		    unset( $data['to_user_id'] );
		    $this->create( $data );
		    // +
		    $data['object_id'] = $obj2;
		    $data['to_user_id'] = $data['user_id']; unset( $data['user_id'] ); 
		    $data['amount'] = $data['amount']*$course;
		    $this->create( $data );
		} else {
			if ( !$commissionRow->available ) { throw new Zend_Db_Adapter_Exception( self::CON_UNAVAILBLE ); }
			if ( $obj1 == $obj2 ) { throw new Zend_Db_Adapter_Exception( self::CON_UNAVAILBLE ); }
			$tUser = new User();
			$user = Zend_Auth::getInstance()->getIdentity();
			$user = $tUser->find( $user->user_id )->current();
			if ( $user->account_type == 0 ) {
				$commission = 1.7;
			}
			else if ( $user->account_type == 1 ) {
				$commission = 0.5;
			}
			else if ( $user->account_type == 2 ) {
				$commission = 0.4;
			}
			else if ( $user->account_type == 3 ) {
				$commission = 0.3;
			}
			/* $commission = $commissionRow->commission; */
			$amount = $data['amount'];
			// conversion transaction
		    // -
			$data['object_id'] = $obj1;
		    unset( $data['to_user_id'] );
		    $this->create( $data );
		    // +
		    $data['object_id'] = $obj2;
		    $data['to_user_id'] = $data['user_id']; unset( $data['user_id'] ); 
		    $data['amount'] = $amount*$course*(1-$commission/100);
		    $this->create( $data );
		    // commission transaction
			$tUser = new User();
			$data['amount'] = $amount*$course*($commission/100);
			$data['description'] = $data['description']."(commission)";
			$data['user_id'] = $data['to_user_id'];
			$data['to_user_id'] = $tUser->getAdmin()->user_id; 
		    $data['type'] = $this->type['commission'];
			$this->create( $data );
		}
	}
	
	/**
 	 * The commission for storage
     * @param  array $data - see function create
     * @return void
     */
	public function storage( $data ) {
		$data['type'] = $this->type['storage'];
		$this->create( $data );
	}

}

