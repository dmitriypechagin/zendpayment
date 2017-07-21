<?php

/**
 * Model Object
 *  
 * @author Lepaysys
 * @version 1.0
 */


class Object extends Zend_Db_Table_Abstract {

	/**
 	 * @see Zend_Db_Table_Abstract
     * @var array
 	 */
	protected $_dependentTables = array('Balance','Transaction','Commissions');

    /**
 	 * @see Zend_Db_Table_Abstract
     * @var string
     */
    protected $_rowClass = 'ObjectRow';
    
    /**
 	 * @see Zend_Db_Table_Abstract
     * @return void
     */
    protected function _setupTableName()
    {
    	$this->_name = Zend_Registry::get('tablePrefix').'object';
        parent::_setupTableName();
    }

	/**
 	 * Creation of new object
     * @param  	array $data 
     * 			string $data['abbr'] The reduced name 
     * 			string $data['name'] Name object
     * 			int $data['exp'] Accuracy of representation 
     * 			string $data['description'] Description object 
     * 			float $data['factor'] Factor (reserve)
     * 			float $data['course'] Course basic currency 
     * 			float $data['comission'] The commission on a witdarawal 
     * 			float $data['storage'] The storage commission 
     * 			enum('1', '2', '3') $data['storagePeriod'] The storage period: 1 - day, 2 - month, 3- year 
     * 			float $data['minWithdrawal'] Minimum amount for a witdarawal  
     * 			boolean $data['availabilityWithdrawal'] Availability to witdarawal    
     * 			boolean $data['base'] : 1 - basic currency, 0 - non basic currency    
     * 			boolean $data['deleted'] : 1 - remote currency, 0 - availability      
     * @return void
     */
    public function addObject( $data )    {
    	if ( !count( $this->getBase() )) $data['base']=1;
    	$this->insert($data);
    }
    
    /**
     * Determine whether a given reduced name object 
     *
     * @param  string $abbr
     * @return boolean
     */
    public function existsByAbbr( $abbr ) {
    	$select = $this->select()->where(' abbr = ? ', $abbr );
    	return count( $this->fetchRow( $select ) ) > 0;
    }
    
    /**
     * Return all non deleted objects  
     *
     * @param  Zend_Db_Table_Select $select
     * @return ObjectRowset
     */
    public function fetchAllLive( $select=NULL ) {
    	$select = $select ? $select :  $this->select();
    	return $this->fetchAll( $select->where(' deleted = ? ', 0 ) );
    }
    
    /**
     * Return object row for reduced name 
     *
     * @param  boolean $onlyLive If TRUE review only not remote objects.
     * @return ObjectRow
     */
    public function findByAbbr( $abbr, $onlyLive = TRUE ) {
    	$rows = $this->fetchAllLive( $this->select()->where(' abbr = ? ', $abbr ) );
		if ( !count($rows) & !$onlyLive ) $rows = $this->fetchAll( $this->select()->where(' abbr = ? ', $abbr ) );
		return $rows->current();
    }
    
    /**
     * Return object row for purse name 
     *
     * @param  boolean $onlyLive If TRUE review only not remote objects.
     * @return ObjectRow
     */
    public function findByPurse( $purse, $onlyLive = TRUE ) {
    	return $this->findByAbbr( substr( $purse, 0, Zend_Registry::get('purse')->abbrLength ), $onlyLive );
    }
	
	public function abbrByObjectId ( $object_id ) {
		$rows = $this->fetchAllLive( $this->select()->where(' object_id = ? ', $object_id ) );
		
		return $rows;
	}
    
    /**
     * Return object row basic currency  
     *
     * @return ObjectRow
     */
    public function getBase() {
    	return $this->fetchRow( $this->select()->where(' base = 1 ') );
    }

    /**
     * Return objects no basic currencys  
     *
     * @param  Zend_Db_Table_Select $select
     * @return ObjectRowset
     */
    public function getNonBase( $select=NULL ) {
    	$select = $select ? $select :  $this->select();
    	return $this->fetchAllLive( $select->where(' base = 0 ') );
    }
    
    /**
     * Return course for identifier object   
     *
     * @param  int $id Identifier object
     * @return ObjectRowset
     */
    public function getCourseById( $id ) {
    	return $this->find( $id )->current()->course;
    }
    
