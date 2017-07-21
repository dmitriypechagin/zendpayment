<?php

 
class ApanelController extends Zend_Controller_Action
{
	
	public function indexAction() {

	}

	public function settingsAction() {
		$form = new install_settings();
		$form->setDefaults( Zend_Registry::get('purse')->toArray() );
		$this->view->form = $form;
		if ( $this->getRequest()->isPost() && isset($this->_request->Set) && $form->isValid( $this->getRequest()->getPost() ) ) {
	        	$values = $form->getValues();
	        	if ( $values['abbrLength']<$values['purseLength'] ) {
		        	$file = Zend_Registry::get('fileConfig'); 
	        		$config = new Zend_Config_Ini( $file ,
		                                    null,
		                                    array('skipExtends'        => true,
		                                          'allowModifications' => true));
					$config->settings = array();
					$config->settings->purseLength = $values['purseLength'];
					$config->settings->abbrLength = $values['abbrLength'] ? $values['abbrLength'] :  Zend_Registry::get('purse')->abbrLength ;
					$writer = new Zend_Config_Writer_Ini(array('config'   => $config,'filename' => $file ));
		    		$writer->write(); 
					$this->view->message = $this->_helper->controllerMessage("VALID_OPERATION");
	        	} else {
	        		
	        	}
        }
	}
	
	public function currenciesAction() {
		if ( $this->getRequest()->getParam("act")=="edit" ) return $this->currencyEdit();
		if ( $this->getRequest()->getParam("act")=="base" ) $this->currencyMakeBase();
		if ( $this->getRequest()->getParam("act")=="delete" ) $this->currencyDelete();
		$tObject = new Object();
		$addCurForm = new newCurrencyForm( newCurrencyForm::NEW_CURRENCY );
		$this->view->addCurForm = $addCurForm;
		if ( $this->getRequest()->isPost() && $addCurForm->isValid($this->getRequest()->getPost())) {
		    $tObject->addObject($addCurForm->getValues());
        }  
        $this->allCurrency($tObject);
	}
	
	protected function currencyDelete() {
		$tObject = new Object();
		$id_currency = $this->getRequest()->getParam("currency");
		$currency = $tObject->find($id_currency)->current();
		$currency->delete();
	}
	
	protected function currencyMakeBase() {
		$tObject = new Object();
		$tObject->setBase( $this->getRequest()->getParam("currency") );
	}
	
	protected function currencyEdit() {
		$tObject = new Object();
		$addCurForm = new newCurrencyForm( newCurrencyForm::EDIT_CURRENCY );
		$this->view->addCurForm = $addCurForm;
		$id_currency = $this->getRequest()->getParam("currency");
		$currency = $tObject->find($id_currency)->current();
		if ( $this->getRequest()->isPost() ) {
		    if ( $addCurForm->isValid( $this->getRequest()->getPost() ) ) {
 		    	$currency->setFromArray( $addCurForm->getValues() );
 		    	$currency->save();
		    }
		} else {
			$addCurForm->isValid($currency->toArray());
		}   
        $this->allCurrency($tObject);
        return $this->render("currencies");
	}

	protected function allCurrency($tObject) {
		$rowsObject = $tObject->fetchAllLive();
		$this->view->currencies = $rowsObject;
		$this->view->baseObject = $tObject->getBase();
		if ( !count($rowsObject)) { $this->view->nocurrencies = "no currencies"; } else { $this->view->nocurrencies = ""; }
	}
	
