<?php
/**
 * Module class
 */

namespace Admin;


use Admin\Controller\DashboardController;
use Admin\Controller\IndexController;
use Admin\Controller\MessageController;
use Admin\Controller\ProfileController;
use Admin\Controller\SearchController;
use Admin\Exception\RuntimeException;
use Admin\Service\AclManager;
use Admin\Service\AuthService;
use Zend\Mvc\MvcEvent;


class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $sharedEventManager = $event->getApplication()->getEventManager()->getSharedManager();
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatchListener'], 100);
    }

    /**
     * @param MvcEvent $event
     * @throws RuntimeException
     */
    public function onDispatchListener(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();

        $appConfig = $serviceManager->get('ApplicationConfig');
        $appEnv = isset($appConfig['application']['env']) ? $appConfig['application']['env'] : 'development';

        $viewModel = $event->getViewModel();
        $viewModel->setVariable('appEnv', $appEnv);

        $controller = $event->getRouteMatch()->getParam('controller', null);
        if($controller == IndexController::class) { // Allow all access
            return ;
        }

        // Login status validate
        $authService = $serviceManager->get(AuthService::class);
        if (!$authService->hasIdentity()) {
            $viewModel->setTemplate('layout/admin_simple');
            throw new RuntimeException('使用本模块需要您先登录系统.');
        }

        // Set module default template
        $viewModel->setTemplate('layout/admin_layout');

        $whiteList = [
            ProfileController::class => ['*'],
            DashboardController::class => ['*'],
            SearchController::class => ['*'],
            MessageController::class => ['in', 'out', 'read', 'delete', 'unread', 'send'],
        ];

        $action = $event->getRouteMatch()->getParam('action', null);
        // Convert action name to camel-case form dash-style
        //$action = str_replace('-', '', lcfirst(ucwords($action, '-')));

        if (array_key_exists($controller, $whiteList) &&
            (in_array('*', $whiteList[$controller]) || in_array($action, $whiteList[$controller]) )) {
            return ;
        }

        $aclManager = $serviceManager->get(AclManager::class);
        if (!$aclManager->isValid($controller, $action)) {
            //$viewModel->setTemplate('layout/admin_simple');
            throw new RuntimeException('我们找遍了整个宇宙也没发现谁给了你权利使用这个功能哦!');
        }
    }

}
