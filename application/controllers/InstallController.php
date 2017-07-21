<?php


class InstallController extends Zend_Controller_Action
{
	
	private $db;
	private $file;
	
	public function init() {
		try {
			$this->db = Zend_Registry::get('db');
		} catch (Exception $e) {		}
		$this->_helper->layout->setLayout('install');
    	$this->file = APPLICATION_PATH . '/configs/config.ini';
	}
	
    private function getForm($action,$value)
    {
        $form = new Zend_Form();
        $form->setAction($this->view->url(array('action'=>$action)));
        $form->addElement('submit', $value, array('order' => 100));        
        return $form;
    } 
    
    public function indexAction() 
    {
    	$this->_redirect( "/install/step1" );
    }    

    public function step1Action()
    { 
    	$form1 = new install_dbForm();
    	$this->view->form = $form1; 
        if ($this->getRequest()->isPost() && isset($this->_request->Set) && $form1->isValid( $this->getRequest()->getPost() ) ) {
        	$values = $form1->getValues(); 
        	if ( file_exists( $file ) ){
			    $config = new Zend_Config_Ini( $this->file ,
	                                    null,
	                                    array('skipExtends'        => true,
	                                          'allowModifications' => true));
        	} else {
        		$config = new Zend_Config(array(), true);
			}
			
			
			$config->general = array();
			$config->general->db = array();
			$config->general->db->config = array();
			
			$config->general->db->adapter = $values['adapter'];
			$config->general->db->config->host = $values['host'];
			$config->general->db->config->username = $values['username'];
			$config->general->db->config->password = $values['password'];
			$config->general->db->config->dbname = $values['dbname'];
			$config->general->db->config->charset = $values['charset'];

			$db = Zend_Db::factory( $config->general->db->adapter, $config->general->db->config->toArray() );
			try {
    			$db->getConnection();
				$writer = new Zend_Config_Writer_Ini(array('config'   => $config,'filename' => $this->file ));
	    		        $writer->write(); 

				$this->view->message = $this->_helper->controllerMessage("CONNECT_DATABASE");
	    		$this->view->form = $this->getForm('step2','Further'); 
			} catch (Zend_Db_Adapter_Exception $e) {  
				$this->view->message = $this->_helper->controllerMessage("NOT_CONNECT_DATABASE");
//				$this->view->message = $this->_helper->controllerMessage("NOT_CONNECT_DATABASE")."<b>".$e->getMessage();
			} catch (Zend_Config_Exception $e) {
				$this->view->message = $this->_helper->controllerMessage("NO_CONFIG_WRITER");
			} catch (Zend_Exception $e) {
				$this->view->message = $this->_helper->controllerMessage("NO_CONNECT_ADAPTER");
			}
			
        }
    }
    
