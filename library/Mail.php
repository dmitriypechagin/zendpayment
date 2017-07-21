<?php

class Simple_Mail extends Zend_Mail
{
	public function __construct($charset = 'utf-8')
	{
		parent::__construct($charset);
		$this->setFrom('info@v-salikov.ru', 'Simple Money');
	}
	
	public function setBodyView($script, $params = array())
	{
		$layout = new Zend_Layout(array(
			'layoutPath' => APPLICATION_PATH . '/views/scripts'	
		));
		$layout->setLayout('email');
		
		$view = new Zend_View();
		$view->setScriptPath(APPLICATION_PATH . '/views/email');
		
		foreach ($params as $key => $value) {
			$view->assign($key, $value);
		}
		
		$layout->content = $view->render($script . '.phtml');
		$html = $layout->render();
		$this->setBodyHtml($html);
		
		return $this;
	}
}

?>