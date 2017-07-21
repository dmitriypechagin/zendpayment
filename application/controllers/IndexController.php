<?php 

class IndexController extends Zend_Controller_Action
{
	public function indexAction() 
	{
/* 		$form = new AuthForm();
		$this->view->form = $form;
		if ( $this->getRequest()->isPost() && $form->isValid($_POST) ) {
			$values = $form->getValues();
			$this->view->values = $values;
			$tUser = new User();
			if ( $tUser->login($values['email'],$values['password']) ) {
				$red = trim(str_replace( $this->getRequest()->getBaseUrl() , "", $this->getRequest()->getServer('REQUEST_URI') ));
				return $this->_redirect( $red!="/" ? $red : "/account" );
			} else {
						$this->view->message = $this->_helper->controllerMessage("INVALID_AUTH");
			}
		} */

		//unset($_SESSION['zh_aut_step']);
		
		$locale = new Zend_Locale(Zend_Locale::BROWSER);
		$lang = $locale->getLanguage();

		if ( isset($_SESSION['zh_aut_step']) AND $_SESSION['zh_aut_step'] == 2 )
		{
			if ( isset($_POST['password']) )
			{
				$err = true;

				if ( empty($_POST['password']) ) {
					$err = false;
					$this->view->err_password = 'Введите пароль';
				}			
				else if ( empty($_POST['auth_code']) ) {
					$err = false;
					$this->view->err_code = 'Введите код подтверждения';
				}


				if ( $err )
				{
					$users = new User();

					$data = $users->getUserDataById($_SESSION['zh_user_id']);
					
					if ( $_POST['auth_code'] != $_SESSION['auth_code'] ) {
						$this->view->err_code = 'Неверно введен код потверждения';
					}
					
					if ( md5($_POST['password']) != $data['password'] ) {
						$this->view->err_password = 'Неверно введен пароль';
					}
					else
					{
						$users->zh_login($data);

						header("Location: /cp/account");
					}
				}
			}
		}
		elseif ( isset($_POST['id'], $_POST['PIN']) )
		{
			$err = true;

			if ( empty($_POST['id']) ) {
				$err = false;
				$this->view->err_id = 'Введите ID';

			} elseif ( !preg_match('/^[0-9]{9,}$/', $_POST['id']) ) {
				$err = false;
				$this->view->err_id = 'Недопустимый ID';
			}



			if ( $err )
			{
				$users = new User();

				$data = $users->getUserDataById($_POST['id']);

				if ( !$data ) {
					$this->view->err_id = 'Пользователя с указанным ID не существует';
				}
				else
				{
					if ( empty($_POST['PIN']) ) {
						$err = false;
						$this->view->err_PIN = 'Введите PIN';

					} elseif ( $_POST['PIN'] != $data->PIN ) {
						$err = false;
						$this->view->err_PIN = 'Неверно введен ПИН-код';
					}
					else
					{
						$_SESSION['zh_aut_step'] = 2;
						$_SESSION['zh_sayHi'] = $data->sayHi;
						$_SESSION['zh_user_id'] = $data->user_id;
						$_SESSION['auth_code'] = substr(uniqid(), 0, 8);
						$_SESSION['auth_type'] = $data->auth_type;
						$_SESSION['phone'] = $data->phone;
						
						if ( $_SESSION['auth_type'] == 0 ) {
							$to = $data->e_mail;
							$subject = 'Подтверждение входа';
							$text = 'Код потверждения входа: '. $_SESSION['auth_code'] ."\n";
							
							$users->sendEmail($to, $subject, $text);
						}
						else if ( $_SESSION['auth_type'] == 1 ) {
							$to = $_SESSION['phone'];
							$text = 'Код подтверждения входа: '. $_SESSION['auth_code'];
							
							$users->sendSms($to, $text);
						}
					}
				}
			}
		}
	}

