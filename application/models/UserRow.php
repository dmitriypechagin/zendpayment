<?php 

/**
 * Row class for model User
 *  
 * @author Lepaysys
 * @version 1.0
 */

class UserRow extends Zend_Db_Table_Row_Abstract
{
	const INVALID_USER_NAME = "invalidUserName"; 
	
    /**
     * Return user balance - amount for all purses.
     * If $opt['accessible'] is TRUE blocked purses are skiped
     * 
     * @param  array $opt
     * @return array BalanceRow
     */
	public function getBalance( $opt = array() ) {
		$tObject = new Object();
		$tBalance = new Balance();
		$allCur = $tObject->fetchAllLive();
		foreach ($allCur as $cur) {
			$bal = $tBalance->findBalanceByUserObject( $this->user_id , $cur->object_id ) ;
			if ( $opt['аccessible'] && $bal->blocked ) continue; 
			$res[] = $bal;
		}
		return $res;
	}
	
	public function getBalanceToForm( $object_id ) {
		$tObject = new Object();
		$tBalance = new Balance();
		
		$bal = $tBalance->findBalanceByUserObject( $this->user_id , $object_id ) ;
		
		return $bal;
	}
	
    /**
     * Commission collection for storage from the user.
     * If user is admin commission collection from all users in system
     * 
     * @return void
     */
	public function takeStorageCommission() {
		if ( $this->admin_mode ) {
			$allUsers = $this->getTable()->getNoAdmin();
			foreach ($allUsers as $user) {
				$user->takeStorageCommission();
			}
			return;
		};
		
		$balances = $this->getBalance();
		foreach ( $balances as $balance ) {
			$balance->takeStorageCommission();
		}
	}
	
    /**
     * Determine whether a given valid user login.
     * Called from AuthForm. 
     * 
     * @return boolean
     */
	static function isValidUserName( $value ) {
		$validatorEmail = new Zend_Validate_EmailAddress(array('domain' => TRUE, 'allow' => Zend_Validate_Hostname::ALLOW_DNS  ));
		return $validatorEmail->isValid( $value );
	}
	
	
}