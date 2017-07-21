<?

class PhoneForm extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$userId = $this->createElement('hidden', 'userid', array ('class'=>"form-control", 'value' => $_SESSION['zh_user_id'] ) )
                       ->setRequired(true);
		$phoneNum = $this->createElement('text', 'phone', array ( 'label' => 'Phone', 'class'=>"input3 val", 'placeholder' => "Phone number") )
                       ->setRequired(true);
		$confirmCode = $this->createElement('text', 'confirmcode', array ( 'label' => 'Code', 'class'=>"form-control", 'style' => "display: none;", 'placeholder' => "Code") )
                       ->setRequired(true);
		$submitCode = $this->createElement('button', 'Confirm', array('label' => 'Confirm', 'class'=>"btn mw-md btn-primary", 'style' => "display: none;",  'decorators' => decorators::$tableTrButton) );
					   
		$this->addElement($userId)
             ->addElement($phoneNum)
			 ->addElement($confirmCode)
			 ->addElement($submitCode)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Send', array('label' => 'Отправить код', 'class'=>"btn mw-md btn-success m-b-lg", 'decorators' => decorators::$tableTrButton) );
	}
}