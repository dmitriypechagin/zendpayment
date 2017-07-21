<?php 
/**
 * The assistant to action for messages of controlers
 * 
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class controllers_helper_ControllerMessage extends Zend_Controller_Action_Helper_Abstract
      
{
	
	public static $VALID_OPERATION = "Operation is conducted successfully"; 
	public static $NOT_ACCESS = "There is no access to operation";
	public static $NO_VALID_OLD_PASSWORD = "The old password is incorrectly";
	public static $INVALID_AUTH = "Invalid user name or the password";

	// install message 
	public static $CONNECT_DATABASE = "Connection with a database is installed";
	public static $NOT_CONNECT_DATABASE = "It is impossible to be connected to a database";
	public static $NO_CONNECT_DATABASE = "It is impossible to be connected to a database";
	public static $NO_CONNECT_ADAPTER = "It is impossible to connect the adapter";
	public static $TABLES_CREATED = "Tables are created";
	public static $ADMIN_CREATED = "The login account of the manager is created";
	public static $INSTALL_COMPLET = "Installation is completed";
	public static $ENTER_SYSTEM = "To enter into system";
	public static $NO_CONFIG_WRITER = "Error of record of a file of a configuration. Open access on write in the directory /application/configs";
	/**
     * @var Zend_Loader_PluginLoader
     */
    public $pluginLoader;

    public function __construct()
    {
        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }

    public function direct( $name )
    {
		$m = self::$$name;
        return $m;
    }
}
