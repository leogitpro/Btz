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
use Zend\Session\SessionManager;


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
        $sharedEventManager->attach(\Zend\Mvc\Application::class, MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchErrorListener'], 100);
    }

    /**
     * @param MvcEvent $event
     * @throws RuntimeException
     */
    public function onDispatchListener(MvcEvent $event)
    {

        // Init Default Session
        $event->getApplication()->getServiceManager()->get(SessionManager::class);

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

        $request = $event->getRequest();
        if ($request instanceof \Zend\Http\PhpEnvironment\Request) {

            $serviceManager = $event->getApplication()->getServiceManager();
            $logger = $serviceManager->get('Logger');

            $logger->exception($exception);

            $response = $event->getResponse();
            if(!$response instanceof \Zend\Http\PhpEnvironment\Response) {
                $response = new \Zend\Http\PhpEnvironment\Response();
            }

            $responseAcceptJson = false;
            $requestAccept = $request->getHeaders('Accept');
            if ($requestAccept instanceof \Zend\Http\Header\Accept) {
                /**
                $matched = $requestAccept->match('application/json');
                if($matched instanceof \Zend\Http\Header\Accept\FieldValuePart\AcceptFieldValuePart) {
                    if('json' == $matched->getFormat()) {
                        $responseAcceptJson = true;
                    }
                }
                //*/

                //**
                $requestAcceptValue = $requestAccept->getFieldValue();
                if(preg_match("/application\\/json/", $requestAcceptValue)) {
                    $responseAcceptJson = true;
                }
                //*/
            }

            if($responseAcceptJson) {

                $headerContentType = new \Zend\Http\Header\ContentType();
                $headerContentType->setMediaType('application/json');
                $headerContentType->setCharset('UTF-8');

                $responseHeaders = new \Zend\Http\Headers();
                $responseHeaders->addHeader($headerContentType);
                $response->setHeaders($responseHeaders);

                $response->setStatusCode(200);

                $content = json_encode(
                    ['success' => false, 'errcode' => '9999', 'errmsg' => $exception->getMessage()],
                    JSON_UNESCAPED_UNICODE
                );
                $response->setContent($content);

                $event->setResult($response);
            }
        }
    }

}
