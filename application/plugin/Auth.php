<?php

class Plugin_Auth extends Zend_Controller_Plugin_Abstract {

    private $_auth;
    private $_acl;
    
 	protected $_noAuth = array(
 		'module'     => 'default',
 		'controller' => 'index',
 		'action'     => 'index');
 	protected $_noAcl  = array(
 		'module'     => 'default',
 		'controller' => 'error',
 		'action'     => 'error');
 
    
 	public function preDispatch( Zend_Controller_Request_Abstract $request)
 	{
 		$this->_auth = Zend_Auth::getInstance();
 	 	if( $this->_auth->hasIdentity() ) {
   			$role = $this->_auth->getIdentity()->admin_mode ? 'admin' : 'member';
 	 	} else
   			$role = 'guest';

   		$this->_acl = new Zend_Acl();
        // Roles 
        $this->_acl->addRole(new Zend_Acl_Role('guest'));
        $this->_acl->addRole(new Zend_Acl_Role('member'));
        $this->_acl->addRole(new Zend_Acl_Role('admin'));
        // Resources 		
		$this->_acl->add(new Zend_Acl_Resource('index'));
		$this->_acl->add(new Zend_Acl_Resource('account'));
		$this->_acl->add(new Zend_Acl_Resource('apanel'));
		$this->_acl->add(new Zend_Acl_Resource('install'));

		$this->_acl->allow('guest');
   		$this->_acl->deny('guest','account');
   		$this->_acl->deny('guest','apanel');
   		$this->_acl->allow('member');
   		$this->_acl->deny('member','index');
   		$this->_acl->deny('member','apanel');
   		$this->_acl->allow('admin');
   		$this->_acl->deny('admin','index');
   		$this->_acl->allow('admin','index','reg');
   		
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($this->_acl);
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($role);
        
   		$controller  = $request->controller;
		$action      = $request->action;
		$module     = $request->module;
		$resource   = $request->controller;
 
   		if( $this->_acl->has($resource) )
   			$resource = null;
 
 		if( !$this->_acl->isAllowed($role, $controller, $action) )
			list($module, $controller, $action)  =( !$this->_auth->hasIdentity() ) ?  array_values($this->_noAuth) : array_values($this->_noAcl);
 
		$request->setModuleName($module);
		$request->setControllerName($controller);
		$request->setActionName($action);
 	}
		
}
