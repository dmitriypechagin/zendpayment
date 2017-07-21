<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initNavigation()
    {
        $this->bootstrap('View');
        $view = $this->getResource('view');

        $pagesTop = array(
            array(
                'controller' => 'index',
                'label'         => 'Head Page',// 'Главная страница',
                'resource'      => 'index',
                'privilege'     => 'login'
            ),
			array (
                'controller' => 'index',
                'action'        => 'forgot',
                'resource'      => 'index',
                'privilege'     => 'forgot',
                'label'         => 'Forgot',
            ),
            array (
                'controller' => 'index',
                'action'        => 'reg',
                'resource'      => 'index',
                'privilege'     => 'login',
                'label'         => 'Registration'
            )
/*            ,
            array (
                'controller' => 'index',
                'action'        => 'login',
                'resource'      => 'index',
                'privilege'     => 'login',
                'label'         => 'Login'
            )
*/        );

        $pages = array(
            array(
                'controller' => 'index',
                'label'         => 'Head Page',// 'Главная страница',
                'resource'      => 'index',
            ),

/*            array(
                'controller' => 'account',
                'action'        => 'index',
                // Ресурс для проверки прав доступа
                'resource'      => 'account',
                // И привилегия
                'privilege'     => 'index',
                'label'         => 'Users',
                'pages' => array (
                    array (
                        'controller' => 'apanel',
                        'action'        => 'new',
                        'resource'      => 'apanel',
                        'privilege'     => 'new',
                        'label'         => 'Добавить пользователя',
                    ),
                )
            ),
*/
            array (
                'controller' => 'apanel',
                'action'        => 'settings',
                'resource'      => 'apanel',
                'privilege'     => 'settings',
                'label'         => 'System Settings, fa-cogs',
            ),
            array (
                'controller' => 'apanel',
                'action'        => 'currencies',
                'resource'      => 'apanel',
                'privilege'     => 'currencies',
                'label'         => 'Currencies, fa-usd',
            ),
            array (
                'controller' => 'apanel',
                'action'        => 'exchange',
                'resource'      => 'apanel',
                'privilege'     => 'exchange',
                'label'         => 'Exchange rate and commission, fa-percent',
            ),
            array (
                'controller' => 'apanel',
                'action'        => 'issue',
                'resource'      => 'apanel',
                'privilege'     => 'issue',
                'label'         => 'Issue and repayment, fa-reply',
            ),
/*            array (
                'controller' => 'apanel',
                'action'        => 'reports',
                'resource'      => 'apanel',
                'privilege'     => 'reports',
                'label'         => 'Reports',
            ),
*/            array (
                'controller' => 'apanel',
                'action'        => 'viewing',
                'resource'      => 'apanel',
                'privilege'     => 'viewing',
                'label'         => 'Viewing of purses, fa-eye',
            ),
            array (
                'controller' => 'apanel',
                'action'        => 'blocking',
                'resource'      => 'apanel',
                'privilege'     => 'blocking',
                'label'         => 'Blocking of purses, fa-ban',
            ),
            array (
                'controller' => 'apanel',
                'action'        => 'estimation',
                'resource'      => 'apanel',
                'privilege'     => 'estimation',
                'label'         => 'Estimation of means, fa-university',
            ),
			array (
                'controller' => 'apanel',
                'action'        => 'verification',
                'resource'      => 'apanel',
                'privilege'     => 'verification',
                'label'         => 'Verification requests, fa-check',
            ),
			array (
                'controller' => 'account',
                'action'        => 'invoices',
                'resource'      => 'account',
                'privilege'     => 'invoices',
                'label'         => 'Invoices, fa-list-alt',
            ),
            array (
                'controller' => 'account',
                'action'        => 'conversion',
                'resource'      => 'account',
                'privilege'     => 'conversion',
                'label'         => 'Conversion, fa-exchange',
            ),
            array (
                'controller' => 'account',
                'action'        => 'transfer',
                'resource'      => 'account',
                'privilege'     => 'transfer',
                'label'         => 'Transfer, fa-check',
            ),
            array (
                'controller' => 'account',
                'action'        => 'history',
                'resource'      => 'account',
                'privilege'     => 'history',
                'label'         => 'History, fa-history',
            ),
			array (
                'controller' => 'account',
                'action'        => 'support',
                'resource'      => 'account',
                'privilege'     => 'support',
                'label'         => 'Support, fa-question',
            ),

            array (
                'controller' => 'index',
                'action'        => 'reg',
                'resource'      => 'index',
                'privilege'     => 'reg',
                'label'         => 'Registration, fa-user-plus',
            ),
            array (
                'controller' => 'account',
                'action'        => 'changep',
                'resource'      => 'account',
                'privilege'     => 'changep',
                'label'         => 'Сhange password, fa-unlock',
            ),
            array (
                'controller' => 'account',
                'action'        => 'logout',
                'resource'      => 'account',
                'privilege'     => 'logout',
                'label'         => 'Logout, fa-sign-out',
            )
        );

        $container = new Zend_Navigation($pages);
        $containerTop = new Zend_Navigation($pagesTop);
        $view->navigation($container);
        $view->topMenu = $containerTop;
//        $view->navigation($containerTop);

        return $container;

    }

}
