<?php

/**
 * Db Install Form. 
 */

class Install_dbForm extends Zend_Form {
	
	public function init() {
        $this->setMethod('post');
        
        if (extension_loaded('pdo') && in_array('mysql', PDO::getAvailableDrivers()) ) 
        	{ $adapters['Pdo_Mysql'] = 'Pdo_Mysql'; }
        if (extension_loaded('mysqli')) { $adapters['Mysqli'] = 'Mysqli'; }   
        	
        $adapter  = $this->createElement('select', 'adapter', array ( 
        						'label' => 'adapter', 
        						'multiOptions' => $adapters
        					) )
        				 ->setValue(0);
/*        
		$adapter = $this->createElement('hidden', 'adapter')
        				->setValue('PDO_MYSQL');
*/        				
		$host = $this->createElement('text', 'host', array( 'label' =>'host' ))
        				->setValue('localhost');
		$dbname = $this->createElement('text', 'dbname', array( 'label' =>'dbname' ));
		$username = $this->createElement('text', 'username', array( 'label' =>'username' ));
		$password = $this->createElement('password', 'password', array( 'label' =>'password' ));
		$charset = $this->createElement('hidden', 'charset')
        				->setValue('UTF8');
        				
		$this->addElement($adapter)
		     ->addElement($dbname)
			 ->addElement($host)
		     ->addElement($username)
		     ->addElement($password)
		     ->addElement($charset)
		     ->addElement('submit', 'Set', array('label' => 'Set') );
	}
	
}
