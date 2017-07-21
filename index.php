<?php

// echo        error_reporting( );

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));


// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH ),
    realpath(APPLICATION_PATH . '/controllers/form/'),
    realpath(APPLICATION_PATH . '/../library/'),
    realpath(APPLICATION_PATH . '/../application/models/'),
    get_include_path(),
)));

//require_once 'Zend/Loader.php';
//Zend_Loader::registerAutoload();
require_once('Zend/Loader/Autoloader.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);
//$autoloader->registerNamespace('application_');
//Zend_Loader_Autoloader::getInstance();


// setup database
Zend_Registry::set('fileConfig', APPLICATION_PATH . '/configs/config.ini' );
try {
	$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini', 'general');
	$db = Zend_Db::factory($config->db->adapter, $config->db->config->toArray());
	try {
	    $db->getConnection();
		Zend_Registry::set('db', $db);
		Zend_Db_Table::setDefaultAdapter($db);
		// settings
		try {
			$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini', 'tables');
			Zend_Registry::set('tablePrefix', $config->prefix."_" );
			$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini', 'settings');
			Zend_Registry::set('purse', $config);
		} catch (Zend_Exception $e) {
//			die("No config file found ");
		}
	} catch (Zend_Db_Adapter_Exception $e) {  
	    die("Невозможно подключиться к базе");
	} catch (Zend_Exception $e) {
	    // возможно, попытка загрузки требуемого класса адаптера потерпела неудачу
	    die("Невозможно подключить адаптер");
	}
} catch (Zend_Exception $e) {
//	die("No config file found ");
}

// Actions helper
Zend_Controller_Action_HelperBroker::addPrefix('controllers_helper');

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();

/*

*/

