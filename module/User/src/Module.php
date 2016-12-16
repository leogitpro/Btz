<?php
/**
 * Module.php
 *
 * User Module class
 *
 */

namespace User;


use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;


class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }




    public function init(ModuleManager $manager)
    {

        //$eventManager = $manager->getEventManager();
        //$sharedEventManager = $eventManager->getSharedManager();

        //$priority = 100;
        //$sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], $priority);
        //$sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onRoute'], $priority);
    }


    /**
     * Custom route lisener
     *
     * @param MvcEvent $event
     */
    public function onRoute(MvcEvent $event)
    {
        if (php_sapi_name() == "cli") {
            return;
        }


        $sm = $event->getApplication()->getServiceManager();

        $config = $sm->get("config");

        echo '<pre>';
        print_r($config);
        echo '</pre>';

        //$routeMatch = $event->getRouteMatch();

        exit();
        /**
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

}