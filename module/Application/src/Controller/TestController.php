<?php
/**
 * TestController.php
 *
 * User: leo
 * Date: 17/4/16
 * Time: 下午5:40
 */

namespace Application\Controller;


use Application\Exception\RuntimeException;
use Zend\Http\Client;
use Zend\Http\Header\ContentType;
use Zend\Http\Header\UserAgent;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class TestController extends AppBaseController
{

    public function indexAction()
    {
        $key = (string)$this->params()->fromRoute('key');
        $apiList = $this->getConfigPlugin()->get('api_list.weixin');
        $apiKeys = array_keys($apiList);
        if (empty($key)) {
            $key = array_shift($apiKeys);
        }

        return new ViewModel([
            'apiList' => $apiList,
            'apiKey' => $key,
            'activeId' => 'test',
        ]);
    }


    public function simulatorAction()
    {
        $key = (string)$this->params()->fromRoute('key', 'api');

        $wxid = (int)$this->params()->fromPost('wxid', 0);
        $identifier = (string)$this->params()->fromPost('identifier', '');

        $apiUrl = 'http://www.bentuzi.com/weixin/' . $key . '/' . $wxid . '/' . $identifier . '.json';
        if('userinfo' == $key) {
            $openid = (string)$this->params()->fromPost('openid', '');
            $apiUrl .= '?openid=' . $openid;
        }
        if('jssign' == $key) {
            $url = (string)$this->params()->fromPost('url', '');
            $apiUrl .= '?url=' . urlencode($url);
        }

        $res = $this->sendHttpRequest($apiUrl);
        if('{' != substr($res, 0, 1) || '}' != substr($res, -1)) {
            throw new RuntimeException('无效的 JSON 数据' . PHP_EOL . $res);
        }

        $response = $this->getResponse();
        if (!$response instanceof Response) {
            $response = new Response();
        }

        $contentType = new ContentType();
        $contentType->setMediaType('text/html');
        $contentType->setCharset('UTF-8');

        $headers = new Headers();
        $headers->addHeader($contentType);

        $response->setHeaders($headers);
        $response->setContent($res);
        return $response;
    }




    private function sendHttpRequest($url)
    {
        $headerContentType = new ContentType();
        $headerContentType->setMediaType('application/x-www-form-urlencoded');
        $headerContentType->setCharset('UTF-8');

        $headerUserAgent = new UserAgent('Btz/1.0');

        $requestHeaders = new Headers();
        $requestHeaders->addHeader($headerContentType);
        $requestHeaders->addHeader($headerUserAgent);

        $request = new Request();
        $request->setHeaders($requestHeaders);
        $request->setUri($url);
        $request->setVersion(Request::VERSION_11);

        $request->setMethod(Request::METHOD_GET);

        $client = new Client();
        $client->setRequest($request);
        $client->setOptions([
            'maxredirects' => 0,
            'timeout' => 30,
        ]);

        $response = $client->send();

        if(!$response->isSuccess()) {
            throw new RuntimeException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return $response->getBody();
    }


}