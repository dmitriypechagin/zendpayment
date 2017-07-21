<?php 

/**
 * Row class for model Objects
 *  
 * @author Lepaysys
 * @version 1.0
 */

class ObjectRow extends Zend_Db_Table_Row_Abstract
{

	const INVALID_COURSE = "Exchange rate should be more than 0";
	
	protected $tCommissions;

	protected function tCommissions() {
		if ( !$this->tCommissions ) $this->tCommissions = new Commissions();  
		return $this->tCommissions;
	}
	
    /**
     * Return dependent commission row for object $object     
     *
     * @param  ObjectRow $object 
     * @return CommissionRow
     */
	protected function getRowCommission( $object ) {
    	$commissionTable = $this->tCommissions();
    	$select = $commissionTable->select()->where('object_to = ?', $object->object_id ); 
    	$commission = $this->findDependentRowset('Commissions','object_from', $select )->current();
    	if ( !$commission ) {
    		$commission = $commissionTable->createRow();
    		$commission->object_from = $this->object_id;	
    		$commission->object_to = $object->object_id;	
    	} 
    	return $commission;
	}
	
    /**
     * Return value of commission to object $object     
     *
     * @param  ObjectRow $object 
     * @return float
     */
	public function getCommissionToObject($object){
    	return $this->getRowCommission( $object )->commission;
    }
    
    /**
     * Return value of available conversion to object $object     
     *
     * @param  ObjectRow $object 
     * @return boolean
     */
    public function getAvailableCommissionToObject($object){
    	return $this->getRowCommission( $object )->available;
    }
    
    /**
     * Set value of commission to object $object     
     *
     * @param  ObjectRow $object 
     * @param  float $value 
     * @return ObjectRow
     */
    public function setCommissionToObject($object, $value) {
    	$commission = $this->getRowCommission( $object );
    	$commission->commission = $value;
    	$commission->save();
    	return $this;
    }
    
    /**
     * Set value of available conversion to object $object     
     *
     * @param  ObjectRow $object 
     * @param  boolean $value 
     * @return ObjectRow
     */
    public function setAvailableCommissionToObject($object, $value) {
    	$commission = $this->getRowCommission( $object );
    	$commission->available = $value;
    	$commission->save();
    	return $this;
    }
    
    /**
     * Set value of course     
     *
     * @param  float $value 
     * @return void
     */
    public function setCourse($value = 1) {
    	if (!$value) throw new Zend_Db_Adapter_Exception( self::INVALID_COURSE );
    	$this->course = $value;
    	$this->save();
    }
    
    /**
     * Set value of percent of the commission for storage and storage period     
     *
     * @param  float $value 
     * @param  int $period The storage period: 1 - day, 2 - month, 3- year 
     * @return void
     */
    public function setStorage( $value , $period ) {
    	$this->storage = $value;
    	$this->storagePeriod = $period;
    	$this->save();
    }

    /**
     * Return formated amount     
     *
     * @param  float $amount
     * @return string
     */
    public function getFormatAmount( $amount ) {
		return $amount ? number_format($amount, $this->exp, ',', '') : "0";
    }

    /**
     * Delete object     
     *
     * @return void
     */
    public function delete( ) {
		$tBalace = new Balance();
		$tTransaction = new Transaction();
		$base = $this->getTable()->getBase()->object_id;
		if ( $base==$this->object_id ) return;  
		$balances = $tBalace->findBalancesByObject( $this->object_id );
		foreach ($balances as $balance) {
			$values = Array(
				'objectFrom' => $this->object_id,
				'objectTo' => $base,
				'amount' => $balance->amount,
				'user_id' => $balance->user_id
			);
			$tTransaction->conversion( $values, 1 );
			$balance->delete();
		}
		$this->deleted = 1;
		$this->save();
    }
}
