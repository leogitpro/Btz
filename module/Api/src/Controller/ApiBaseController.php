<?php
/**
 * ApiBaseController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Api\Controller;


use Admin\Service\WechatManager;
use Application\Controller\AppBaseController;
use Zend\Json\Json;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class ApiBaseController extends AppBaseController
{

    public function onDispatch(MvcEvent $e)
    {
        $response = parent::onDispatch($e);

        $headers = $this->getResponse()->getHeaders();

        if ($response instanceof JsonModel) {
            $value = $response->getVariables();
            $headers->addHeaderLine('content-type', 'application/json; charset=UTF-8');
            $this->getResponse()->setContent(Json::encode($value, true));
            return $this->getResponse();
        }

        $headers->addHeaderLine('content-type', 'text/html; charset=UTF-8');

        return $this->getResponse();
    }

    /**
     * @return WechatManager
     */
    protected function getWechatManager()
    {
        return $this->getSm(WechatManager::class);
    }

    /**
     * @param array|mixed $data
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function sendJsonResponse($data)
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('content-type', 'application/json; charset=UTF-8');
        $response->setContent(Json::encode($data, true));
        return $response;
    }

    /**
     * @param array|mixed $data
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function sendHtmlResponse($data = '')
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('content-type', 'text/html; charset=UTF-8');
        $response->setContent($data);
        return $response;
    }


}