<?php

/**
 * Model Fill
 *  
 * @author Nazar Vasheniak
 * @version 1.0
 */

class Fill extends Zend_Db_Table_Abstract
{
	public function create( array $data ) {
        $this->_name = Zend_Registry::get('tablePrefix').'fill';

		$this->insert( $data );
		
		return $this->getAdapter()->lastInsertId();
	}

	public function pay( array $data )
	{
		$this->_name = Zend_Registry::get('tablePrefix').'balance';
		
		$tBalance = new Balance();
		
		$userBalance = $tBalance->findBalanceByUserObject( $data['user_id'], 17 );
		if ($userBalance) $userBalance->change( $data['amount'] );
		$this->insert( $data ); 
	}
	
	public function getFillData($fill_id)
	{
		$this->_name = Zend_Registry::get('tablePrefix').'fill';
		
		return $this->fetchRow( $this->select()->where('id = "'. $fill_id .'"') );
	}
	
	public function updateFill($data, $fill_id)
	{
		$this->_name = Zend_Registry::get('tablePrefix').'fill';

		$row = $this->update($data, 'id = '. $fill_id);
		
		if( !$row ) {
			return false;
		}

		return true;
	}
	
} 