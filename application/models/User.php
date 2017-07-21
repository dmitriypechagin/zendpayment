<?php

/**
 * Model User
 *  
 * @author Lepaysys
 * @version 1.0
 */
	
class User extends Zend_Db_Table_Abstract
{

	/**
 	 * @see Zend_Db_Table_Abstract
     * @var array
 	 */
	protected $_dependentTables = array('Balance');

	/**
 	 * @see Zend_Db_Table_Abstract
     * @var string
     */
	protected $_rowClass = 'UserRow';
    
    /**
 	 * @see Zend_Db_Table_Abstract
     * @return void
     */
	protected function _setupTableName()
    {
    	$this->_name = Zend_Registry::get('tablePrefix').'user';
    	parent::_setupTableName();
    }    
    
    /**
 	 * @see Zend_Db_Table_Abstract
     * @return void
     */
    protected function _setupPrimaryKey()
    {
        $this->_primary = 'user_id';
    	parent::_setupPrimaryKey();
    }   

    /**
     * Determine whether a given user ID exists
     *
     * @param  string $id User
     * @return boolean
     */
    public function exists( $id ) {
    	return count( $this->find($id) ) > 0;
    }
    
    /**
     * Authenticate user. If valid, UserRow::takeStorageCommission() is called for commission collection for storage  
     *
     * @param  string $email
     * @param  string $password
     * @return boolean
     */
    public function login($email, $password) {
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable();
        $authAdapter->setTableName($this->_name)
                    ->setIdentityColumn('e_mail')
                    ->setCredentialColumn('password')
                    ->setCredentialTreatment('MD5(?)')
                    ->setIdentity($email)
                    ->setCredential($password);
        $result = $auth->authenticate($authAdapter);
        if($result->isValid()) {
            $row = $authAdapter->getResultRowObject();
            $auth->getStorage()->write( $row );
            $session = new Zend_Session_Namespace('Zend_Auth');
            $session->setExpirationSeconds(24*3600);
            Zend_Session::rememberMe(24*3600*30);
            $user = $this->find( $row->user_id )->current();
            $user->takeStorageCommission();
            return true;
        }
        return false;
    }



	public function zh_login($data)
	{
		$auth = Zend_Auth::getInstance();

		$auth->getStorage()->write( $data );
		$session = new Zend_Session_Namespace('Zend_Auth');
		$session->setExpirationSeconds(24*3600);
		Zend_Session::rememberMe(24*3600*30);
		$user = $this->find( $data->user_id )->current();
		$user->takeStorageCommission();

		return true;
	}


    /**
     * Return admin row  
     *
     * @return UserRow
     */
    public function getAdmin() {
    	return $this->fetchRow( $this->select()->where(' admin_mode = 1 ') );
    }
    
    /**
     * Return no admin users  
     *
     * @return UserRowset
     */
    public function getNoAdmin() {
    	return $this->fetchAll( $this->select()->where(' admin_mode = 0 ') );
    }
    
    /**
     * Return user id for purse name  
     *
     * @param  string $purse
     * @return int
     */
    public function getIdByPurse( $purse ) {
    	return (int)substr( $purse, Zend_Registry::get('purse')->abbrLength );
    }
	
	public function sendActivationEmail()
	{
		$config = array(
			'auth' => 'plain',
            'username' => 'info@v-salikov.ru',
            'password' => 'HsCp4YpE',
		);

		$transport = new Zend_Mail_Transport_Smtp('p379348.ispmgr.ihc.ru', $config);

		$mail = new Zend_Mail();
		$mail->setBodyText('This is the text of the mail.');
		$mail->setFrom('info@v-salikov.ru', 'Simple Money');
		//echo $this->_row->email;echo 55;
		$mail->addTo($this->_row->email);
		$mail->setSubject('Активация аккаунта');
		$mail->send($transport);
	}


	public function saveUser($data)
	{
		$this->_name = Zend_Registry::get('tablePrefix').'user';

		$this->insert($data);

		return $this->getAdapter()->lastInsertId();
	}


	public function updateUserData($data, $where)
	{
		$this->_name = Zend_Registry::get('tablePrefix').'user';

		$row = $this->update($data, $where);

		if( !$row ) {
			return false;
		}

		return true;
	}


	public function getUserByEmail($email)
	{
		$this->_name = Zend_Registry::get('tablePrefix').'user';

		$row = $this->fetchRow('e_mail = "'. $email .'"');

		if( !$row ) {
			return false;
		}

		return true;
	}


	public function getUserDataById($id)
	{
		$this->_name = Zend_Registry::get('tablePrefix').'user';

		$row = $this->fetchRow('user_id = '. $id);

		if( !$row ) {
			return false;
		}

		return $row;
	}
	
	public function sendEmail($to, $subject, $text)
	{
		/* $config = array(
			'auth' => 'plain',
			'username' => 'info@v-salikov.ru',
			'password' => 'HsCp4YpE',
		); */
		
		$config = array(
			'auth' => 'plain',
			'username' => 'info@sm-payment.com',
			'password' => '82356123neron',
			'ssl' => 'tls',
			'port' => 25
		);
		

		/* $transport = new Zend_Mail_Transport_Smtp('p379348.ispmgr.ihc.ru', $config); */
		$transport = new Zend_Mail_Transport_Smtp('smtp.yandex.ru', $config);

		$mail = new Zend_Mail();
		$mail->setBodyText($text);
		$mail->setFrom('info@sm-payment.com', 'Simple Money');
		$mail->addTo($to);
		$mail->setSubject($subject);
		$mail->send($transport); 
	}
	
	public function sendSms($to, $text)
	{
		$src = '<?xml version="1.0" encoding="UTF-8"?>
		<SMS>
			<operations>
				<operation>SEND</operation>
			</operations>
			<authentification>
				<username>idealive36@gmail.com</username>
				<password>444970890</password>
			</authentification>
			<message>
				<sender>SimpleMoney</sender>
				<text>'. $text .'</text>
			</message>
			<numbers>
				<number messageID="msg11">' . $to . '</number>
			</numbers>
		</SMS>';
 
		$Curl = curl_init();
		$CurlOptions = array(
			CURLOPT_URL=>'http://api.atompark.com/members/sms/xml.php',
			CURLOPT_FOLLOWLOCATION=>false,
			CURLOPT_POST=>true,
			CURLOPT_HEADER=>false,
			CURLOPT_RETURNTRANSFER=>true,
			CURLOPT_CONNECTTIMEOUT=>15,
			CURLOPT_TIMEOUT=>100,
			CURLOPT_POSTFIELDS=>array('XML'=>$src),
		);
		curl_setopt_array($Curl, $CurlOptions);
		if(false === ($Result = curl_exec($Curl))) {
			throw new Exception('Http request failed');
		}
 
		curl_close($Curl);

		echo $Result;
	}
	
	public function smsFill( $data )
	{
		$tBalance = new Balance();
		$userBalance = $tBalance->findBalanceByUserObject( $data['user_id'], $data['object_id'] );

		//$adminId = $this->getAdmin()->user_id;
		//$adminBalance = $tBalance->findBalanceByUserObject( $adminId, $data['object_id'] );

		if ($userBalance) $userBalance->change( -$data['amount'] );
		//if ($adminBalance) $adminBalance->change( $data['amount'] );

		$this->updateUserData( array(
			'sms_balance' => ($data['sms_balance'] + $data['amount'])
		),
		'user_id = '. $data['user_id'] );
	}
}

