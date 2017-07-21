<?php

/**
 * Model Ticket
 *  
 * @author Nazar Vasheniak
 * @version 1.0
 */

class Ticket extends Zend_Db_Table_Abstract
{
	protected $_rowClass = 'TicketRow';
	
	protected $_referenceMap = array(
        'user' => array(
            'columns'         => array('user_id'),
            'refTableClass'   => 'User',            
            'refColumns'      => array('user_id')
	    )
	);
	
	public $type = array( 
		''=>0, 
		'new' => 1, 
		'readed' => 2, 
		'answered' => 3, 
		'closed' =>4
	);
	
	protected function _setupTableName()
    {
    	$this->_name = Zend_Registry::get('tablePrefix').'tickets';
        parent::_setupTableName();
    }
	
	protected function _setupPrimaryKey()
    {
        $this->_primary = 'user_id';
    	parent::_setupPrimaryKey();
    }   
	
	public function fetchAllLive( $select=NULL ) {
    	$select = $select ? $select :  $this->select();
    	return $this->fetchAll();
    }

	public function create($data) {
        $this->_name = Zend_Registry::get('tablePrefix').'tickets';

		$this->insert($data);
	}

	public function getTicketsByUserId($id)
	{
		$this->_name = Zend_Registry::get('tablePrefix').'tickets';

		$row = $this->fetchAll('user_id = '. $id);

		if( !$row ) {
			return false;
		}

		return $row;
	}
} 