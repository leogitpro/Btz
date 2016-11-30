<?php
/**
 * Module class
 */

namespace Admin;


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
        $eventManager = $manager->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();

        $priority = 100;
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], $priority);
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], $priority);
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onError'], $priority);
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_RENDER_ERROR, [$this, 'onError'], $priority);

        //$event = $manager->getEvent();
        //$container = $event->getParam('ServiceManager');

        //var_dump($container->get('appLogger'));
        //$logger = $container->get("");

        /**
        $event = $manager->getEvent();
        $container = $event->getParam('ServiceManager');
        $logger = $container->get("appLogger");

        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], $priority);
        $logger->debug(
            'Regeist listener' . PHP_EOL .
            'identify: ' . __NAMESPACE__ . PHP_EOL .
            'event: ' . MvcEvent::EVENT_ROUTE . PHP_EOL .
            'callback: ' . __CLASS__ . '::onRoute' . PHP_EOL .
            'priority: ' . $priority
        );

        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], $priority);
        $logger->debug(
            'Regeist listener' . PHP_EOL .
            'identify: ' . __NAMESPACE__ . PHP_EOL .
            'event: ' . MvcEvent::EVENT_DISPATCH . PHP_EOL .
            'callback: '. __CLASS__ .'::onDispatch' . PHP_EOL .
            'priority: ' . $priority
        );

        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onError'], $priority);
        $logger->debug(
            'Regeist listener' . PHP_EOL .
            'identify: ' . __NAMESPACE__ . PHP_EOL .
            'event: ' . MvcEvent::EVENT_DISPATCH_ERROR . PHP_EOL .
            'callback: '. __CLASS__ .'::onError' . PHP_EOL .
            'priority: ' . $priority
        );

        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_RENDER_ERROR, [$this, 'onError'], $priority);
        $logger->debug(
            'Regeist listener' . PHP_EOL .
            'identify: ' . __NAMESPACE__ . PHP_EOL .
            'event: ' . MvcEvent::EVENT_RENDER_ERROR . PHP_EOL .
            'callback: '. __CLASS__ .'::onError' . PHP_EOL .
            'priority: ' . $priority
        );
        //*/
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


    /**
     * Custom dispatch lisener
     * Use custom layout for this module
     *
     * @param MvcEvent $event
     */
    public function onDispatch(MvcEvent $event)
    {
        $controller = $event->getTarget();
        $controllerClass = get_class($controller);
        $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        if ($moduleNamespace == __NAMESPACE__) {
            $viewModel = $event->getViewModel();
            $viewModel->setTemplate('layout/admin_simple');

            $logger = $event->getApplication()->getServiceManager()->get('AppLogger');
            $logger->debug("Use custom layout: layout/admin_simple");
        }
    }


    /**
     * Custom error lisener
     *
     * @param MvcEvent $event
     */
    public function onError(MvcEvent $event)
    {
        // Get the exception information.
        $exception = $event->getParam('exception');
        if ($exception!=null) {
            $exceptionName = $exception->getMessage();
            $file = $exception->getFile();
            $line = $exception->getLine();
            $stackTrace = $exception->getTraceAsString();
        }
        $errorMessage = $event->getError();
        $controllerName = $event->getController();

        // Prepare email message.
        $to = 'admin@yourdomain.com';
        $subject = 'Website Exception';

        $body = '';
        if(isset($_SERVER['REQUEST_URI'])) {
            $body .= "Request URI: " . $_SERVER['REQUEST_URI'] . "\n\n";
        }
        $body .= "Controller: $controllerName\n";
        $body .= "Error message: $errorMessage\n";
        if ($exception!=null) {
            $body .= "Exception: $exceptionName\n";
            $body .= "File: $file\n";
            $body .= "Line: $line\n";
            $body .= "Stack trace:\n\n" . $stackTrace;
        }

        $logger = $event->getApplication()->getServiceManager()->get('AppLogger');
        $logger->debug(PHP_EOL . $body);

        //$body = str_replace("\n", "<br>", $body);

        // Send an email about the error.
        //mail($to, $subject, $body);
    }
}
