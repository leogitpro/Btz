<?php
/**
 * Module.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application;

use Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager;


class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }


    /**
     *
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {

        // Init Default Session
        $event->getApplication()->getServiceManager()->get(SessionManager::class);

        // Get shared event manager
        $sharedEventManager = $event->getApplication()->getEventManager()->getSharedManager();

        // Current module dispatch listener
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatchListener'], 100);
    }


    /**
     * @param MvcEvent $event
     */
    public function onDispatchListener(MvcEvent $event)
    {

        /**
        //var_dump(__METHOD__);
        $controllerClass = $event->getControllerClass();
        $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        if ($moduleNamespace != __NAMESPACE__) {
            return ;
        }
        //*/

        $request = $event->getRequest();
        if (!$request instanceof \Zend\Http\PhpEnvironment\Request) {
            return ;
        }

        $result = $event->getResult();
        if ($result instanceof \Zend\View\Model\ViewModel) {
            $result->setTerminal($request->isXmlHttpRequest()); // Disable layout
        }

    }

}