	public function exchangeAction() {
		$tObject = new Object();
		if ( $this->getRequest()->isPost() ) {
			$values = $this->getRequest()->getParams();
			$filter = new Zend_Filter_LocalizedToNormalized();
        	$valid_float = new floatNormalizedValidator(  array( min =>0 ) ) ;
			try{
	            foreach ($values["course"] as $id_currency => $course) {
					$normCourse = $filter->filter( $course );
	            	$currency = $tObject->find($id_currency)->current();
					if ( $valid_float->isValid($normCourse) ) {
						$currency->setCourse( $normCourse );
					} else {
						$currency->setCourse( 1 );
					}
	            } 
	            foreach ($values["available"] as $id_currency => $commisions) {
	            	foreach ( $commisions as $id_currency_to => $value) {
	            		$tObject->setAvailable( $id_currency, $id_currency_to, $value );
	            	} 
	            } 
	            foreach ($values["commission"] as $id_currency => $commisions) {
	            	foreach ( $commisions as $id_currency_to => $comission) {
	            		$normComission = $filter->filter( $comission );
	            		if ( $valid_float->isValid($normComission) ) {
	            			$tObject->setCommission( $id_currency, $id_currency_to, $normComission );
	            		} else {
	            			$tObject->setCommission( $id_currency, $id_currency_to, 0 );
	            		}
	            	} 
	            } 
	            foreach ($values["storage"] as $id_currency => $storage) {
					$normStorage = $filter->filter( $storage["storage"] );
	            	$currency = $tObject->find($id_currency)->current();
					if ( $valid_float->isValid($normStorage) ) {
	            		$currency->setStorage($normStorage,$storage["period"]);
					} else {
	            		$currency->setStorage( 0 ,$storage["period"]);
					}
	            }
			} catch (Zend_Db_Adapter_Exception $e) {
				echo $e->getMessage();
        	}
		}
		$baseCur = $tObject->getBase();
		$nonBaseCur = $tObject->getNonBase();
		$this->view->baseCur = $baseCur;
		$this->view->nonBaseCur = $nonBaseCur;
		$allCur = $tObject->fetchAllLive();
		$this->view->allCur = $allCur;
	}

	public function issueAction() {
		$tUser = new User();
		$user = Zend_Auth::getInstance()->getIdentity();
		$user = $tUser->find( $user->user_id )->current();
		$iForm = new issueForm( $this->view->balance = $user->getBalance() );
		$this->view->iForm = $iForm;
		$defaultNamespace = new Zend_Session_Namespace('Default');		
    	// Confirm transfer
		if ( $this->getRequest()->getPost('Confirm') ) {
			$values = $defaultNamespace->issue;	
			$tTransaction = new Transaction();
			try {
				switch ( $values['oper'] ) {
					case 1: $tTransaction->issue( array( to_user_id => $user->user_id, object_id => $values[purse], amount => $values[amount], description => $values[note] ) ); break;
					case 2: $tTransaction->repayment( array( user_id => $user->user_id, object_id => $values[purse], amount => $values[amount], description => $values[note] ) ); break;
				}
				$this->view->message = $this->_helper->controllerMessage("VALID_OPERATION");
			} catch ( Zend_Db_Adapter_Exception $e ) {
				$this->view->message = $e->getMessage();
			}
        	return $this->render('validissue');	
		}
    	// Send transfer
		if ( $this->getRequest()->getPost('Send') && $iForm->isValid($this->getRequest()->getPost())) {
			
			$values = $iForm->getValues();
      		$defaultNamespace->issue = $values;
			$this->view->values = $values;
			return $this->render('confirmissue');	
		}
		// Change transfer
		if ( $this->getRequest()->getPost('Change') ) {
			$values = $defaultNamespace->issue;	
			$iForm->setDefaults( $values );
		}
		$this->view->balance = $user->getBalance() ;
	}

