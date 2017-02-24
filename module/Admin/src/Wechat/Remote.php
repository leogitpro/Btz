<?php
/**
 * Remote.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Wechat;


use Application\Service\AppLogger;

class Remote
{

    /**
     * @var Http
     */
    private $http;

    /**
     * @var AppLogger
     */
    private $logger;

    public function __construct(Http $http, AppLogger $logger)
    {
        $this->http = $http;
        $this->logger = $logger;
    }


    /**
     * @param string $appid
     * @param string $appsecret
     * @return array|bool
     */
    public function getAccessToken($appid, $appsecret)
    {
        $apiUrl = Api::GetAccessTokenUrl($appid, $appsecret);
        return $this->sendGetRequest($apiUrl);
    }


    /**
     * @param string $access_token
     * @return array|bool
     */
    public function getCallbackHosts($access_token)
    {
        $apiUrl = Api::GetWechatCallbackHosts($access_token);
        return $this->sendGetRequest($apiUrl);
    }


    /**
     * @param string $url
     * @return bool|array
     */
    private function sendGetRequest($url)
    {
        $this->logger->debug('Send http get request: ' . PHP_EOL . $url);

        $result = $this->http->get($url);
        if (!$result) {
            return false;
        }

        return json_decode($result, true);
    }

}