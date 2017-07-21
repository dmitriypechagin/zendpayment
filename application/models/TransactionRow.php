<?php 

/**
 * Row class for model Transaction
 *  
 * @author Lepaysys
 * @version 1.0
 */

class TransactionRow extends Zend_Db_Table_Row_Abstract
{
	protected $tBalance;
	
	protected function tBalance() {
		if ( !$this->tBalance ) $this->tBalance = new Balance();  
		return $this->tBalance;
	}
	
    /**
     * Return purse name sender user.
     * 
     * @return string
     */
	public function purseNameFrom() {
		return $this->tBalance()->purseNameByUserObject( $this->user_id, $this->object_id );			
	}
	
    /**
     * Return purse name receiver user.
     * 
     * @return string
     */
	public function purseNameTo() {
		return $this->tBalance()->purseNameByUserObject( $this->to_user_id, $this->object_id );
	}
	
    /**
     * Return formatted amount transaction.
     * 
     * @return float
     */
	public function getAmount() {
		$cur = $this->findParentRow('Object'); 
		return $cur->getFormatAmount( $this->amount ); 
	}
	
    /**
     * Return string status transaction
     * 
     * @return string
     */
	public function getStatus() {
		$arr = $this->getTable()->status;
		$a = array_combine( array_values( $arr ), array_keys( $arr ) );
		return $a[ $this->status ];			
	}
}
