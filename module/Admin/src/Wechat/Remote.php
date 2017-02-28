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
        $apiUrl = ApiURL::GetAccessTokenUrl($appid, $appsecret);
        return $this->sendGetRequest($apiUrl);
    }


    /**
     * @param string $access_token
     * @return array|bool
     */
    public function getCallbackHosts($access_token)
    {
        $apiUrl = ApiURL::GetWechatCallbackHosts($access_token);
        return $this->sendGetRequest($apiUrl);
    }


    /**
     * @param string $access_token
     * @param string $type
     * @param string|integer $scene
     * @param integer $expired
     * @return array|bool
     */
    public function createQrCode($access_token, $type, $scene, $expired)
    {
        $apiUrl = ApiURL::GetCreateQrCodeUrl($access_token);
        $post = new \stdClass();
        $sceneValue = new \stdClass();
        if ('QR_SCENE' == $type) {
            $post->expire_seconds = (int)$expired;
            $post->action_name = 'QR_SCENE';
            $sceneValue->scene_id = intval($scene);
        } else if('QR_LIMIT_SCENE' == $type) {
            $post->action_name = 'QR_LIMIT_SCENE';
            $sceneValue->scene_id = intval($scene);
        } else if('QR_LIMIT_STR_SCENE' == $type) {
            $post->action_name = 'QR_LIMIT_STR_SCENE';
            $sceneValue->scene_str = (string)$scene;
        } else {
            return false;
        }

        $sceneObj = new \stdClass();
        $sceneObj->scene = $sceneValue;

        $post->action_info = $sceneObj;

        return $this->sendPostRequest($apiUrl, json_encode($post));
    }


    /**
     * @param string $url
     * @param string $post
     * @return bool|array
     */
    private function sendPostRequest($url, $post)
    {
        $this->logger->debug('Send http post request: ' . PHP_EOL . $url);
        $result = $this->http->post($url, $post);
        if (!$result) {
            return false;
        }

        return json_decode($result, true);
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