<?

class TicketForm extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$ticketFrom = $this->createElement('hidden', 'userid', array ('class'=>"form-control", 'value' => $_SESSION['zh_user_id'] ) )
                       ->setRequired(true);
		$ticketSubject = $this->createElement('text', 'subject', array ( 'label' => 'Тема', 'class'=>"form-control", 'placeholder' => "Subject") )
                       ->setRequired(true);
		$ticketMessage = $this->createElement('textarea', 'message', array ( 'label' => 'Текст обращения', 'class'=>"form-control" , 'placeholder' => "Message", 'rows' => '5') )
                       ->setRequired(true);
					   
		$this->addElement($ticketFrom)
             ->addElement($ticketSubject)
             ->addElement($ticketMessage)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Send', array('label' => 'send', 'class'=>"btn mw-md btn-primary", 'decorators' => decorators::$tableTrButton) );
	}
}