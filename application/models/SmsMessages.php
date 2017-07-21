<?php

/**
 * Model Sms
 *  
 * @author Nazar Vasheniak
 * @version 1.0
 */
	
class SmsMessages extends Zend_Db_Table_Abstract
{
	protected function _setupTableName()
    {
    	$this->_name = Zend_Registry::get('tablePrefix').'sms';
    	parent::_setupTableName();
    }    
	
	protected function _setupPrimaryKey()
    {
        $this->_primary = 'msg_id';
    	parent::_setupPrimaryKey();
    }  
	
	public function getSmsAll()
	{
		$this->_name = Zend_Registry::get('tablePrefix').'sms';
		
		return $this->fetchAll( $this->select() );
	}
	
	public function getSmsByUserId( $user_id )
	{	
		return $this->fetchAll( $this->select()->where(' user_id = '. $user_id) );
	}
	
	public function getSmsByMsgId( $msg_id )
	{	
		return $this->fetchRow( $this->select()->where(' msg_id = '. $msg_id) );
	}
	
	public function paySms( $user_id )
	{
		$tUser = new User();
    	$user = Zend_Auth::getInstance()->getIdentity();
		$user = $tUser->find( $user->user_id )->current();
		
		$curBalance = $user->sms_balance;
		$user->sms_balance = ($curBalance - 0.05);
		$user->save();
		
		/* $this->_name = Zend_Registry::get('tablePrefix').'user';
		
		$row = $this->fetchRow( $this->select()->where(' user_id = '. $user_id) );
		
		$this->updateUserData( array( 'sms_balance' => ($row['sms_balance'] - 0.05) ), 'user_id = '. $user_id ); */
	}
	
	public function saveSms( $data )
	{
		$this->_name = Zend_Registry::get('tablePrefix').'sms';
		
		$this->insert($data);
		
		return $this->getAdapter()->lastInsertId();
	}
	
	public function sendSms( $data )
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
				<text>'. $data['text'] .'</text>
			</message>
			<numbers>
				<number messageID="msg'. $data['msg_id'] .'">' . $data['phone'] . '</number>
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
		
		$this->paySms( $data['user_id'] );
	}
}