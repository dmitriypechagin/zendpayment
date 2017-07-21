<?php

/**
 * Model Codecard
 *  
 * @author Nazar Vasheniak
 * @version 1.0
 */

class Codecard extends Zend_Db_Table_Abstract
{
	protected function _setupTableName()
    {
    	$this->_name = Zend_Registry::get('tablePrefix').'codecard';
    	parent::_setupTableName();
    }
	
	protected function _setupPrimaryKey()
    {
        $this->_primary = 'id';
    	parent::_setupPrimaryKey();
    }
	
	public function existsByUserId( $user_id ) {
    	$select = $this->select()->where(' user_id = ? ', $user_id );
    	return $this->fetchAll( $select );
    }
	
	public function generateCode()
	{
		$chars = 'ABDEFGHKNQRSTYZ23456789';
		$numChars = strlen($chars);
		$string = '';
		
		for ($i = 0; $i < 7; $i++) {
			$string .= substr($chars, rand(1, $numChars) - 1, 1);
		}
		
		return $string;
	}
	
	public function create( $user_id )
	{
		$this->_name = Zend_Registry::get('tablePrefix').'codecard';
		
		$data = array( 'code1' => $this->generateCode(),
					   'code2' => $this->generateCode(),
					   'code3' => $this->generateCode(),
					   'code4' => $this->generateCode(),
					   'code5' => $this->generateCode(),
					   'code6' => $this->generateCode(),
					   'code7' => $this->generateCode(),
					   'code8' => $this->generateCode(),
					   'code9' => $this->generateCode(),
					   'code10' => $this->generateCode(),
					   'code11' => $this->generateCode(),
					   'code12' => $this->generateCode(),
					   'code13' => $this->generateCode(),
					   'code14' => $this->generateCode(),
					   'code15' => $this->generateCode(),
					   'code16' => $this->generateCode(),
					   'code17' => $this->generateCode(),
					   'code18' => $this->generateCode(),
					   'code19' => $this->generateCode(),
					   'code20' => $this->generateCode(),
					   'code21' => $this->generateCode(),
					   'code22' => $this->generateCode(),
					   'code23' => $this->generateCode(),
					   'code24' => $this->generateCode(),
					   'code25' => $this->generateCode(),
					   'code26' => $this->generateCode(),
					   'code27' => $this->generateCode(),
					   'code28' => $this->generateCode(),
					   'code29' => $this->generateCode(),
					   'code30' => $this->generateCode(),
					   'user_id' => $user_id
		);
		
		$this->insert($data);
		
		return $this->getAdapter()->lastInsertId();
	}
	
	public function getCodeCardByUserId($user_id)
	{
		$this->_name = Zend_Registry::get('tablePrefix').'codecard';

		$row = $this->fetchRow('user_id = '. $user_id);

		if( !$row ) {
			return false;
		}

		return $row;
	}
}