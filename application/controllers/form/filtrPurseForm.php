<?php

class filtrPurseForm extends Zend_Form {
    
	public function init() {
        $this->setMethod('post');
        $validPurseName = new Zend_Validate_Callback(array ( callback => array ('BalanceRow', 'isValidPurseName' ) ) );
		$validPurseName->setMessage( BalanceRow::NO_VALID_PURSE , Zend_Validate_Callback::INVALID_VALUE);
        $id = $this->createElement('text', 'id', array ( 'label' => 'user ID' ) )
                       ->addValidator('int')
                       ->addValidator('GreaterThan', false, array ( 'min' => 0 ) );
        $username = $this->createElement('text', 'email', array ( 'label' => 'Login name' ));
        $username->addValidator('emailAddress',true, array('domain' => TRUE, 'allow' => Zend_Validate_Hostname::ALLOW_DNS  ))
                 ->addFilter('StringToLower');
        $purse = $this->createElement('text', 'purse', array ( 'label' => 'purse' ) )
                       ->addValidator( $validPurseName );
        $amount1 = $this->createElement('text', 'amount1', array ( 'label' => 'amount' ) )
                       ->addValidator('float');
        $amount2 = $this->createElement('text', 'amount2', array ( 'label' => '-', 'decorators' => decorators::$tableTdElement ) )
                       ->addValidator('float');
		$order   = $this->createElement('select', 'field', array ( 'label' => 'Сортировка', 'multiOptions' => array( 'user_id'=>'id пользователя', 'amount'=>'Сумма')) )
                       ->setValue('user_id');
        $order_t = $this->createElement('select', 'order_t', array ( 'multiOptions' => array( 'ASC'=>'По возрастанию', 'DESC'=>'По убыванию')) )
                       ->setValue('ASC');
                       
		$this->addElement($id)
             ->addElement($username)
             ->addElement($purse)
             ->addElement($amount1)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement($amount2)
//             ->addElement($order)
//             ->addElement($order_t)
             ->addElement('submit', 'Seach', array('label' => 'Seach', 'class'=>"submit_small", 'decorators' => decorators::$tableTdButton) );
	}
	
	public function loadDefaultDecorators() {
		$this->setDecorators( decorators::$formWrapperSmall );
	}
}