	public function regAction()
	{
		unset($_SESSION['zh_aut_step']);
		//unset($_SESSION['zh_step']);

		if ( isset($_SESSION['zh_step']) AND $_SESSION['zh_step'] == 3 )
		{
			if ( isset($_POST['sayHi'], $_POST['PIN'], $_POST['password'], $_POST['repeatPassword']) )
			{
				$err = true;

				if ( empty($_POST['sayHi']) ) {
					$err = false;
					$this->view->err_sayHi = 'Введите приветствие';
				}

				if ( empty($_POST['PIN']) ) {
					$err = false;
					$this->view->err_PIN = 'Введите ПИН';
				}

				if ( empty($_POST['password']) OR empty($_POST['repeatPassword']) ) {
					$err = false;
					$this->view->err_password = 'Введите пароли';

				} elseif ( $_POST['password'] != $_POST['repeatPassword'] ) {
					$err = false;
					$this->view->err_password = 'Пароли не совпадают';
				}


				if ( $err )
				{
					$_SESSION['zh_step'] = 4;

					$PIN = abs(intval($_POST['PIN']));

					$users = new User();

					$data = array(
						'password' => md5($_POST['password']),
						'sayHi' => $_POST['sayHi'],
						'PIN' => $PIN,
						'code' => substr(uniqid(), 0, 8)
					);

					$where = 'user_id = '. $_SESSION['zh_last_id'];

					$users->updateUserData($data, $where);


					$to = $_SESSION['zh_email'];
					$subject = 'Успешная регистрация';
					$text = 'Здравствуйте!

Поздравляем с успешной регистрацией в платёжной системе Simple Money!

Ваши регистрационные данные в нашей системе:

ID: '. $_SESSION['zh_last_id'] .'
PIN: '. $PIN .'

Используйте ваш логин и пароль для входа в систему:
https://sm-payment.com/cp/
Мы рекомендуем запомнить ID, Пароль и PIN а так-же хранить эти данные в безопасном месте.

Что дальше?
Ваш аккаунт уже полностью готов к работе, осталось только пополнить его любым удобным способом и начать пользоваться системой. При помощи Simple Money вы можете:
- Отправлять деньги по всему миру, даже тем, кто ещё не зарегистрирован в Simple Money
- Переводить деньги внутри системы
- Принимать платежи Simple Money на своём сайте
- Зарабатывать вместе с Simple Money, привлекая новых пользователей в систему

Если у вас остались какие-либо вопросы по работе системы, наша служба поддержки всегда рада помочь!

Удачной работы!';

					$users->sendEmail($to, $subject, $text);
				}
			}
		}
		elseif ( isset($_SESSION['zh_step']) AND $_SESSION['zh_step'] == 2 )
		{
			if ( isset($_POST['code']) )
			{
				$err = true;

				if ( empty($_POST['code']) ) {
					$err = false;
					$this->view->err_code = 'Введите код подтверждения';

				} elseif ( $_POST['code'] != $_SESSION['zh_regCode'] ) {
					$err = false;
					$this->view->err_code = 'Неправильный код подтверждения';
				}


				if ( $err )
				{
					$_SESSION['zh_step'] = 3;
				}
			}
		}
		elseif ( isset($_POST['email'], $_POST['g-recaptcha-response']) )
		{
			/* $err = true;

			if ( empty($_POST['g-recaptcha-response']) ) {
				$err = false;
				$this->view->err_recaptcha = 'Пройдите капчу';
			} */

			$myCurl = curl_init();
			curl_setopt_array($myCurl, array(
				CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => http_build_query(array("response" => $_POST['g-recaptcha-response'], "secret" => "6LdK7CgTAAAAAN6AQXVmMqFfiFNjJnD0v2lN2ACP"))
			));

			$response = curl_exec($myCurl);
			curl_close($myCurl);

			$response = json_decode($response, true);

			if ( !($response AND isset($response['success']) AND $response['success']) )
			{
				$err = false;
				$this->view->err_recaptcha = 'Неверно указана капча';
			}
			else
			{
				$users = new User();
				
				if ( $users->getUserByEmail($_POST['email']) ) {
					$this->view->err_email = 'Пользователь с таким E-mail уже зарегистрирован';
				}
				else
				{
					$_SESSION['zh_regCode'] = substr(uniqid(), 0, 8);//Генерация кода потверждения
					$_SESSION['zh_step'] = 2;


					$data = array(
						'e_mail' => $_POST['email']
					);

					$last_id = $users->saveUser($data);

					$_SESSION['zh_last_id'] = $last_id;
					$_SESSION['zh_email'] = $_POST['email'];

					$to = $_POST['email'];
					$subject = 'Активация аккаунта';

					$text = 'Код потверждения: '. $_SESSION['zh_regCode'];

					$users->sendEmail($to, $subject, $text);
				}
			}
			


			/* if ( empty($_POST['email']) ) {
				$err = false;
				$this->view->err_email = 'Введите E-mail';

			} elseif ( !preg_match('/^[a-z0-9_-]{1}[a-z0-9._-]{0,63}@[a-z0-9]{1}[a-z0-9.-]{0,63}\.[a-z0-9]{2,24}$/', $_POST['email']) ) {
				$err = false;
				$this->view->err_email = 'Недопустимый E-mail';
			}

			if ( !(isset($_POST['keep_me_logged_in']) AND $_POST['keep_me_logged_in'] == 1) ) {
				$err = false;
				$this->view->err_keep_me_logged_in = 'Примите условия';
			}

			if ( $err )
			{
				$users = new User();

				if ( $users->getUserByEmail($_POST['email']) ) {
					$this->view->err_email = 'Пользователь с таким E-mail уже зарегистрирован';
				}
				else
				{
					$_SESSION['zh_regCode'] = substr(uniqid(), 0, 8);//Генерация кода потверждения
					$_SESSION['zh_step'] = 2;


					$data = array(
						'e_mail' => $_POST['email']
					);

					$last_id = $users->saveUser($data);

					$_SESSION['zh_last_id'] = $last_id;


					$config = array(
						'auth' => 'plain',
						'username' => 'info@v-salikov.ru',
						'password' => 'HsCp4YpE',
					);

					$transport = new Zend_Mail_Transport_Smtp('p379348.ispmgr.ihc.ru', $config);

					$mail = new Zend_Mail();
					$mail->setBodyText('Код потверждения: '. $_SESSION['zh_regCode'] ."\nID: ". $last_id ."\n\nДанные нигде не сохраняются, пожалуйста, запишите их в надёжное место.");
					$mail->setFrom('info@v-salikov.ru', 'Simple Money');
					$mail->addTo($_POST['email']);
					$mail->setSubject('Активация аккаунта');
					$mail->send($transport);
				}
			} */
		}
	}
	
