<?php


/**
 * Model Balance
 *  
 * @author Lepaysys
 * @version 1.0
 */
class Balance extends Zend_Db_Table_Abstract {
	
	/**
 	 * @see Zend_Db_Table_Abstract
     * @var string
     */
	protected $_rowClass = 'BalanceRow';
	
    /**
 	 * @see Zend_Db_Table_Abstract
     * @return void
     */
    protected function _setupTableName()
    {
    	$this->_name = Zend_Registry::get('tablePrefix').'balance';
        parent::_setupTableName();
    }    
	
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
	    'object' => array(
            'columns'         => array('object_id'),
            'refTableClass'   => 'Object',            
            'refColumns'      => array('object_id')
	    )
	);
	
	/**
 	 * Type of purse
     * @var array
     */
	public $strBlocked = array( 0=>'Enabled', 1 =>'Blocked' );
	
    /**
     * Return Balance row for user $user_id and object $object_id     
     *
     * @param  int $user_id 
     * @param  int $object_id 
     * @return BalanceRow
     */
	public function findBalanceByUserObject( $user_id, $object_id ) {
			if ( !$user_id || !$object_id ) return;
			$select = $this->select()->where( 'user_id = ?', $user_id ) 
		    	                     ->where( 'object_id = ?', $object_id );
        	if ( !($userBalance = $this->fetchRow( $select )) ) $userBalance = $this->createRow( array( user_id => $user_id, object_id => $object_id ) );
        	return $userBalance;
	}
	
    /**
     * Return Balance rowset for object $object_id     
     *
     * @param  int $object_id 
     * @return BalanceRowset
     */
	public function findBalancesByObject( $object_id ) {
			$select = $this->select()->where( 'object_id = ?', $object_id );
			return $this->fetchAll( $select );
	}
	
    /**
     * Return Balance row for purse name     
     *
     * @param  string $purse 
     * @return BalanceRow
     */
	public function findBalanceByPurse( $purse ) {
		$tObject = new Object();
		$tUser = new User();
		return $this->findBalanceByUserObject( $tUser->getIdByPurse( $purse ), $tObject->findByPurse( $purse )->object_id );
	}
	
    /**
     * Return purse name for user $user_id and object $object_id
     *
     * @param  int $user_id 
     * @param  int $object_id 
     * @return string
     */
	public function purseNameByUserObject( $user_id, $object_id ) {
		if ( $user_id ) {
			return $this->findBalanceByUserObject( $user_id, $object_id )->purseName();
		} else {
			return "";
		} 
	}
	
    /**
     * Returns a total amount of means in system on each object
     *
     * @return BalanceRowset
     */
	public function amountObjects() {
		$select = $this->select()->from($this,array('object_id, sum(amount) sum') ) 
		    	                     ->group('object_id');
		return $this->fetchAll($select);		    	 
	}


	public function getCurrencyBalance($usesr_id, $object_id)
	{
		$this->_name = Zend_Registry::get('tablePrefix').'balance';

		$row = $this->fetchRow('user_id = '. $usesr_id .' AND object_id = '. $object_id);

		if( !$row ) return false;

		return $row;
	}
}