	public function viewingAction() {
    	$tBalance = new Balance();
    	$select = $tBalance->select();
		$defaultNamespace = new Zend_Session_Namespace('Default');		
		$fForm = new filtrPurseForm();
		if ( isset($defaultNamespace->filtrForm) && $fForm->isValid( $defaultNamespace->filtrForm ) ) {
        	$values = $defaultNamespace->filtrForm;
    	}
		if ( $this->getRequest()->isPost() && $fForm->isValid($this->getRequest()->getPost())) {
			$values = $fForm->getValues();
        	$defaultNamespace->filtrForm = $values;
    	}
		if ( $values['id'] ) $select->where("user_id = ?", $values['id'] ); 
		if ( $values['email'] ) {
			$tUser = new User();
			$selectUser = $tUser->select()->where('e_mail = ?', $values['email'] );
			$user = $tUser->fetchRow( $selectUser ); 
			if ( $user ) $select->where("user_id = ?", $user->user_id );
		} 
		if ( $values['purse'] ) {
			$tObject = new Object();
    		$tUser = new User();
			$select->where( 'user_id = ?', $tUser->getIdByPurse( $values['purse'] ) ) 
	               ->where( 'object_id = ?', $tObject->findByPurse( $values['purse'] )->object_id );
		}
		if ( $values['amount1'] ) $select->where(" amount >= ?", $values['amount1'] );
		if ( $values['amount2'] ) $select->where(" amount <= ?", $values['amount2'] );
		
		if ( $this->_getParam('filed') && $this->_getParam('order_t') ) $select->order($this->_getParam('filed')." ". $this->_getParam('order_t') );

		$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$this->view->paginator = $paginator;
		$this->view->fForm = $fForm;
		$this->view->filed = $this->_getParam('filed');
		$this->view->order_t = $this->_getParam('order_t');
	}
	
	public function blockingAction() {
		$sForm = new seachPurseForm();
		$this->view->sForm = $sForm;
    	if ( $this->getRequest()->isPost() ) {
    		$tBalance = new Balance();
    		$bForm = new blockedForm();
			$this->view->bForm = $bForm;
			if ( $this->getRequest()->getPost('Seach') && $sForm->isValid($this->getRequest()->getPost())) {
				$values = $sForm->getValues();
				$purse = $tBalance->findBalanceByPurse($values['purse']);
				$bForm->setDefaults( array_merge( $values, array( 'blocked'=>$purse->blocked) ));
    	    }
    		if ( $this->getRequest()->getPost('Set') && $bForm->isValid($this->getRequest()->getPost())) {
				$values = $bForm->getValues();
				$tBalance->findBalanceByPurse($values['purse'])
						 ->setBlocked( $values['blocked'], $values['note'] );
				$this->view->message = $this->_helper->controllerMessage("VALID_OPERATION"); 
    		}
    		$this->view->purse = $purse;
    	}
    	
	}
	
	public function estimationAction() {
    	$tBalance = new Balance();
    	$ao = $tBalance->amountObjects();
		$this->view->ao = $ao ;	
	}
	
	public function verificationAction() {
		$tVerification = new Verification();
		$this->view->notVerificated = $tVerification->getNotVerificated();
		$this->view->Verificated = $tVerification->getVerificated();
		
		if ( isset($_GET['verificate']) ) {
			$tUser = new User();
			if ( $_GET['verificate'] == 'true' ) {
				$tVerification->verificate( $_GET['user_id'] );
				$tUser->updateUserData( array(
						'firstname' => $_GET['firstname'],
						'lastname' => $_GET['lastname'],
						'middlename' => $_GET['middlename'],
						'country' => $_GET['country'],
						'city' => $_GET['city'],
						'birthdate' => $_GET['birthdate'],
						'docnum' => $_GET['docnum'],
						'docdate' => $_GET['docdate'],
						'docwho' => $_GET['docwho'],
						'verificated' => 1,
						'account_type' => 1
					), 'user_id = '. $_GET['user_id'] );
				
				echo '<script>jQuery(document).ready(function() {
					swal({
						title: "Успешно!",
						text: "Заявка на верификацию пользователя ' . $_GET['user_id'] . ' одобрена!",
						type: "success",
						confirmButtonColor: "#007AFF"
					});
				});
				</script>';
				echo "<script>setTimeout(function() { window.location = '/cp/apanel/verification' }, 3000)</script>";
			}
		}
	}
	
}