	public function forgotAction()
	{
		if ( isset($_SESSION['zh_fg_step']) AND $_SESSION['zh_fg_step'] == 3 )
		{
			if ( isset($_POST['password'], $_POST['repeatPassword']) ) {
				$err = true;

				if ( empty($_POST['password']) OR empty($_POST['repeatPassword']) ) {
					$err = false;
					$this->view->err_password = 'Введите пароль';

				} elseif ( $_POST['password'] != $_POST['repeatPassword'] ) {
					$err = false;
					$this->view->err_password = 'Пароли не совпадают';
				}
				
				if ( $err )
				{
					$_SESSION['zh_fg_step'] = 4;

					$users = new User();

					$data = array(
						'password' => md5($_POST['password'])
					);

					$where = 'user_id = '. $_SESSION['zh_user_id'];

					$users->updateUserData($data, $where);
				}
			}
		}
		else if ( isset($_SESSION['zh_fg_step']) AND $_SESSION['zh_fg_step'] == 2 )
		{
			if ( isset($_POST['fg_code']) ) {
				$err = true;
		
				if ( empty($_POST['fg_code']) ) {
					$err = false;
					$this->view->err_code = 'Введите код подтверждения';
				}
				
				if ( $err )
				{
					$users = new User();

					$data = $users->getUserDataById($_SESSION['zh_user_id']);
					
					if ( $_POST['fg_code'] != $_SESSION['fg_code'] ) {
						$this->view->err_code = 'Неверно введен код потверждения';
					}
					
					else
					{
						$_SESSION['zh_fg_step'] = 3;
					}
				}
			}
		}
		else if ( isset($_POST['id'] ) )
		{
			$err = true;

			if ( empty($_POST['id']) ) {
				$err = false;
				$this->view->err_id = 'Введите ID';

			} elseif ( !preg_match('/^[0-9]{9,}$/', $_POST['id']) ) {
				$err = false;
				$this->view->err_id = 'Недопустимый ID';
			}
			
			if ( $err )
			{
				$users = new User();

				$data = $users->getUserDataById($_POST['id']);
				
				if ( !$data ) {
					$this->view->err_id = 'Пользователя с указанным ID не существует';
				}
				else
				{
					$_SESSION['zh_fg_step'] = 2;
					$_SESSION['zh_user_id'] = $data->user_id;
					$_SESSION['fg_code'] = substr(uniqid(), 0, 8);
					$_SESSION['fg_type'] = $data->fg_type;
					$_SESSION['PIN'] = $data->PIN;
					
					if ( $_SESSION['fg_type'] == 0 ) {
						$to = $data->e_mail;
						$subject = 'Восстановление пароля';
						$text = 'Код восстановления пароля: '. $_SESSION['fg_code'];
						
						$users->sendEmail($to, $subject, $text);
					}
					else if ( $_SESSION['fg_type'] == 1 ) {
						$to = $data->phone;
						$text = 'Код восстановления пароля: '. $_SESSION['fg_code'];
						
						$users->sendSms($to, $text);
					}
				}
			}
		}
	}
}