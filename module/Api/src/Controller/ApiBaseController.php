<?php
/**
 * ApiBaseController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Api\Controller;


use Application\Controller\AppBaseController;
use WeChat\Service\AccountService;
use WeChat\Service\OauthService;
use WeChat\Service\WeChatService;
use Zend\Http\Header\ContentType;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class ApiBaseController extends AppBaseController
{
    const MEDIA_TYPE_JSON = 'application/json';
    const MEDIA_TYPE_HTML = 'text/html';
    const API_CHARSET = 'UTF-8';


    public function onDispatch(MvcEvent $e)
    {
        $viewModel = parent::onDispatch($e);

        if ($viewModel instanceof JsonModel) {
            $params = $viewModel->getVariables();

            $response = $this->getResponse();
            $response->setContent(json_encode($params, JSON_UNESCAPED_UNICODE));
            return $response;
        }

        if ($viewModel instanceof ViewModel) {
            $viewModel->setTerminal(true);

            return $viewModel;
        }

        return $this->getResponse();
    }


    protected function declareResponseContentType($type = 'application/json')
    {
        $headerContentType = new ContentType();
        if ($type == self::MEDIA_TYPE_JSON) {
            $headerContentType->setMediaType(self::MEDIA_TYPE_JSON);
        } else {
            $headerContentType->setMediaType(self::MEDIA_TYPE_HTML);
        }
        $headerContentType->setCharset(self::API_CHARSET);

        $response = $this->getResponse();
        if($response instanceof Response) {
            $response->getHeaders()->addHeader($headerContentType);
        }
    }


    /**
     * @return WeChatService
     */
    protected function getWeChatService()
    {
        return $this->getSm(WeChatService::class);
    }


    /**
     * @return AccountService
     */
    protected function getWeChatAccountService()
    {
        return $this->getSm(AccountService::class);
    }


    /**
     * @return OauthService
     */
    protected function getWeChatOauthService()
    {
        return $this->getSm(OauthService::class);
    }


}