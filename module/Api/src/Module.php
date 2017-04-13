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

        // Register listener
        $sharedEventManager->attach(\Zend\Mvc\Application::class, MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchErrorListener'], 100);
    }



    /**
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

        
        $request = $event->getRequest();
        if ($request instanceof \Zend\Http\PhpEnvironment\Request) {

            $response = new \Zend\Http\PhpEnvironment\Response();
            //$response->setStatusCode(500);
            $content = $exception->getMessage();

            $headerContentType = new \Zend\Http\Header\ContentType();

            if($request->isXmlHttpRequest()) {
                $headerContentType->setMediaType('application/json');
                $content = \Zend\Json\Encoder::encode(['errcode' => '9999', 'errmsg' => $content]);
            } else {
                $headerContentType = new \Zend\Http\Header\ContentType();
                $headerContentType->setMediaType('text/html');
            }

            $headerContentType->setCharset('UTF-8');

            $responseHeaders = new \Zend\Http\Headers();
            $responseHeaders->addHeader($headerContentType);

            $response->setHeaders($responseHeaders);

            $response->setStatusCode(200);

            $response->setContent($content);
            $event->setResult($response);
        }

    }


}