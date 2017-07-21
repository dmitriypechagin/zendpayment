<?php

/**
 * Model Verification
 *  
 * @author Nazar Vasheniak
 * @version 1.0
 */
	
class Verification extends Zend_Db_Table_Abstract
{
	protected function _setupTableName()
    {
    	$this->_name = Zend_Registry::get('tablePrefix').'verification';
    	parent::_setupTableName();
    }

	public function create( array $data )
	{
		$this->_name = Zend_Registry::get('tablePrefix').'verification';
		
		$this->insert( $data );
	}
	
	public function verificate( $user_id )
	{
		$this->_name = Zend_Registry::get('tablePrefix').'verification';
		
		$data = array('status' => 1);
		
		$row = $this->update($data, $user_id);

		if( !$row ) {
			return false;
		}

		return true;
	}
	
	public function getAllRequests()
	{
		$this->_name = Zend_Registry::get('tablePrefix').'verification';
		
		$row = $this->fetchAll();

		if( !$row ) {
			return false;
		}

		return $row;
	}
	
	public function getNotVerificated()
	{
		$this->_name = Zend_Registry::get('tablePrefix').'verification';
		
		$row = $this->fetchAll('status = 0');

		if( !$row ) {
			return false;
		}

		return $row;
	}
	
	public function getVerificated()
	{
		$this->_name = Zend_Registry::get('tablePrefix').'verification';
		
		$row = $this->fetchAll('status = 1');

		if( !$row ) {
			return false;
		}

		return $row;
	}
}