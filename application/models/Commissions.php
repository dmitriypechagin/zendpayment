<?php

/**
 * Model Commissions
 *  
 * @author Lepaysys
 * @version 1.0
 */
	
class Commissions extends Zend_Db_Table_Abstract
{

	/**
 	 * @see Zend_Db_Table_Abstract
     * @var array
     */
	protected $_referenceMap = array(
	    'object_from' => array(
            'columns'         => array('object_from'),
            'refTableClass'   => 'Object',            
            'refColumns'      => array('object_id')
	    ),
	    'object_to' => array(
            'columns'         => array('object_to'),
            'refTableClass'   => 'Object',            
            'refColumns'      => array('object_id')
	    )
    );
	
	/**
 	 * @see Zend_Db_Table_Abstract
     * @var string
     */
    protected function _setupTableName()
    {
    	$this->_name = $this->_name = Zend_Registry::get('tablePrefix').'commissions';
        parent::_setupTableName();
    }    
	    
    /**
     * Return transfer commissions rowset   
     *
     * @return CommissionsRowset
     */
    public function getTransferCommissions() {
    	return $this->fetchAll( $this->select()->where(' object_from = object_to') );
    }

    /**
     * Return transfer commissions row for object $id   
     *
     * @param $id identifier object
     * @return CommissionsRow
     */
    public function getTransferCommissionByObject( $id ) {
    	return $this->fetchRow( $this->select()->where(' object_from = object_to AND object_from = ?', $id ) );
    }
    
    /**
     * Return conversion commissions rowset   
     *
     * @return CommissionsRowset
     */
    public function getAllConversionCommissions() {
    	return $this->fetchAll( $this->select()->where(' object_from <> object_to') );
    }

    /**
     * Return conversion commissions row from object $id1 to object $id2   
     *
     * @param $id1 identifier object
     * @param $id2 identifier object
     * @return CommissionsRow
     */
    public function getConversionCommission( $id1, $id2) {
    	return $this->fetchRow( $this->select()->where(' object_from=?', $id1)->where('object_to=?',$id2) );
    }
}

