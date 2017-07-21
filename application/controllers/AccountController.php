<?php

class AccountController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $tUser = new User();
    	$user = Zend_Auth::getInstance()->getIdentity();
		$user = $tUser->find( $user->user_id )->current();
		
		$this->view->balance = $user->getBalance();
		$purForm = new pursesForm( $user->getBalance( array ( 'аccessible' => TRUE ) ) );
		$this->view->purForm = $purForm;
    	$tCommissions = new Commissions();
    	
    	$tObject = new Object();
    	$this->view->Objects = $tObject->fetchAll(); 
    	$this->view->balance = $user->getBalance();
		
		$tCodecard = new Codecard();
		$cardData = $tCodecard->getCodeCardByUserId( $user->user_id );
		$this->view->cardData = $cardData;
		
		$phoneForm = new phoneForm();
		$confirmSmsForm = new confirmSmsForm();
		$authTypeForm = new authTypeForm();
		$this->view->phoneForm = $phoneForm;
		$this->view->confirmSmsForm = $confirmSmsForm;
		$this->view->authTypeForm = $authTypeForm;
		$this->view->firstname = $user->firstname;
		$this->view->lastname = $user->lastname;
		$this->view->middlename = $user->middlename;
		$this->view->birthdate = $user->birthdate;
		$this->view->e_mail = $user->e_mail;
		$this->view->phone = $user->phone;
		$this->view->activated = $user->activated;
		$this->view->verificated = $user->verificated;
		$this->view->account_type = $user->account_type;
		$this->view->auth_type = $user->auth_type;
		$this->view->confirmation_type = $user->confirmation_type;
		$this->view->fg_type = $user->fg_type;
		$this->view->in_notifications = $user->in_notifications;
		$this->view->out_notifications = $user->out_notifications;
		$this->view->sms_balance = $user->sms_balance;
		$this->view->code_card = $user->code_card;
		
		if ( $this->getRequest()->getPost('SmsFillPay') ) {
			$tObject = new Object();
			$abbrObject = $tObject->findByAbbr( $_POST['abbr'] );
			
			$_SESSION['sms_user_id'] = $user->user_id;
			$_SESSION['sms_object_id'] = $abbrObject->object_id;
			$_SESSION['sms_amount'] = $_POST['amount'];
			$_SESSION['smsConfirmCode'] = substr(uniqid(), 0, 8);
			
			$to = $user->e_mail;
			$subject = 'Подтверждение транзакции';
			$text = $_SESSION['smsConfirmCode'];
				
			$tUser->sendEmail($to, $subject, $text);
			
			echo "<script>jQuery(document).ready(function() {
				jQuery('#sms-fill-confirm-modal').modal('show');
				});
			</script>";
		}

		if ( $this->getRequest()->getPost('SmsFillConfirm') ) {
			if ( isset($_POST['SmsFillConfirmCode']) && $_POST['SmsFillConfirmCode'] == $_SESSION['smsConfirmCode'] ) {
				$smsFill = $tUser->smsFill( array( 
					'user_id' => $user->user_id,
					'object_id' => $_SESSION['sms_object_id'],
					'amount' => $_SESSION['sms_amount'],
					'sms_balance' => $user->sms_balance
				));
				
				unset($_SESSION['sms_user_id']);
				unset($_SESSION['sms_object_id']);
				unset($_SESSION['sms_amount']);
				unset($_SESSION['smsConfirmCode']);
			}
			else {
				echo 'Вы ввели неверный код подтвеждения';
			}
		}

		if ( $user->activated == 1 ) {
			echo "<script>$('#phoneForm').css('display', 'none');</script>";
		}
		$data = array(
				'user_id' => $user->user_id,
				'phone' => $_POST['phone']
			);
		$code = $user->code;
		
		if ( $this->getRequest()->getPost('PhoneSendCode') ) {
			$phonePrefix = $_POST['__phone_prefix'];
			$phone .= $phonePrefix;
			$phone .= $_POST['phone'];
			$user->phone = $phone;
			$user->save();
			
			$tUser->sendSms($phone, 'Код подтверждения номера: ' . $code);

			echo "<script>jQuery(document).ready(function() {
				jQuery('#phone-confirm-modal').modal('show');
				});
			</script>";
		}
		
		if ( $this->getRequest()->getPost('PhoneConfirm') ) {
			if ( $_POST['phone-confirm-code'] == $code ) {
				$user->activated = 1;
				$user->save();
				echo '<script>jQuery(document).ready(function() {
					swal({
						title: "Успешно!",
						text: "Телефон успешно подтвержден!",
						type: "success",
						confirmButtonColor: "#007AFF"
					});
				});
				</script>';
				echo "<script>setTimeout(function() { window.location = '/cp/account' }, 3000)</script>";
			}
			else {
				echo '<script>jQuery(document).ready(function() {
					swal({
						title: "Ошибка!",
						text: "Код подтверждения не верный!",
						type: "error",
						confirmButtonColor: "#007AFF"
					});
				});
				</script>';
				echo "<script>setTimeout(function() { window.location = '/cp/account' }, 3000)</script>";
			}
		}
		
		if ( $this->getRequest()->getPost('ProfileSettings') ) {
			$user->auth_type = $_POST['auth-type'];
			$user->confirmation_type = $_POST['confirmation-type'];
			$user->fg_type = $_POST['fg-type'];
			$user->in_notifications = $_POST['in-notifications'];
			$user->out_notifications = $_POST['out-notifications'];
			$user->save();
			echo '<script>jQuery(document).ready(function() {
				swal({
					title: "Успешно!",
					text: "Настройки успешно сохранены!",
					type: "success",
					confirmButtonColor: "#007AFF"
				});
			});
			</script>';
			echo "<script>setTimeout(function() { window.location = '/cp/account' }, 3000)</script>";
		}
		
		if ( $this->getRequest()->getPost('Verificate') )
		{
			if ( !empty($_POST['firstname']) AND !empty($_POST['lastname']) AND !empty($_POST['city']) AND !empty($_POST['birthdate']) AND !empty($_POST['docnum']) AND !empty($_POST['docwho']) AND !empty($_POST['docwhen']) )
			{
				mkdir("/var/www/www-root/data/www/sm-payment.com/uploads/". $user->user_id ."/", 0777);
				$uploaddir = '/var/www/www-root/data/www/sm-payment.com/uploads/'. $user->user_id .'/';
				$_FILES['userfile1']['name'] = $user->user_id .'-file1';
				$_FILES['userfile2']['name'] = $user->user_id .'-file2';
				move_uploaded_file($_FILES['userfile1']['tmp_name'], $uploaddir . $_FILES['userfile1']['name']);
				move_uploaded_file($_FILES['userfile2']['tmp_name'], $uploaddir . $_FILES['userfile2']['name']);
				
				$tVerification = new Verification();

				$tVerification->create(array( 
					'user_id' => $user->user_id,
					'firstname' => $_POST['firstname'],
					'lastname' => $_POST['lastname'],
					'middlename' => $_POST['middlename'],
					'country' => $_POST['country'],
					'city' => $_POST['city'],
					'birthdate' => $_POST['birthdate'],
					'docnum' => $_POST['docnum'],
					'docwho' => $_POST['docwho'],
					'docdate' => $_POST['docwhen'],
					'docimg1' => '/uploads/'. $user->user_id .'/'. $_FILES['userfile1']['name'],
					'docimg2' => '/uploads/'. $user->user_id .'/'. $_FILES['userfile2']['name']
				));
				
				echo '<script>jQuery(document).ready(function() {
					swal({
						title: "Успешно!",
						text: "Заявка на верификацию аккаунта успешно отправлена!",
						type: "success",
						confirmButtonColor: "#007AFF"
					});
				});
				</script>';
				echo "<script>setTimeout(function() { window.location = '/cp/account' }, 3000)</script>";
			}
		}

		if ( $this->getRequest()->getPost('CodeCardRequest') ) {
			$tCodecard = new Codecard();
			$cardData = $tCodecard->getCodeCardByUserId( $user->user_id );
			$this->view->cardData = $cardData;
			
			if ( $cardData ) {
				echo '<script>jQuery(document).ready(function() {
					swal({
						title: "Ошибка!",
						text: "Вы уже выпустили кодовую карту!",
						type: "error",
						confirmButtonColor: "#007AFF"
					});
				});
				</script>';
				
				echo "<script>setTimeout(function() { window.location = '/cp/account' }, 3000)</script>";
			}
			else {
				$tCodecard->create( $user->user_id );
				$user->code_card = 1;
				$user->save();
				
				echo '<script>jQuery(document).ready(function() {
					swal({
						title: "Успешно!",
						text: "Кодовая карта успешно создана!",
						type: "success",
						confirmButtonColor: "#007AFF"
					});
				});
				</script>';
				
				echo "<script>setTimeout(function() { window.location = '/cp/account' }, 3000)</script>";
			}
		}
		
		if ( !empty($_POST['SmsAmount']) AND is_numeric($_POST['SmsAmount']) )
		{
			$SmsAmount = (double) $_POST['SmsAmount'];

			$Balance = new Balance();
			$myBalance = $Balance->getCurrencyBalance($_SESSION['zh_user_id'], 10);

			$zh_userdata = $tUser->getUserDataById($_SESSION['zh_user_id']);

			$data = array(
				'user_id' => $_SESSION['zh_user_id'],
				'object_id' => 10,
				'amount' => $SmsAmount,
				'sms_balance' => $zh_userdata['sms_balance']
			);

			//$zh_data = $tUser->smsFill();

			echo '<pre>';
			print_r($myBalance);
			echo '</pre>';exit;
		}
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        Zend_Session::forgetMe();
		if ( isset($_SESSION['zh_aut_step']) ) unset($_SESSION['zh_aut_step']);
	  	return $this->_redirect( "" );
    }
    
    public function changepAction()
    {
		$tUser = new User();
    	$user = Zend_Auth::getInstance()->getIdentity();
		$user = $tUser->find( $user->user_id )->current();
		$this->view->account_type = $user->account_type;
		
		$form = new changepForm();
    	if ( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$values = $form->getValues();
			if ( $user->password == md5($values['oldpassword']) ) {
	        	$user->password = md5($values['newpassword']);
	        	$user->save();
				$this->view->message = $this->_helper->controllerMessage("VALID_OPERATION"); 
			} else {
				$this->view->message = $this->_helper->controllerMessage("NO_VALID_OLD_PASSWORD"); 
			} 
    	}
    	$this->view->form = $form;
    }
    
    public function transferAction()
    {
		$tUser = new User();
    	$user = Zend_Auth::getInstance()->getIdentity();
		$user = $tUser->find( $user->user_id )->current();
		$this->view->account_type = $user->account_type;
    	$transferForm = new transferForm( $user->admin_mode );
    	$this->view->fTransfer = $transferForm;
		$defaultNamespace = new Zend_Session_Namespace('Default');
		$this->view->confirmation_type = $user->confirmation_type;
		$activatedPhone = $user->activated;
		$this->view->activatedPhone = $activatedPhone;
		$smsBalance = $user->sms_balance;
		$this->view->smsBalance = $smsBalance;
		$this->view->user_id = $user->user_id;
    	// Confirm transfer
		if ( $this->getRequest()->getPost('Confirm') ) {
			$tObject = new Object();
			$values = $defaultNamespace->transfer;	
			$abbr = $values['abbr'];
			$purse = $values['abbr'] . '' . $values['purse'];
			$abbrObject = $tObject->findByPurse( $purse );
			$userID = $tUser->getIdByPurse( $purse );
			$userData = $tUser->getUserDataById( $userID );
			$tTransaction = new Transaction();
			
			if ( $_POST['confirmCode'] == $_SESSION['confirmCode'] ) {
				try {
					$tTransaction->transfer( array( 'mode' => $user->admin_mode,
				                                'user_id' => $user->user_id, 
				                                'to_user_id' => $userID, 
				                                'object_id' => $abbrObject->object_id, 
				                                'amount' => $values['amount'], 
				                                'description' => $values['note'], 
				                                'commission' => $values['commission'] ) );
					$this->view->message = $this->_helper->controllerMessage("VALID_OPERATION");; 
				} catch (Zend_Db_Adapter_Exception $e) {
					$this->view->message = $e->getMessage();
				}
				
				unset($_SESSION['confirmCode']);
				
				/* if ( $userData['in_notifications'] == 2 ) {
					$to = $userData['phone'];
					$text = 'Вы получили перевод от пользователя '. $user->user_id .' на кошелек: '. $values['purse'] ."\nСумма: ". $values['amount'] . ' ' . $abbr . "\nПримечание: ". $values['note'];
					
					if ( $smsCheck == 0 ) {					
						$to = $userData['e_mail'];
						$subject = 'Начисление средств';
						$text = 'Вы получили перевод от пользователя '. $user->user_id .' на кошелек: '. $values['purse'] ."\nСумма: ". $values['amount'] . ' ' . $abbr . "\nПримечание: ". $values['note'];
					
						$tUser->sendEmail($to, $subject, $text);
					
						$this->view->smsCheck = $smsCheck; 
					}
					else if ( $smsCheck == 1 ) {
						$tUser->sendSms($to, $text);
					
						$this->view->smsCheck = $smsCheck; 
					}
				}		
				else if ( $userData['in_notifications'] == 1 ) {
					$to = $userData['e_mail'];
					$subject = 'Начисление средств';
					$text = 'Вы получили перевод от пользователя '. $user->user_id .' на кошелек: '. $values['purse'] ."\nСумма: ". $values['amount'] . ' ' . $abbr . "\nПримечание: ". $values['note'];
					
					$tUser->sendEmail($to, $subject, $text);
				}
			
				if ( $user->out_notifications == 2 ) {
					$phoneNum = $user->phone;
					$email = $user->e_mail;
					$subject = 'Расход средств';
					$text = 'Вы совершили платеж на кошелек: '. $values['purse'] ."\nСумма: ". $values['amount'] . ' ' . $abbr . "\nКоммиссия: ". $values['commission'] . ' ' . $currency . "\nПримечание: ". $values['note'];
					
					$tUser->sendSms($phoneNum, $text);
					
					if ( $tUser->sendSms->error = true ) {
					
						$tUser->sendEmail($email, $subject, $text);
					}
				}
				else if ( $user->out_notifications == 1 ) {
					$to = $user->e_mail;
					$subject = 'Расход средств';
					$text = 'Вы совершили платеж на кошелек: '. $values['purse'] ."\nСумма: ". $values['amount'] . ' ' . $abbr . "\nКоммиссия: ". $values['commission'] . ' ' . $currency . "\nПримечание: ". $values['note'];
					
					$tUser->sendEmail($to, $subject, $text);
				} */
				
				/* return $this->render('validt'); */
				
				echo '<script>jQuery(document).ready(function() {
					swal({
						title: "Успешно!",
						text: "Вы успешно перевели средства!",
						type: "success",
						confirmButtonColor: "#007AFF"
					});
				});
				</script>';
				
				echo "<script>setTimeout(function() { window.location = '/cp/account' }, 3000)</script>";
			}

		}
    	// Send transfer
		if ( $this->getRequest()->getPost('Send') && $transferForm->isValid($this->getRequest()->getPost())) {
			$values = $transferForm->getValues();
            $values['summa'] = $values['amount'] - $values['commission']; 
      		$defaultNamespace->transfer = $values;
			$this->view->values = $values;
			$abbr = $values['abbr'];
			$_SESSION['confirmCode'] = uniqid();
			
			if ( $user->confirmation_type == 0 ) {
				$this->view->alertClass = 'info';
				$this->view->alertHeading = '<i class="ti-info"></i> Информация';
				$this->view->alertMessage = '<strong>На Ваш Email выслан SMS-код подтверждения! </strong>Введите его в поле ниже, для того, что бы подтвердить выполнение транзакции.';
				
				$to = $user->e_mail;
				$subject = 'Подтверждение транзакции';
				$text = 'Вы собираетесь перевести средства на кошелек: '. $values['purse'] ."\nСумма: ". $values['amount'] . ' ' . $abbr . "\nПримечание: ". $values['note'] . "\nКод подтверждения транзакции: ". $_SESSION['confirmCode'];
				
				$tUser->sendEmail($to, $subject, $text);
			}
			else if ( $user->confirmation_type == 1 ) {			

				if ( $activatedPhone == 0 ) {	
					$this->view->alertClass = 'danger';
					$this->view->alertHeading = '<i class="ti-close"></i> Ошибка';
					$this->view->alertMessage = '<strong>Вы не подтвердили мобильный телефон! </strong>Код подтверждения данной транзакции выслан на Ваш Email. Для получения возможности подтвеждения транзакций по SMS, подтвердите мобильный телефон в профиле.';
					
					$to = $user->e_mail;
					$subject = 'Подтверждение транзакции';
					$text = 'Вы собираетесь перевести средства на кошелек: '. $values['purse'] ."\nСумма: ". $values['amount'] . ' ' . $abbr . "\nПримечание: ". $values['note'] . "\nКод подтверждения транзакции: ". $_SESSION['confirmCode'];
					
					$tUser->sendEmail($to, $subject, $text);
				}
				else if ( $activatedPhone == 1 ) {
					if ( $smsBalance < 0.05 ) {
						$this->view->alertClass = 'warning';
						$this->view->alertHeading = '<i class="ti-alert"></i> Предупреждение';
						$this->view->alertMessage = '<strong>На Вашем SMS-балансе недостаточно средств! </strong>Код подтверждения данной транзакции выслан на Ваш Email. Для получения возможности подтвеждения транзакций по SMS, пополните Ваш SMS-счет.';
						
						$to = $user->e_mail;
						$subject = 'Подтверждение транзакции';
						$text = 'Вы собираетесь перевести средства на кошелек: '. $values['purse'] ."\nСумма: ". $values['amount'] . ' ' . $abbr . "\nПримечание: ". $values['note'] . "\nКод подтверждения транзакции: ". $_SESSION['confirmCode'];
					
						$tUser->sendEmail($to, $subject, $text);
					}
					else if ( $smsBalance >= 0.05 ) {
						$tSmsMessages = new SmsMessages();
						
						$this->view->alertClass = 'info';
						$this->view->alertHeading = '<i class="ti-info"></i> Информация';
						$this->view->alertMessage = '<strong>На Ваш номер телефона выслан SMS-код подтверждения! </strong>Введите его в поле ниже, для того, что бы подтвердить выполнение транзакции.';
						
						$msg_id = $tSmsMessages->saveSms( array( 
							'user_id' => $user->user_id,
							'phone' => $user->phone,
							'text' => 'Вы собираетесь перевести средства на кошелек: '. $values['purse'] ."\nСумма: ". $values['amount'] . ' ' . $abbr . "\nПримечание: ". $values['note'] . "\nКод подтверждения транзакции: ". $_SESSION['confirmCode']
						));
					
						$smsData = $tSmsMessages->getSmsByMsgId( $msg_id ); 
					
						$tSmsMessages->sendSms( array( 
							'msg_id' => $smsData['msg_id'],
							/* 'user_id' => $smsData['user_id'],*/
							'phone' => $smsData['phone'],
							'text' => 'Вы собираетесь перевести средства на кошелек: '. $values['purse'] ."\nСумма: ". $values['amount'] . ' ' . $abbr . "\nПримечание: ". $values['note'] . "\nКод подтверждения транзакции: ". $_SESSION['confirmCode']
						));
					}
				}
			}

			echo "<script>$(document).ready(function(){ $('#confirm-form').modal('show'); });</script>";	
		}
    	// Change transfer
		if ( $this->getRequest()->getPost('Change') ) {
			$values = $defaultNamespace->transfer;	
			$transferForm->setDefaults( $values );
		}
		if ( !$user->admin_mode ) {
    		$tCommissions = new Commissions();
			if ( $user->account_type == 0 ) {
				$viewCommission = 1.7;
			}
			else if ( $user->account_type == 1 ) {
				$viewCommission = 0.5;
			}
			else if ( $user->account_type == 2 ) {
				$viewCommission = 0.4;
			}
			else if ( $user->account_type == 3 ) {
				$viewCommission = 0.3;
			}
    		$this->view->Commissions = $viewCommission;	
    	}
		$this->view->balance = $user->getBalance();
    }

    public function historyAction()
    {
    	$user = Zend_Auth::getInstance()->getIdentity();
    	$tTransaction = new Transaction();
		$tObject = new Object();
		$tUser = new User();
		$this->view->account_type = $user->account_type;
		$select = $tTransaction->select();
		$rForm = new reportsForm();
		$defaultNamespace = new Zend_Session_Namespace('Default');		
		if ( isset($defaultNamespace->reportForm) && $rForm->isValid( $defaultNamespace->reportForm ) ) {
        	$values = $defaultNamespace->reportForm;
    	}
    	if ( $this->getRequest()->isPost() && $rForm->isValid($this->getRequest()->getPost())) {
			$values = $rForm->getValues();
        	$defaultNamespace->reportForm = $values;
    	}
    	// утсановка фильтров
    	if ( !$user->admin_mode ) $select->where("user_id = ? OR to_user_id = ?", $user->user_id, $user->user_id );
		if ( $values['id'] ) $select->where("transaction_id = ?", $values['id'] ); 
		if ( $values['date1'] ) $select->where("date > ?", $values['date1'] ); 
		if ( $values['date2'] ) $select->where("date < ?", $values['date2'] ); 
		if ( $values['purseFrom'] ) {
			$select->where("object_id = ?", $tObject->findByPurse( $values['purseFrom'], 0 )->object_id );
			$select->where("user_id= ?", $tUser->getIdByPurse( $values['purseFrom'] ) );  				
		}
		if ( $values['purseTo'] ) {
			$select->where("object_id = ?", $tObject->findByPurse( $values['purseTo'], 0 )->object_id );
			$select->where("to_user_id= ?", $tUser->getIdByPurse( $values['purseTo'] ) );  				
		} 
		if ( $values['amount'] ) { 
			$select->where("amount = ?", $values['amount'] );	
		} else {
			$select->where( "amount>0" );	
		} 
		if ( $values['type'] ) $select->where("type = ?", $values['type'] ); 
		if ( $values['note'] ) $select->where("description LIKE ?", "%".$values['note']."%" ); 
		if ( $this->_getParam('filed') && $this->_getParam('order_t') ) 
			switch ( $this->_getParam('filed') ) {
				case 'purseFrom':
					$select->order( array("object_id ".$this->_getParam('order_t'), "user_id ".$this->_getParam('order_t') ) );
					break;
				case 'purseTo':
					$select->order( array("object_id ".$this->_getParam('order_t') , "to_user_id ".$this->_getParam('order_t') ) );
					break;
				default:
					$select->order($this->_getParam('filed')." ". $this->_getParam('order_t') );
					break;
			}
		$select->order( array( "date DESC" ) );
		//
		$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$this->view->paginator = $paginator;
		$this->view->rForm = $rForm;
		$this->view->filed = $this->_getParam('filed');
		$this->view->order_t = $this->_getParam('order_t');
		$lang=Zend_Registry::get('Lang');
		if ( $this->_getParam('download') ) {
			$charset = ($lang=="ru") ? "utf-8" : "windows-1251"; 
			foreach ($paginator as $tr) {
				$str = array ();
				$str[] = $tr->transaction_id;
				$str[] = $tr->date;
				$str[] = $tr->purseNameFrom();
				$str[] = $tr->purseNameTo();
				$str[] = $tr->getAmount();
				$str[] = iconv("UTF-8", "$charset//IGNORE",$this->view->Translate($tr->getTable()->typeById($tr->type)));
				$str[] = iconv("UTF-8", "$charset//IGNORE", $tr->description);
				$csv[] = implode(";", $str);
			}
			$this->view->csv = implode("\r\n", $csv);
			$this->_helper->layout->disableLayout();
			$this->charset = $charset;
			return $this->render('download');
		}
    }

    public function fillAction()
    {
        $tUser = new User();
    	$user = Zend_Auth::getInstance()->getIdentity();
		$user = $tUser->find( $user->user_id )->current();
		
		$this->view->account_type = $user->account_type;
		
		$courseRur =  0.015326962425951612;
		$courseEur =  1.1147029316687103;
		$courseUsd = 0.93;
		
		if ( isset($_GET['advcash']) ) {
			if ( $_GET['advcash'] == 'new' ) {
				$tFill = new Fill();
				$fill_id = $tFill->create(array( 'user_id' => $user->user_id ));
				
				$this->view->fill_id = $fill_id;
				$_SESSION['fill_id'] = $fill_id;
				
				echo "<script>$(document).ready(function(){ $('#advcash-form').modal('show'); });</script>";
			}
		}
		
		if ( isset($_GET['ac_transaction_status']) ) {
			$tFill = new Fill();
			$fillData = $tFill->getFillData( $_SESSION['fill_id'] );
			
			if ( $_GET['ac_transaction_status'] == 'COMPLETED' ) {
				if ( $_GET['ac_merchant_currency'] == 'RUR' ) {
					$amount = ($_GET['ac_merchant_amount']*$courseRur + 0.05);
				}
				else if ( $_GET['ac_merchant_currency'] == 'EUR' ) {
					$amount = ($_GET['ac_merchant_amount']*$courseEur + 0.05);
				}
				else if ( $_GET['ac_merchant_currency'] == 'USD' ) {
					$amount = ($_GET['ac_merchant_amount']*$courseUsd + 0.05);
				}
				
				if ( isset($_SESSION['fill_id']) && $fillData['status'] == 0 ) {

					$tFill->pay( array( 'user_id' => $user->user_id, 'amount' => $amount ) );
					$tFill->updateFill( array( 'wallet' => 'AdvCash', 'currency' => $_GET['ac_merchant_currency'], 'amount' => $_GET['ac_merchant_amount'], 'note' => $_GET['ac_comments'], 'status' => 1), $_SESSION['fill_id'] );
					
					echo '<script>jQuery(document).ready(function() {
						swal({
							title: "Счет успешно пополнен!",
							text: "Вы получили ' . $amount . ' SPM",
							type: "success",
							confirmButtonColor: "#007AFF"
						});
					});
					</script>';
					
					echo "<script>setTimeout(function() { window.location = '/cp/account' }, 3000)</script>";
				}
				else if ( !isset($_SESSION['fill_id']) ) {
					echo '<script>jQuery(document).ready(function() {
						swal({
							title: "Ошибка!",
							text: "Платеж не удался",
							type: "error",
							confirmButtonColor: "#007AFF"
						});
					});
					</script>';
				
					echo "<script>setTimeout(function() { window.location = '/cp/account' }, 3000)</script>";
				}
			}
			else {
				echo '<script>jQuery(document).ready(function() {
					swal({
						title: "Ошибка!",
						text: "Платеж не удался",
						type: "error",
						confirmButtonColor: "#007AFF"
					});
				});
				</script>';
				
				echo "<script>setTimeout(function() { window.location = '/cp/account' }, 3000)</script>";
			}
			
			unset($_SESSION['fill_id']);
			session_unregister('fill_id');
		}
		
		if ( isset($_GET['perfectmoney']) ) {
			if ( $_GET['perfectmoney'] == 'new' ) {
				$tFill = new Fill();
				$fill_id = $tFill->create(array( 'user_id' => $user->user_id ));
				
				$this->view->fill_id = $fill_id;
				$_SESSION['fill_id'] = $fill_id;
				
				echo "<script>$(document).ready(function(){ $('#perfectmoney-form').modal('show'); });</script>";
			}
		}
		
		if ( isset($_GET['V2_HASH']) ) {
			$tFill = new Fill();
			$fillData = $tFill->getFillData( $_SESSION['fill_id'] );
			
			if ( $_GET['PAYMENT_UNITS'] == 'USD' ) {
				$amount = ($_GET['PAYMENT_AMOUNT']*$courseUsd + 0.05);
			}
			else if ( $_GET['PAYMENT_UNITS'] == 'EUR' ) {
				$amount = ($_GET['PAYMENT_AMOUNT']*$courseEur + 0.05);
			}
			
			if ( isset($_SESSION['fill_id']) && $fillData['status'] == 0 ) {

				$tFill->pay( array( 'user_id' => $user->user_id, 'amount' => $amount ) );
				$tFill->updateFill( array( 'wallet' => 'PerfectMoney', 'currency' => $_GET['PAYMENT_UNITS'], 'amount' => $_GET['PAYMENT_AMOUNT'], 'note' => $_GET['SUGGESTED_MEMO'], 'status' => 1), $_SESSION['fill_id'] );
					
				echo '<script>jQuery(document).ready(function() {
					swal({
						title: "Счет успешно пополнен!",
						text: "Вы получили ' . $amount . ' SPM",
						type: "success",
						confirmButtonColor: "#007AFF"
					});
				});
				</script>';
					
				echo "<script>setTimeout(function() { window.location = '/cp/account' }, 3000)</script>";
			}
			else if ( !isset($_SESSION['fill_id']) ) {
				echo '<script>jQuery(document).ready(function() {
					swal({
						title: "Ошибка!",
						text: "Платеж не удался",
						type: "error",
						confirmButtonColor: "#007AFF"
					});
				});
				</script>';
				
				echo "<script>setTimeout(function() { window.location = '/cp/account' }, 3000)</script>";
			}
		}
		
		if ( $this->getRequest()->getPost('PayeerPay') ) {
			$this->view->m_shop = $m_shop = '227813643';
			$this->view->m_orderid = $m_orderid = '1';
			$m_amount = $_POST['m_amount'];
			$m_curr = $_POST['m_curr'];
			$m_desc = $_POST['m_desc'];
			$m_key = 'sA6w4hzcxevGB72k';
			
			$arHash = array(
				$m_shop,
				$m_orderid,
				$m_amount,
				$m_curr,
				$m_desc,
				$m_key
			);
			$sign = strtoupper(hash('sha256', implode(':', $arHash)));
			
			echo "<script>alert(" . $sign . ");</script>";
		}
		
    }

    public function conclusionAction()
    {
    	$user = Zend_Auth::getInstance()->getIdentity();
		$tUser = new User();
		$user = $tUser->find( $user->user_id )->current();
		$this->view->account_type = $user->account_type;
		$tTransaction = new Transaction();
		// cancel withdrawal
		if ( $this->_getParam("act")=="cancel" ) {
			$tr_id = $this->_getParam("tr");
			$rowTr = $tTransaction->find( $tr_id )->current();
			if ( ( $rowTr && ($rowTr->user_id == $user->user_id) || $user->admin_mode) && !$rowTr->status) {
				$tTransaction->cancel( $tr_id );	
			} else {
				$this->view->message = $this->_helper->controllerMessage("NOT_ACCESS");
			}   	
		}
		// executed withdrawal
		if ( $this->_getParam("act")=="executed" ) {
			$tr_id = $this->_getParam("tr");
			$rowTr = $tTransaction->find( $tr_id )->current();
			if ( $user->admin_mode && $rowTr && !$rowTr->status) {
				$rowTr->status = $tTransaction->status['executed'];
				$rowTr->save();	
			} else {
				$this->view->message = $this->_helper->controllerMessage("NOT_ACCESS");
			}   	
		}
		// form
		$iForm = new issueForm( $user->getBalance( array ( 'аccessible' => TRUE ) ) );
		$iForm->removeElement( 'oper' );
		$this->view->iForm = $iForm;
		if ( $this->getRequest()->isPost() && $iForm->isValid($this->getRequest()->getPost())) {
			$values = $iForm->getValues();
			try {
				$tTransaction->withdrawal( array( user_id => $user->user_id, object_id => $values[purse], amount => $values[amount], description => $values[note], status => 0 ) );
				$this->view->message = $this->_helper->controllerMessage("VALID_OPERATION"); 
        	} catch (Zend_Db_Adapter_Exception $e) {
				$this->view->message = $e->getMessage();
        	}	
		}
		$select = $tTransaction->select();
		if ( !$user->admin_mode ) $select->where('user_id = ?', $user->user_id );
		$select->where('type = ? ', $tTransaction->type['withdrawal']);
		// sort
		$field = $this->_getParam('filed') ? $this->_getParam('filed') : 'status';
		$order = $this->_getParam('order_t') ? $this->_getParam('order_t') : 'ASC';
		switch ( $field ) {
			case 'purseFrom':
				$select->order( array("object_id ".$order, "user_id ".$order ) );
				break;
			case 'date':
				$select->order($field." ". $order );
				break;
			case 'status':
				$select->order( array ( "ROUND( status/2 )". $order, "date DESC" ) );
				break;
			default:
				$select->order( array ( $field." ". $order, "date DESC" ) );
				break;
		}
		$select->order("date ASC");
		// paginator
		$this->view->balance = $user->getBalance();
		$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$this->view->paginator = $paginator;
		$this->view->filed = $field;
		$this->view->order_t = $order;
		$this->view->mode = $user->admin_mode;
    }
    
    public function conversionAction()
    {
    	$user = Zend_Auth::getInstance()->getIdentity();
		$tUser = new User();
		$user = $tUser->find( $user->user_id )->current();
		$this->view->account_type = $user->account_type;
		$this->view->balance = $user->getBalance();
		$convForm = new conversionForm( $user->getBalance( array ( 'аccessible' => TRUE ) ) );
		$this->view->convForm = $convForm;
    	$tCommissions = new Commissions();
    	if ( $this->getRequest()->isPost() && $convForm->isValid($this->getRequest()->getPost())) {
			$values = $convForm->getValues();
			$values['user_id'] = $values['to_user_id'] = $user->user_id;
			unset($values['summa']);  
			$tTransaction = new Transaction();
			try {
				$tTransaction->conversion( $values, $user->admin_mode );
				$this->view->message = $this->_helper->controllerMessage("VALID_OPERATION"); 
				echo '<script>jQuery(document).ready(function() {
					swal({
						title: "Успешно!",
						text: "Конвертация успешно выполнена",
						type: "success",
						confirmButtonColor: "#007AFF"
					});
				});
				</script>';
					
				echo "<script>setTimeout(function() { window.location = '/cp/account/conversion' }, 3000)</script>";
			} catch (Zend_Db_Adapter_Exception $e) {
				$this->view->message = $e->getMessage();
        	}	
		}
    	$tObject = new Object();
    	$this->view->Objects = $tObject->fetchAll(); 
    	$this->view->balance = $user->getBalance();
    	$tCommissions = new Commissions();
		if ( $user->account_type == 0 ) {
			$viewCommission = 1.7;
		}
		else if ( $user->account_type == 1 ) {
			$viewCommission = 0.5;
		}
		else if ( $user->account_type == 2 ) {
			$viewCommission = 0.4;
		}
		else if ( $user->account_type == 3 ) {
			$viewCommission = 0.3;
		}
    	$this->view->Commissions = $viewCommission;	
		$baseCur = $tObject->getBase();
		$nonBaseCur = $tObject->getNonBase();
		$this->view->baseCur = $baseCur;
		$this->view->nonBaseCur = $nonBaseCur;
		$allCur = $tObject->fetchAllLive();
		$this->view->allCur = $allCur;
    }
	
	public function pursesAction()
    {
    	$user = Zend_Auth::getInstance()->getIdentity();
		$tUser = new User();
		$user = $tUser->find( $user->user_id )->current();
		$this->view->account_type = $user->account_type;
		$this->view->balance = $user->getBalance();
		$purForm = new pursesForm( $user->getBalance( array ( 'аccessible' => TRUE ) ) );
		$this->view->purForm = $purForm;
    	$tCommissions = new Commissions();
    	
    	$tObject = new Object();
    	$this->view->Objects = $tObject->fetchAll(); 
    	$this->view->balance = $user->getBalance();
    }
	
	public function supportAction()
    {
        $tUser = new User();
    	$user = Zend_Auth::getInstance()->getIdentity();
		$user = $tUser->find( $user->user_id )->current();
		$this->view->account_type = $user->account_type;

		$Ticket = new Ticket();

		$this->view->allTickets = $Ticket->getTicketsByUserId($user->user_id);

/* echo "<pre>";
print_r($this->view->allTickets);
echo "</pre>"; */

		$this->view->ticket_err = false;

		if ( !count($this->view->allTickets) ) {
			$this->view->ticket_err = true;
		}

		$ticketForm = new ticketForm();
		$this->view->ticketForm = $ticketForm;
		$defaultNamespace = new Zend_Session_Namespace('Default');

		if ( $this->getRequest()->getPost('Send') ) {
			$ticket = new Ticket();
			$values = $ticketForm->getValues();
			$data = array(
				'user_id' => $user->user_id,
				'subject' => $_POST['subject'],
				'message' => $_POST['message']
			);
			$ticket->create($data);
		}
    }
	
	public function invoicesAction()
	{
		$tUser = new User();
    	$user = Zend_Auth::getInstance()->getIdentity();
		$user = $tUser->find( $user->user_id )->current();	
		$this->view->account_type = $user->account_type;
		$tInvoice = new Invoice();
		
		if ( !$user->admin_mode ) {
			$tCommissions = new Commissions();
			$this->view->Commissions = $tCommissions->getTransferCommissions();	
		}
		
		if ( $this->getRequest()->getPost('InvoiceCreate') ) {
			$tObject = new Object();
			$purse = $_POST['abbr'] . '' . $_POST['to_user_id'];
			$abbrObject = $tObject->findByPurse( $purse );
			$userID = $tUser->getIdByPurse( $purse );
			$abbr = $_POST['abbr'];
			
			$tInvoice->invoicing( array( 'mode' => $user->admin_mode,
				                        'user_id' => $user->user_id, 
				                        'to_user_id' => $_POST['to_user_id'], 
				                        'object_id' => $abbrObject->object_id, 
										'object_abbr' => $abbr, 
				                        'amount' => $_POST['amount'], 
				                        'description' => $_POST['note']  ) );
			
			echo '<script>jQuery(document).ready(function() {
					swal({
						title: "Успешно!",
						text: "Счет пользователю ' . $_POST['to_user_id'] . ' на сумму ' . $_POST['amount'] . '' . $abbr . ' успешно выставлен.",
						type: "success",
						confirmButtonColor: "#007AFF"
					});
				});
				</script>';
				
			echo "<script>setTimeout(function() { window.location = '/cp/account/invoices' }, 3000)</script>";
		}
		
		$inbox = $tInvoice->existsByToUserId( $user->user_id );
		$this->view->inbox = $inbox;
		
		$outbox = $tInvoice->existsByUserId( $user->user_id );
		$this->view->outbox = $outbox;
		
		if ( $this->getRequest()->getPost('InvoicePay') ) {
			$tObject = new Object();
			$purse = $_POST['object_abbr'] . '' . $_POST['from_user_id'];
			$abbrObject = $tObject->findByPurse( $purse );
			$payData = $tInvoice->dataByInvoiceId( $_POST['invoice_id'] );
			$_SESSION['invoiceConfirmCode'] = substr(uniqid(), 0, 8);
			
			/* if ( $user->confirmation_type == 0 ) {	
				$to = $user->e_mail;
				$subject = 'Подтверждение транзакции';
				$text = 'Вы собираетесь оплатить счет №: '. $_POST['invoice_id'] ." пользователю: ". $payData['user_id'] . "\nСумма с комиссией: ". $payData['amount_with_comm'] ."\nКомментарий: ". $payData['description'] . "\nКод подтверждения транзакции: ". $_SESSION['invoiceConfirmCode'];
				
				$tUser->sendEmail($to, $subject, $text);
				
				echo "<script>$(document).ready(function(){ $('#invoice-pay-confirm-modal').modal('show'); });</script>";
			}
			else if ( $user->confirmation_type == 1 ) {			

				if ( $activatedPhone == 0 ) {				
					$to = $user->e_mail;
					$subject = 'Подтверждение транзакции';
					$text = 'Вы собираетесь оплатить счет №: '. $_POST['invoice_id'] ." пользователю: ". $payData['user_id'] . "\nСумма с комиссией: ". $payData['amount_with_comm'] ."\nКомментарий: ". $payData['description'] . "\nКод подтверждения транзакции: ". $_SESSION['invoiceConfirmCode'];
					
					$tUser->sendEmail($to, $subject, $text);
					
					echo "<script>$(document).ready(function(){ $('#invoice-pay-confirm-modal').modal('show'); });</script>";
				}
				else if ( $activatedPhone == 1 ) {
					if ( $smsBalance < 0.05 ) {
						$to = $user->e_mail;
						$subject = 'Подтверждение транзакции';
						$text = 'Вы собираетесь оплатить счет №: '. $_POST['invoice_id'] ." пользователю: ". $payData['user_id'] . "\nСумма с комиссией: ". $payData['amount_with_comm'] ."\nКомментарий: ". $payData['description'] . "\nКод подтверждения транзакции: ". $_SESSION['invoiceConfirmCode'];
					
						$tUser->sendEmail($to, $subject, $text);
						
						echo "<script>$(document).ready(function(){ $('#invoice-pay-confirm-modal').modal('show'); });</script>";
					}
					else if ( $smsBalance >= 0.05 ) {
						$tSmsMessages = new SmsMessages();

						$msg_id = $tSmsMessages->saveSms( array( 
							'user_id' => $user->user_id,
							'phone' => $user->phone,
							'text' => $text = 'Вы собираетесь оплатить счет №: '. $_POST['invoice_id'] ." пользователю: ". $payData['user_id'] . "\nСумма с комиссией: ". $payData['amount_with_comm'] ."\nКомментарий: ". $payData['description'] . "\nКод подтверждения транзакции: ". $_SESSION['invoiceConfirmCode']
						));
					
						$smsData = $tSmsMessages->getSmsByMsgId( $msg_id ); 
					
						$tSmsMessages->sendSms( array( 
							'msg_id' => $smsData['msg_id'],
							'phone' => $smsData['phone'],
							'text' => 'Вы собираетесь оплатить счет №: '. $_POST['invoice_id'] ." пользователю: ". $payData['user_id'] . "\nСумма с комиссией: ". $payData['amount_with_comm'] ."\nКомментарий: ". $payData['description'] . "\nКод подтверждения транзакции: ". $_SESSION['invoiceConfirmCode']
						));
						
						echo "<script>$(document).ready(function(){ $('#invoice-pay-confirm-modal').modal('show'); });</script>";
					}
				}
			} */
			
			$tInvoice->invoicePay( array( 'user_id' => $payData['user_id'],
										  'to_user_id' =>  $payData['to_user_id'],
										  'object_id' => $payData['object_id'],
										  'object_abbr' => $payData['object_abbr'], 
										  'amount' => $payData['amount'],
										  'amount_with_comm' => $payData['amount_with_comm']) );
										  
			$tInvoice->updateInvoice( array( 'status' => 1 ), $payData['invoice_id'] );

			echo '<script>jQuery(document).ready(function() {
						swal({
							title: "Успешно!",
							text: "Счет пользователю ' . $payData['user_id'] . ' на сумму ' . $payData['amount'] . '' . $payData['object_abbr'] . ' успешно оплачен.",
							type: "success",
							confirmButtonColor: "#007AFF"
						});
					});
			</script>';
				
			echo "<script>setTimeout(function() { window.location = '/cp/account/invoices' }, 3000)</script>";
		}
		
		/* if ( $this->getRequest()->getPost('InvoicePayConfirm') ) {
			if ( $_POST['InvoicePayConfirmCode'] == $_SESSION['invoiceConfirmCode'] ) {
				$tInvoice->invoicePay( array( 'user_id' => $payData['user_id'],
										  'to_user_id' =>  $payData['to_user_id'],
										  'object_id' => $payData['object_id'],
										  'object_abbr' => $payData['object_abbr'], 
										  'amount' => $payData['amount'],
										  'amount_with_comm' => $payData['amount_with_comm']) );
										  
				$tInvoice->updateInvoice( array( 'status' => 1 ), $payData['invoice_id'] );

				echo '<script>jQuery(document).ready(function() {
						swal({
							title: "Успешно!",
							text: "Счет пользователю ' . $payData['user_id'] . ' на сумму ' . $payData['amount'] . '' . $payData['object_abbr'] . ' успешно оплачен.",
							type: "success",
							confirmButtonColor: "#007AFF"
						});
					});
				</script>';
				
				echo "<script>setTimeout(function() { window.location = '/cp/account/invoices' }, 3000)</script>";
				
				unset($_SESSION['invoiceConfirmCode']);
			}
			else {
				echo '<script>jQuery(document).ready(function() {
						swal({
							title: "Ошибка!",
							text: "Код подтвеждения не верный",
							type: "error",
							confirmButtonColor: "#007AFF"
						});
					});
				</script>';
				
				echo "<script>setTimeout(function() { window.location = '/cp/account/invoices' }, 3000)</script>";
				
				echo "<script>$(document).ready(function(){ $('#invoice-pay-confirm-modal').modal('show'); });</script>";
			}
		} */
		
	}
	    
}

