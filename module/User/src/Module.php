<?php
/**
 * Module.php
 *
 * User Module class
 *
 */

namespace User;


use User\Controller\AuthController;
use User\Service\AuthManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;


class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }


    /**
     * Module bootstrap listener.
     * Called after the MVC bootstrapping is completed.
     *
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        // Get shared event manager
        $sharedEventManager = $event->getApplication()->getEventManager()->getSharedManager();

        // Register listener
        $sharedEventManager->attach(AbstractActionController::class, MvcEvent::EVENT_DISPATCH, [$this, 'dispatchListenerForAccess'], 100);

    }

    /**
     * Module custom listener for the module event register
     *
     * @param MvcEvent $event
     */
    public function dispatchListenerForAccess(MvcEvent $event)
    {
        // Get controller and action name which was dispatched.
        $controller = $event->getRouteMatch()->getParam('controller', null);
        if (0 === strpos($controller, 'Admin')) { // Admin module use special ACL system. skip it.
            return ;
        }

        $action = $event->getRouteMatch()->getParam('action', null);

        // Convert action name to camel-case form dash-style
        $action = str_replace('-', '', lcfirst(ucwords($action, '-')));
        if(in_array($action, ['login', 'logout']) && $controller == AuthController::class) { // Allow all access
            return ;
        }

        // Get the instance of AuthManager service.
        $authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);

        if(!$authManager->access($controller, $action)) {
            // Make sure not any debug information output.
            // Redirect() use PHP response header Location: url
            return $event->getTarget()->redirect()->toRoute('user/auth', ['action' => 'login', 'suffix' => '.html']);
        }
    }

}