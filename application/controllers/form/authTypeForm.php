<?

class AuthTypeForm extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$type_select = $this->createElement('select', 'auth_type', array ( 'label' => '', 'class' => 'form-control', 'multiOptions' => array('0' => 'Email', '1' => 'SMS') ) )
                       ->setRequired(true);
					   
		$this->addElement($type_select)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Change', array('label' => 'change', 'class'=>"btn mw-md btn-primary m-lg", 'decorators' => decorators::$tableTrButton) );
	}
}