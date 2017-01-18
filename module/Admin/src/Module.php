<?php
/**
 * Module class
 */

namespace Admin;


use Admin\Controller\DashboardController;
use Admin\Controller\IndexController;
use Admin\Controller\ProfileController;
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
     * Module bootstrap listener.
     *
     * Called after the MVC bootstrapping is completed.
     *
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        // Get shared event manager
        $sharedEventManager = $event->getApplication()->getEventManager()->getSharedManager();

        // Register listener,
        // attach identifier is __NAMESPACE__, only response admin module event handler.
        // if want response all module event handler, use AbstractActionController::class instead __NAMESPACE__
        //$sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_ROUTE, [$this, 'onRouteListener'], 100);
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatchListener'], 100);
    }


    /**
     * Force use https protocol
     *
     * @param MvcEvent $event
     * @return ResponseInterface
     */
    public function onRouteListener(MvcEvent $event)
    {
        if (php_sapi_name() == "cli") {
            return;
        }

        //**
        $uri = $event->getRequest()->getUri();
        $scheme = $uri->getScheme();
        if('https' != $scheme) {
            $uri->setScheme('https');
            $response=$event->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $uri);
            $response->setStatusCode(301);
            $response->sendHeaders();
            return $response;
        }
        //*/
    }


    /**
     * Custom dispatch listener
     * Use custom layout for this module
     *
     * @param MvcEvent $event
     */
    public function onDispatchListener(MvcEvent $event)
    {
        // Application running env flag
        $serviceManager = $event->getApplication()->getServiceManager();
        $appConfig = $serviceManager->get('ApplicationConfig');
        $appEnv = isset($appConfig['application']['env']) ? $appConfig['application']['env'] : 'development';
        $viewModel = $event->getViewModel();
        $viewModel->setVariable('appEnv', $appEnv);


        // Get controller name which was dispatched.
        $controller = $event->getRouteMatch()->getParam('controller', null);
        if($controller == IndexController::class) { // Allow all access
            return ;
        }

        // Login status validate
        $authService = $serviceManager->get(AuthService::class);
        if (!$authService->hasIdentity()) {
            return $event->getTarget()->redirect()->toRoute('admin/index', ['action' => 'login', 'suffix' => '.html']);
        }

        // Set module default template
        $viewModel->setTemplate('layout/admin_layout');

        $whiteListControllers = [
            ProfileController::class,
            DashboardController::class,
        ];
        if (in_array($controller, $whiteListControllers)) {
            return ;
        }

        // ACL filter
        $action = $event->getRouteMatch()->getParam('action', null);
        $action = str_replace('-', '', lcfirst(ucwords($action, '-'))); // Convert action name to camel-case form dash-style

        $aclManager = $serviceManager->get(AclManager::class);
        if (!$aclManager->isValid($authService->getIdentity(), $controller, $action)) {
            die('forbid access!');
        }

    }

}