    public function step2Action() {
		$form = $this->getForm ( 'step2', 'Create' );
		$validator = new Zend_Validate_Regex('/^[A-Za-z]?[A-Za-z]?[A-Za-z]?$/');
		$validator->setMessage( "No more than three alphabetic Latin characters", Zend_Validate_Regex::NOT_MATCH );
		$prefix = $form->createElement ( 'text', 'prefix', array( 'label' => "Table_prefix") );
		$prefix->addValidator( $validator );
		$form->addElement ($prefix);
        $this->view->form = $form;
        if ( $this->getRequest()->isPost() && isset($this->_request->Create) && $form->isValid( $this->getRequest()->getPost() ) ) {
        	$values = $form->getValues(); 
        	$prefix = $values['prefix'];
        	try{
        		$create="
					"."SET AUTOCOMMIT=0;
					START TRANSACTION;
	        		CREATE TABLE ".$prefix."_balance (
					  `balance_id` int(11) NOT NULL AUTO_INCREMENT,
					  `user_id` int(11) NOT NULL,
					  `object_id` int(11) DEFAULT NULL,
					  `amount` float unsigned NOT NULL DEFAULT '0',
					  `blocked` tinyint(1) NOT NULL DEFAULT '0',
					  `blocked_description` varchar(255) NOT NULL,
					  `lastDateStorage` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  `is_juridical` tinyint(1) NOT NULL,
					  `mobile_telephone` varchar(255) DEFAULT NULL,
					  `home_telephone` varchar(255) DEFAULT NULL,
					  PRIMARY KEY (`balance_id`),
					  KEY `object` (`object_id`),
					  KEY `user` (`user_id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
	        							
					CREATE TABLE ".$prefix."_commissions (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `object_from` int(11) NOT NULL,
					  `object_to` int(11) NOT NULL,
					  `available` tinyint(1) NOT NULL DEFAULT '1',
					  `commission` float NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `objects` (`object_from`,`object_to`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
										
					CREATE TABLE ".$prefix."_object (
					  `object_id` int(11) NOT NULL AUTO_INCREMENT,
					  `abbr` varchar(5) NOT NULL,
					  `name` varchar(255) NOT NULL,
					  `exp` int(1) DEFAULT '2',
					  `description` text,
					  `factor` float DEFAULT '1',
					  `course` float NOT NULL DEFAULT '1',
					  `comission` float NOT NULL DEFAULT '0',
					  `storage` float NOT NULL DEFAULT '0',
					  `storagePeriod` enum('1','2','3') NOT NULL DEFAULT '1',
					  `minWithdrawal` float NOT NULL DEFAULT '0',
					  `availabilityWithdrawal` tinyint(1) NOT NULL DEFAULT '0',
					  `base` tinyint(1) NOT NULL DEFAULT '0',
					  `deleted` tinyint(1) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`object_id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
										
					CREATE TABLE ".$prefix."_transaction (
					  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
					  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  `user_id` int(11) DEFAULT NULL,
					  `to_user_id` int(11) DEFAULT NULL,
					  `object_id` int(11) DEFAULT NULL,
					  `amount` float DEFAULT NULL,
					  `type` int(1) NOT NULL DEFAULT '0',
					  `status` int(1) NOT NULL DEFAULT '1',
					  `description` varchar(255) DEFAULT NULL,
					  PRIMARY KEY (`transaction_id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
										
					CREATE TABLE ".$prefix."_user (
					  `user_id` int(11) NOT NULL AUTO_INCREMENT,
					  `e_mail` varchar(50) NOT NULL,
					  `password` varchar(32) NOT NULL,
					  `admin_mode` tinyint(1) DEFAULT '0',
					  PRIMARY KEY (`user_id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
					COMMIT;"
	        	;
	            $this->db->query($create);
			    $config = new Zend_Config_Ini( $this->file ,
	                                    null,
	                                    array('skipExtends'        => true,
	                                          'allowModifications' => true));
				$config->tables = array();
				$config->tables->prefix = $values['prefix'];
				$writer = new Zend_Config_Writer_Ini(array('config'   => $config,'filename' => $this->file ));
	    		$writer->write(); 
				$this->view->message = $this->_helper->controllerMessage("TABLES_CREATED");
    			$this->view->form = $this->getForm('step3','Further'); 
        	} catch (Zend_Db_Exception  $e) {
				$this->view->message = $e->getMessage();         		
        	}
		} 
    }
    
    public function step3Action() {
		$form = new RegForm();
        $this->view->form = $form;
        if ( $this->getRequest()->isPost() && isset($this->_request->Send) && $form->isValid( $this->getRequest()->getPost() ) ) {
	        $values = $form->getValues();
	        $users = new User();
	        $user = $users->createRow();
	        $user->e_mail   = $values['email'];
	        $user->password = md5($values['password']);
	        $user->admin_mode = 1;
	        $user->save();
			$this->view->message = $this->_helper->controllerMessage("ADMIN_CREATED");
			$this->view->form = $this->getForm('step4','Further'); 
        }
    }
    
    public function step4Action(){
		$form = new install_settings();
        $this->view->form = $form;
        if ( $this->getRequest()->isPost() && isset($this->_request->Set) && $form->isValid( $this->getRequest()->getPost() ) ) {
	        	$values = $form->getValues();
        		$config = new Zend_Config_Ini( $this->file ,
	                                    null,
	                                    array('skipExtends'        => true,
	                                          'allowModifications' => true));
				$config->settings = array();
				$config->settings->purseLength = $values['purseLength'];
				$config->settings->abbrLength = $values['abbrLength'];
				$writer = new Zend_Config_Writer_Ini(array('config'   => $config,'filename' => $this->file ));
	    		$writer->write(); 
				$this->view->message = $this->_helper->controllerMessage("INSTALL_COMPLET");
    			$this->view->messageLink = $this->_helper->controllerMessage("ENTER_SYSTEM");
    			$this->view->form = null; 
        }
    }
}


