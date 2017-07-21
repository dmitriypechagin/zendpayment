<?php

/**
 * Model Invoice
 *  
 * @author Nazar Vasheniak
 * @version 1.0
 */

class Invoice extends Zend_Db_Table_Abstract
{
	const YUOR_PURSE = "You indicated your purse as a destination";
	
	protected $_rowClass = 'InvoiceRow';
	
	public $status = array( 
		'in handling'=>0, 
		'executed' => 1, 
		'cancel' => 2 
	);
	
	protected function _setupTableName()
    {
    	$this->_name = Zend_Registry::get('tablePrefix').'invoice';
    	parent::_setupTableName();
    }

	protected function _setupPrimaryKey()
    {
        $this->_primary = 'invoice_id';
    	parent::_setupPrimaryKey();
    }
	
	public function fetchAllLive( $select=NULL ) {
    	$select = $select ? $select :  $this->select();
    	return $this->fetchAll( $select );
    }
	
	public function existsByUserId( $user_id ) {
    	$select = $this->select()->where(' user_id = ? ', $user_id );
    	return $this->fetchAll( $select );
    }
	
	public function existsByToUserId( $to_user_id ) {
    	$this->_name = Zend_Registry::get('tablePrefix').'invoice';

		$row = $this->fetchAll('to_user_id = '. $to_user_id);
		
		if( !$row ) {
			return false;
		}

		return $row;
    }
	
	public function dataByInvoiceId ( $invoice_id ) {
		
		$this->_name = Zend_Registry::get('tablePrefix').'invoice';

		return $this->fetchRow( $this->select()->where(' invoice_id = ' . $invoice_id) );
	}
	
	public function updateInvoice($data, $invoice_id)
	{
		$this->_name = Zend_Registry::get('tablePrefix').'invoice';

		$row = $this->update($data, 'invoice_id = '. $invoice_id);
		
		if( !$row ) {
			return false;
		}

		return true;
	}
	
	/**
 	 * Creation of new invoice
 	 * 
     * @param  	array $data 
     * 			int $data['user_id'] Sender user id 
     * 			int $data['to_user_id'] Receiver user id 
     * 			int $data['object_id'] Object id 
     * 			int $data['amount'] Ammount invoice 
     * 			int $data['description'] Description invoice 
     * @return void
     */
	public function create( array $data ) {
		if ( $data['user_id'] == $data['to_user_id'] ) { throw new Zend_Db_Adapter_Exception( self::YUOR_PURSE ); }
        $this->insert( $data ); 
	}
	
	/**
 	 * Invoice canceling
 	 * 
     * @param  int $id invoice   
     * @return void
     */
	public function cancel( $id ) {
		$iv = $this->find( $id )->current();
		$data = $iv->toArray();
		$user_id = $data['user_id'];
		$to_user_id = $data['to_user_id'];
		$data['description'].= "(cancel #".$data['invoice_id'].")";
		$data['invoice_id'] = NULL;
		$iv->status = $this->status['cancel'];
		$iv->save(); 
	}
	
	public function invoicing( $data ) {
		if ( $data['mode'] ) {
			// admin - no commission
			$data['amount'] = $data['amount']*(1-$data['commission']/100);
			unset($data['mode']); unset($data['commission']);
			$this->create( $data );
		} else {
			$tCommissions = new Commissions();
			$tUser = new User();
			$toUserData = $tUser->getUserDataById($data['to_user_id']);
		    unset($data['mode']); unset($data['commission']);
			/* $commission = $tCommissions->getTransferCommissionByObject( $data['object_id'] )->commission; */
			if ( $toUserData['account_type'] == 0 ) {
				$commission = 1.7;
			}
			else if ( $toUserData['account_type'] == 1 ) {
				$commission = 0.5;
			}
			else if ( $toUserData['account_type'] == 2 ) {
				$commission = 0.4;
			}
			else if ( $toUserData['account_type'] == 3 ) {
				$commission = 0.3;
			}
			$data['amount_with_comm'] = $data['amount']*(1+$commission/100);
			$this->create( $data );
		}
	}
	
	public function invoicePay( $data ) {
		$tBalance = new Balance();
		if ( $data[user_id] ) $userBalance = $tBalance->findBalanceByUserObject( $data['user_id'], $data['object_id'] );
		if ( $data[to_user_id] ) $to_userBalance = $tBalance->findBalanceByUserObject( $data['to_user_id'], $data['object_id'] );
		
		// transfer transaction
		if ($to_userBalance) $to_userBalance->change( -$data['amount_with_comm'] );
		if ($userBalance) $userBalance->change( $data['amount'] ); 
		
		// commission transaction
		$tUser = new User();
		$amount = $data['amount'];
		$data['amount'] = ($data['amount_with_comm'] - $amount);
		$data['description'] = $data['description']."(commission)";
		$data['user_id'] = $tUser->getAdmin()->user_id; 
		$adminBalance = $tBalance->findBalanceByUserObject( $data['user_id'], $data['object_id'] );
		if ($adminBalance) $adminBalance->change( $data['amount'] );
		
	}
	
}