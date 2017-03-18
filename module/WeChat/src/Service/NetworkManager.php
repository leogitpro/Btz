<?php
/**
 * NetworkManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace WeChat\Service;


use WeChat\Exception\RuntimeException;
use Zend\Http\Client;
use Zend\Http\Header\ContentType;
use Zend\Http\Header\Cookie;
use Zend\Http\Header\SetCookie;
use Zend\Http\Header\UserAgent;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;


class NetworkManager extends BaseManager
{

    //todo



    /**
     * @param string $url
     * @param mixed $data
     * @return array
     * @throws RuntimeException
     */
    private function sendPostRequest($url, $data)
    {
        try {
            $res = $this->sendRequest($url, $data, 'POST');
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }

        if('{' != substr($res, 0, 1) || '}' != substr($res, -1)) {
            throw new RuntimeException('无效的 JSON 数据' . PHP_EOL . $res);
        }

        return json_decode($res, true);
    }


    /**
     * @param string $url
     * @return array
     * @throws RuntimeException
     */
    private function sendGetRequest($url)
    {
        try {
            $res = $this->sendRequest($url);
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }

        if('{' != substr($res, 0, 1) || '}' != substr($res, -1)) {
            throw new RuntimeException('无效的 JSON 数据' . PHP_EOL . $res);
        }

        return json_decode($res, true);
    }


    /**
     *
     * $method: GET|POST
     * $headers: [
     *             'Accept-Encoding' => 'gzip, deflate',
     *             'Referer' => 'http://www.example.com/',
     *             'X-Requested-With' => 'XMLHttpRequest',
     *             ...]
     * $cookies: ['PHPSESSID' => 'a3065bd45b18847718e202f5bd6306ed', ...]
     *
     * @param string $url
     * @param mixed $data
     * @param string $method
     * @param array $headers
     * @param array $cookies
     * @return string
     * @throws RuntimeException
     */
    private function sendRequest($url, $data = null, $method = 'GET', $headers = [], $cookies = [])
    {
        $headerContentType = new ContentType();
        $headerContentType->setMediaType('GET' == strtoupper($method) ? 'text/html' : 'application/x-www-form-urlencoded');
        $headerContentType->setCharset('UTF-8');

        $headerUserAgent = new UserAgent('Btz/1.0');

        $requestHeaders = new Headers();
        $requestHeaders->addHeader($headerContentType);
        $requestHeaders->addHeader($headerUserAgent);
        if(!empty($headers)) {
            foreach ($headers as $key => $value) {
                if($key != $headerContentType->getFieldName() && $key != $headerUserAgent->getFieldName()) {
                    $requestHeaders->addHeaderLine($key, $value);
                }
            }
        }

        if(!empty($cookies)) {
            $setCookies = [];
            foreach ($cookies as $key => $value) {
                $setCookies[] = new SetCookie($key, $value);
            }
            if(empty($setCookies)) {
                $headerCookie = new Cookie($setCookies);
                $requestHeaders->addHeader($headerCookie);
            }
        }

        $request = new Request();
        $request->setHeaders($requestHeaders);
        $request->setUri($url);
        $request->setVersion(Request::VERSION_11);

        if ($method == Request::METHOD_GET) {
            $request->setMethod(Request::METHOD_GET);
            if (is_array($data) && !empty($data)) {
                $request->setQuery(new Parameters($data));
            }
        } else {
            $request->setMethod(Request::METHOD_POST);
            if (!empty($data)) {
                if(is_array($data)) {
                    $request->setPost(new Parameters($data));
                } else {
                    $request->setContent($data);
                }
            }
        }

        $client = new Client();
        $client->setRequest($request);
        $client->setOptions([
            'maxredirects' => 0,
            'timeout' => 30,
        ]);

        $this->logger->debug('Request: ' . PHP_EOL . $request->toString());

        $response = $client->send();

        $this->logger->debug('Response: ' . PHP_EOL . $response->toString());

        if(!$response->isSuccess()) {
            throw new RuntimeException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return $response->getBody();
    }
}