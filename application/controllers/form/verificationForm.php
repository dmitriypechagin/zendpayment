<?

class VerificationForm extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$user_id = $this->createElement('hidden', 'userid', array ('class'=>"form-control", 'value' => $_SESSION['zh_user_id'] ) )
                       ->setRequired(true);
		$firstname = $this->createElement('text', 'firstname', array ( 'label' => 'Имя', 'class'=>"form-control", 'placeholder' => "Peter") )
                       ->setRequired(true);
		$lastname = $this->createElement('text', 'lastname', array ( 'label' => 'Фамилия', 'class'=>"form-control", 'placeholder' => "Obama") )
                       ->setRequired(true);
		$middlename = $this->createElement('text', 'middlename', array ( 'label' => 'Отчество', 'class'=>"form-control", 'placeholder' => "Barak") )
                       ->setRequired(true);
		$docnum = $this->createElement('text', 'docnum', array ( 'label' => 'Серия и номер документа', 'class'=>"form-control", 'placeholder' => "12 34 567 890") )
                       ->setRequired(true);
		$docwho = $this->createElement('text', 'docwho', array ( 'label' => 'Кем выдан документ', 'class'=>"form-control", 'placeholder' => "Президентом") )
                       ->setRequired(true);
		$docwhen = $this->createElement('text', 'docwhen', array ( 'label' => 'Когда выдан документ', 'class'=>"form-control", 'placeholder' => "01.01.1990") )
                       ->setRequired(true);
		$country = $this->createElement('text', 'country', array ( 'label' => 'Страна', 'class'=>"form-control", 'placeholder' => "United States of America") )
                       ->setRequired(true);
		$city = $this->createElement('text', 'city', array ( 'label' => 'Город', 'class'=>"form-control", 'placeholder' => "New York") )
                       ->setRequired(true);
		$birthdate = $this->createElement('text', 'birthdate', array ( 'label' => 'Дата рождения', 'class'=>"form-control", 'placeholder' => "01.01.1990") )
                       ->setRequired(true);
		
					   
		$this->addElement($user_id)
             ->addElement($firstname)
             ->addElement($lastname)
			 ->addElement($middlename)
			 ->addElement($docnum)
			 ->addElement($docwho)
			 ->addElement($docwhen)
			 ->addElement($country)
			 ->addElement($city)
			 ->addElement($birthdate)
             ->setElementDecorators( decorators::$tableTrElement )
             ->addElement('submit', 'Send', array('label' => 'send', 'class'=>"btn btn-primary pull-right", 'decorators' => decorators::$tableTrButton) );
	}
}