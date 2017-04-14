<?php
/**
 * Module.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Api;


use Zend\Mvc\MvcEvent;


class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        // Get shared event manager
        $sharedEventManager = $event->getApplication()->getEventManager()->getSharedManager();

        // Register dispatch listener
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatchListener'], 100);

        // Register dispatch error listener
        $sharedEventManager->attach(\Zend\Mvc\Application::class, MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchErrorListener'], 100);
    }


    /**
     * @param MvcEvent $event
     */
    public function onDispatchListener(MvcEvent $event)
    {
        //var_dump(__NAMESPACE__);
    }



    /**
     * Module exception
     *
     * @param MvcEvent $event
     */
    public function onDispatchErrorListener(MvcEvent $event)
    {
        $controllerClass = $event->getControllerClass();
        $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        if ($moduleNamespace != __NAMESPACE__) {
            return ;
        }

        $exception = $event->getParam('exception');
        if(!($exception instanceof \Exception) && !($exception instanceof \Throwable)) {
            return ;
        }

        $serviceManager = $event->getApplication()->getServiceManager();
        $logger = $serviceManager->get('Logger');
        $logger->exception($exception);


        $response = $event->getResponse();
        $resetContented = false;

        if($response instanceof \Zend\Http\PhpEnvironment\Response) {
            if ($response->getHeaders()->has('Content-Type')) {
                $contentType = $response->getHeaders()->get('Content-Type');
                if ($contentType instanceof \Zend\Http\Header\ContentType) {
                    if('application/json' == $contentType->getMediaType()) {
                        $response->setContent(json_encode(
                            ['success' => false, 'errcode' => 9999, 'errmsg' => $exception->getMessage()],
                            JSON_UNESCAPED_UNICODE
                        ));
                        $resetContented = true;
                    }
                }
            }
        } else {
            $response = new \Zend\Http\PhpEnvironment\Response();
            $headerContentType = new \Zend\Http\Header\ContentType();
            $headerContentType->setMediaType('text/html');
            $headerContentType->setCharset('UTF-8');
            $response->getHeaders()->addHeader($headerContentType);
        }
        if(!$resetContented) {
            $response->setContent($exception->getMessage());
        }

        $response->setStatusCode(200);

        $event->setResult($response);
    }


}