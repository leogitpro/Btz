<?php
/**
 * Module.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application;


use Zend\Mvc\MvcEvent;


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
        $serviceManager = $event->getApplication()->getServiceManager();

        $viewModel = $event->getViewModel();

        $appConfig = $serviceManager->get('ApplicationConfig');
        $appEnv = isset($appConfig['application']['env']) ? $appConfig['application']['env'] : 'development';
        $viewModel->setVariable('appEnv', $appEnv);

    }

}
