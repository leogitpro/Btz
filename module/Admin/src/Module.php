<?php
/**
 * Module class
 */

namespace Admin;


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
        $appConfig = $event->getApplication()->getServiceManager()->get('ApplicationConfig');
        $appEnv = isset($appConfig['application']['env']) ? $appConfig['application']['env'] : 'development';

        $viewModel = $event->getViewModel();
        $viewModel->setTemplate('layout/admin_layout');
        $viewModel->setVariable('appEnv', $appEnv);
    }

}
