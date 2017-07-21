<?php

class Plugin_LangSelector extends Zend_Controller_Plugin_Abstract 
{
	
	const DEFAULT_LANGUAGE = 'ru'; 
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$lang = $request->getParam('lang',$request->getCookie('lang',self::DEFAULT_LANGUAGE)); 
        if ( !file_exists(APPLICATION_PATH . '/configs/lang/' . $lang . '.csv') ) $lang = self::DEFAULT_LANGUAGE;
        setcookie ("lang", $lang, 0, '/');
        $translate = new Zend_Translate('csv', APPLICATION_PATH . '/configs/lang/' . $lang . '.csv', $lang);

// Fixed Bug of PHP
if ( !$translate->getAdapter()->translate("Submit") ) {
      $f = APPLICATION_PATH . '/configs/lang/' . $lang . '.csv';
      try {
	$cfg=file_get_contents($f);
        $str=explode("\n",$cfg);
        foreach( $str as $item ) {
          if (substr($item, 0, 2) === '?#') { continue; }
          list($key,$value)=explode(";",$item);
          $arr[$key]=$value;
        }
        $translate = new Zend_Translate('array', $arr , $lang);
     } catch (Exception $e) { echo $e->getMessage(); }
}

        Zend_Registry::set('Zend_Translate',$translate);
        Zend_Registry::set('Lang',$lang);
        Zend_Locale::setDefault($lang);
        // Validators translate
        $translate = new Zend_Translate(
            'array',
            APPLICATION_PATH .'/resources/languages/', $lang,
            array('scan' => Zend_Translate::LOCALE_DIRECTORY)
        );
        Zend_Validate_Abstract::setDefaultTranslator($translate);
	}
	
}