    /**
     * Returns course of conversion from object $id1 in object $id2   
     *
     * @param  int $id1 Identifier object
     * @param  int $id2 Identifier object
     * @return float
     */
    public function getConversionCourse( $id1, $id2 ) {
    	$c1 = $this->getCourseById( $id1 );
    	$c2 = $this->getCourseById( $id2 );
    	return $c2/$c1; 
    }
    
    /**
     * Set course of conversion from object $object_from in object $object_to   
     *
     * @param  int $object_from Identifier object
     * @param  int $object_to Identifier object
     * @param  float $value
     * @return void
     */
    public function setCommission( $object_from, $object_to, $value ) {
    	$cur = $this->find( $object_from )->current();
    	$cur->setCommissionToObject( $this->find( $object_to )->current() , $value);
    	if ( $object_from == $object_to ) return ; // transfer commission
    	// recount nonbase commissions
    	$base = $this->getBase();
    	$nonBaseCur = $this->getNonBase();
    	$this->getRowsetsForCommission( $object_from, $object_to, $rowset1, $rowset2);
    	
    	foreach ($rowset1 as $currency_from) {
    		foreach ($rowset2 as $currency_to) {
    			$value = $currency_from->getCommissionToObject( $base ) + $base->getCommissionToObject( $currency_to );
    			$currency_from->setCommissionToObject( $currency_to , $value);
    		}
    	}
    }
    
    /**
     * Set flag availability to conversion from object $object_from in object $object_to   
     *
     * @param  int $object_from Identifier object
     * @param  int $object_to Identifier object
     * @param  float $value
     * @return void
     */
    public function setAvailable( $object_from, $object_to, $value ) {
    	$cur = $this->find( $object_from )->current();
    	$cur->setAvailableCommissionToObject( $this->find( $object_to )->current() , $value);
    	if ( $object_from == $object_to ) return; // transfer commission
    	// recount nonbase available commission
    	$base = $this->getBase();
    	$this->getRowsetsForCommission( $object_from, $object_to, $rowset1, $rowset2);
    	foreach ($rowset1 as $currency_from) {
    		foreach ($rowset2 as $currency_to) {
    			$value = $currency_from->getAvailableCommissionToObject( $base ) && $base->getAvailableCommissionToObject( $currency_to );
    			$currency_from->setAvailableCommissionToObject( $currency_to , $value);
    		}
    	}
    	
	}
	
    /**
     * Sets two objects rowsets for recount commission and availability to witdarawal.
     * Called from setCommission() and setAvailable()      
     *
     * @param  int $object_from Identifier object
     * @param  int $object_to Identifier object
     * @param  float $value
     * @return void
     */
	protected function getRowsetsForCommission( $object_from, $object_to, &$rowset1, &$rowset2 ) {
    	$base = $this->getBase();
    	if ( $base->object_id == $object_from ) {
    		$select = $this->select()->where('object_id <> ?', $object_to );
    		$rowset1 = $this->getNonBase( $select );
    		$rowset2 = $this->find( $object_to );
    	}
    	if ( $base->object_id == $object_to ) {
    		$select = $this->select()->where('object_id <> ?', $object_from );
    		$rowset2 = $this->getNonBase( $select );
    		$rowset1 = $this->find( $object_from );
    	}
	}
	
    /**
     * Set new basic currency. 
     *
     * @param  int $id Identifier object
     * @return void
     */
	public function setBase( $id ){
    	$oldBase = $this->getBase();
    	if ( $oldBase->object_id == $id ) return;
    	$newBase = $this->find( $id )->current();
    	$courseFactor = $newBase->course;
    	$allCurrency = $this->fetchAllLive();
    	foreach ($allCurrency as $currency) {
    		$currency->course = $currency->course/$courseFactor;
    		$currency->save();
    	} 
    	$oldBase->base = 0;
		$oldBase->save();
		$newBase->base = 1;
		$newBase->save();
		// recount nonbase commissions
		$nonBaseCur = $this->getNonBase();
		foreach ( $nonBaseCur as $currency ) {
			if ( $currency->object_id == $oldBase->object_id ) continue;
			$commission = $currency->getCommissionToObject( $newBase );
			$this->setCommission( $currency->object_id, $newBase->object_id, $commission );
			$commission = $newBase->getCommissionToObject( $currency ); 
			$this->setCommission( $newBase->object_id, $currency->object_id, $commission );
		}
    }
}

