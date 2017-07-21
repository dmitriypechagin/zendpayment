<?php 

/**
 * Row class for model Balance
 *  
 * @author Lepaysys
 * @version 1.0
 */

class BalanceRow extends Zend_Db_Table_Row_Abstract
{
	const NO_VALID_PURSE = "The purse '%value%' doesn't exist";
	const PURSE_BLOCKED = "Operations with the blocked purse are inaccessible";
	const NO_MEANS = "There are no means";
	
	public $purse;
	
    /**
     * Return string type balance     
     *
     * @see  Balance::$strBlocked
     * @return string
     */
	public function getBlocked() {
    	return $this->getTable()->strBlocked[$this->blocked];
    }
	
    /**
     * Return purse name      
     *
     * @return string
     */
    public function purseName() {
		if ( !$this->purse ) {
			$purseLength = Zend_Registry::get('purse')->purseLength;
			$abbrLength = Zend_Registry::get('purse')->abbrLength;
			$cur = $this->findParentRow('Object'); 
			$user = $this->findParentRow('User');
			$this->purse = $cur->abbr.str_pad($user->user_id, $purseLength-$abbrLength, "0", STR_PAD_LEFT);
		}
		return $this->purse;
	}
	
    /**
     * Determine whether a given purse name for non deleted objects 
     *
     * @param  string $value Purse name
     * @return boolean
     */
	public function isValidPurseName( $value ) {
        $tUser = new User();
        $tObject = new Object();
		return ( $tObject->findByPurse( $value ) && $tUser->exists( $tUser->getIdByPurse( $value ) ) );		
	}
	
	public function isValidUser( $value ) {
        $tUser = new User();
		return ( $tUser->exists( $value ) );		
	}

    /**
     * Determine whether a given purse name for all objects include deleted 
     *
     * @param  string $value Purse name
     * @return boolean
     */
	public function isValidAllPurseName( $value ) {
        $tUser = new User();
        $tObject = new Object();
		return ( $tObject->findByPurse( $value, 0 ) && $tUser->exists( $tUser->getIdByPurse( $value ) ) );		
	}
	
    /**
     * Return formated amount     
     *
     * @return string
     */



	public function getBalance() {
		$cur = $this->findParentRow('Object'); 
		return $cur->getFormatAmount( $this->amount ); 
	}
	
	public function getIcon() {
		$cur = $this->findParentRow('Object');
		return $cur->icon;
	}
	
	public function getSymbol() {
		$cur = $this->findParentRow('Object');
		return $cur->symbol;
	}

    /**
     * Return reduced name for parent object      
     *
     * @return string
     */
	public function getObjectAbbr() {
		return $this->findParentRow('Object')->abbr;
	}
	
    /**
     * Set blocking type purse.       
     *
     * @see  Balance::$strBlocked
     * @param  int $value 
     * @param  string $desc Description blocking 
     * @return void
     */
	public function setBlocked( $value, $desc ) {
		$this->blocked = $value;
		$this->blocked_description = $value ? $desc : "";
		$this->save();  
		$this->takeStorageCommission();
	}
	
    /**
     * Change of an amount of means on a purse       
     *
     * @param  float $amount
     * @return void
     */
	public function change( $amount ) {
		if ( $amount<0 && $this->blocked ) { throw new Zend_Db_Adapter_Exception( self::PURSE_BLOCKED ); }
		if ( ($this->amount + $amount) < 0 ) { throw new Zend_Db_Adapter_Exception( self::NO_MEANS ); } 
		$this->amount = $this->amount + $amount;
		$this->save(); 
	}

    /**
     * Commission collection for storage       
     *
     * @return void
     */
	public function takeStorageCommission() {
		if ( $this->blocked ) return;
		$object = $this->findParentObject();
		switch ($object->storagePeriod) {
			case 1:
				$compareFormat = "d.m.Y";
				$cycleFormat = "+1 day";
				$transactionFormat = "Y-m-d";
				$suffixForamt = "";  
				break;
			case 2:
				$compareFormat = "m.Y";
				$cycleFormat = "+1 month"; 
				$transactionFormat = "Y-m";
				$suffixForamt = "-01";  
				break;
			case 3:
				$compareFormat = "Y";
				$cycleFormat = "+1 year"; 
				$transactionFormat = "Y";
				$suffixForamt = "-01-01";  
				break;
			default: return;
		} 		
        $q = time();
		if ( $object->storage && $this->amount ) {
			$tUser = new User();
			$tTransaction = new Transaction();
			$t=time();
			$q = strtotime($this->lastDateStorage);
			while ( date( $compareFormat, $q) != date( $compareFormat, $t) ) {
				$q=strtotime( $cycleFormat, $q);
				$data = array (
					'date' => date($transactionFormat,$q).$suffixForamt,
					'user_id' => $this->user_id,
					'to_user_id' => $tUser->getAdmin()->user_id,
					'object_id' => $this->object_id,
					'amount' => $this->amount*$object->storage/100,
					'description' => 'storage comision for '.date( $compareFormat, $q) 
				);
				$tTransaction->storage( $data );
				$this->amount-= $data['amount']; 
			}
		}
		$this->lastDateStorage = date($transactionFormat,$q).$suffixForamt;
		$this->save(); 
	}

}