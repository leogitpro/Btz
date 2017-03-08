<?php
/**
 * Remote.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\WeChat;


use Admin\WeChat\Exception\NetworkRequestException;
use Application\Service\AppLogger;
use Zend\Json\Json;


class Remote
{
    /**
     * @var AppLogger
     */
    private $logger;

    public function __construct(AppLogger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @param string $appId
     * @param string $appSecret
     * @return array
     */
    public function getAccessToken($appId, $appSecret)
    {
        $apiUrl = ApiURL::GetAccessTokenUrl($appId, $appSecret);
        return $this->sendGetRequest($apiUrl);
    }


    /**
     * @param string $access_token
     * @return array
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
     * @return array
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
            return [];
        }

        $sceneObj = new \stdClass();
        $sceneObj->scene = $sceneValue;

        $post->action_info = $sceneObj;

        return $this->sendPostRequest($apiUrl, Json::encode($post, true));
    }


    /**
     * @param $access_token
     * @return array
     */
    public function getTags($access_token)
    {
        return $this->sendGetRequest(ApiURL::GetTagUrl($access_token));
    }



    /**
     * @param string $url
     * @param string $post
     * @return array
     */
    private function sendPostRequest($url, $post)
    {
        try {
            $response = Http::Post($url, $post);
        } catch (NetworkRequestException $e) {
            $this->logger->excaption($e);
            return [];
        }

        return Json::decode($response, Json::TYPE_ARRAY);
    }



    /**
     * @param string $url
     * @return array
     */
    private function sendGetRequest($url)
    {
        try {
            $response = Http::Get($url);
        } catch (NetworkRequestException $e) {
            $this->logger->excaption($e);
            return [];
        }

        return Json::decode($response, Json::TYPE_ARRAY);
    }

}