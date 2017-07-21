<?

class ConfirmSmsForm extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$confirmCode = $this->createElement('text', 'confirmcode', array ( 'label' => 'Code', 'class'=>"input3 val", 'placeholder' => "Code") )
                       ->setRequired(true);
					   
		$this->addElement($confirmCode)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Confirm', array('label' => 'confirm', 'class'=>"btn mw-md btn-success m-b-lg", 'decorators' => decorators::$tableTrButton) );
	}
}