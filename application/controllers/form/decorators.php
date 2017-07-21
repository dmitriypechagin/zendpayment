<?php 

class decorators  {
	
	// Elements button
	public static $tableTdButton = array(
            'ViewHelper',
            array(array('data' => 'HtmlTag'),  array('tag' => 'td' ))
    );
          
	public static $tableTrButton = array(
            'ViewHelper',
            array(array('data' => 'HtmlTag'),  array('tag' => 'td' )),
            array(array('emptyLabel' => 'HtmlTag'),  array('tag' => 'td', 'options'=>array('placement'=>'PREPEND') )),
            array(array('tr' => 'HtmlTag'),  array('tag' => 'tr' ))
	);
	
	public static $transferInput = array(
            'ViewHelper',
            array(array('data' => 'HtmlTag'),  array('tag' => 'div' )),
            array(array('emptyLabel' => 'HtmlTag'),  array('tag' => 'div', 'options'=>array('placement'=>'PREPEND') )),
            array(array('div' => 'HtmlTag'),  array('tag' => 'div' ))
	);
	
	// Elements text 
    public static $tableTdElement = array(
						    'ViewHelper',
		    				'Errors',
					   		array('HtmlTag', array('tag' => 'td')),
		    				array('Label', array('tag' => 'td')) 
	);

	public static $tableTrElement = array(
						    'ViewHelper',
		    				'Errors',
					   		array(array('data'=>'HtmlTag'), array('tag' => 'div', 'class' => '')),
		    				array('Label', array('tag' => 'div')) ,
					   		array(array('div'=>'HtmlTag'), array('tag' => 'div', 'class' => 'form-group')),
	);
	
	// Form 
	public static $formWrapper = array(
	        'FormElements',
	        array(array('tr' => 'htmlTag'), array('tag' => 'tr')),
	        array(array('table' => 'htmlTag'), array('tag' => 'table', 'class'=>'form_table', 'cellspacing'=>"0", 'cellpadding'=>"0", 'border'=>"0" )),
	        'Form',
	        array(array('div' =>'htmlTag'), array('tag' => 'div', 'class' => 'formwrapper')),
	);
	
	public static $formWrapperSmall = array(
	        'FormElements',
	        array(array('tr' => 'htmlTag'), array('tag' => 'tr')),
	        array(array('table' => 'htmlTag'), array('tag' => 'table', 'class'=>'form_table', 'cellspacing'=>"0", 'cellpadding'=>"0", 'border'=>"0" )),
	        'Form',
	        array(array('div' =>'htmlTag'), array('tag' => 'div', 'class' => 'formwrapper_small')),
	);

}