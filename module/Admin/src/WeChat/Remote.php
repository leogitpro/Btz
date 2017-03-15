<?php
/**
 * Remote.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\WeChat;


use Admin\WeChat\Exception\InvalidArgumentException;
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
     * 提取公众号 AccessToken
     *
     * @param string $appId
     * @param string $appSecret
     * @return array
     * @throws InvalidArgumentException
     */
    public function getAccessToken($appId, $appSecret)
    {
        $apiUrl = ApiURL::GetAccessTokenUrl($appId, $appSecret);
        $result = $this->sendGetRequest($apiUrl);
        if (!isset($result['access_token']) || !isset($result['expires_in'])) {
            throw new InvalidArgumentException($result['errmsg'], $result['errcode']);
        }
        return $result;
    }


    /**
     * 微信服务器 IP 地址
     *
     * @param string $access_token
     * @return array
     * @throws InvalidArgumentException
     */
    public function getCallbackHosts($access_token)
    {
        $apiUrl = ApiURL::GetWechatCallbackHosts($access_token);
        $res = $this->sendGetRequest($apiUrl);
        if (!isset($res['ip_list'])) {
            throw new InvalidArgumentException($res['errmsg'], $res['errcode']);
        }
        return (array)$res['ip_list'];
    }


    /**
     * 创建带参数微信二维码
     *
     * @param string $access_token
     * @param string $type
     * @param string|integer $scene
     * @param integer $expired
     * @return array
     * @throws InvalidArgumentException
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
            throw new InvalidArgumentException('Invalid QRCode type: ' . $type, 9999);
        }

        $sceneObj = new \stdClass();
        $sceneObj->scene = $sceneValue;

        $post->action_info = $sceneObj;

        $res = $this->sendPostRequest($apiUrl, json_encode($post));
        if (!isset($res['url']) || !isset($res['ticket'])) {
            throw new InvalidArgumentException($res['errmsg'], $res['errcode']);
        }

        return $res;
    }


    /**
     * 用户标签
     *
     * @param string $access_token
     * @return array
     * @throws InvalidArgumentException
     */
    public function getTags($access_token)
    {
        $result = $this->sendGetRequest(ApiURL::GetTagUrl($access_token));

        if (!isset($result['tags'])) {
            throw new InvalidArgumentException($result['errmsg'], $result['errcode']);
        }

        return (array)$result['tags'];
    }


    /**
     * 删除自定义菜单
     *
     * @param string $access_token
     * @return true
     * @throws InvalidArgumentException
     */
    public function deleteDefaultMenu($access_token)
    {
        $res = $this->sendGetRequest(ApiURL::GetMenuDeleteDefaultUrl($access_token));

        $errCode = @$res['errcode'];
        if (0 != $errCode) {
            throw new InvalidArgumentException($res['errmsg'], $errCode);
        }

        return true;
    }


    /**
     * 创建自定义菜单
     *
     * @param string $access_token
     * @param string $menu
     * @return true
     * @throws InvalidArgumentException
     */
    public function createDefaultMenu($access_token, $menu)
    {
        $apiUrl = ApiURL::GetMenuCreateDefaultUrl($access_token);
        $res = $this->sendPostRequest($apiUrl, $menu);

        $errCode = @$res['errcode'];
        if (0 != $errCode) {
            throw new InvalidArgumentException($res['errmsg'], $errCode);
        }

        return true;
    }


    /**
     * 删除个性化菜单
     *
     * @param $access_token
     * @param string $menuid
     * @return true
     * @throws InvalidArgumentException
     */
    public function deleteConditionalMenu($access_token, $menuid)
    {
        $post = new \stdClass();
        $post->menuid = (string)$menuid;

        $apiUrl = ApiURL::GetMenuDeleteConditionalUrl($access_token);

        $res = $this->sendPostRequest($apiUrl, json_encode($post));

        $errCode = @$res['errcode'];
        if (0 != $errCode) {
            throw new InvalidArgumentException($res['errmsg'], $errCode);
        }

        return true;
    }


    /**
     * 创建个性化菜单
     *
     * @param string $access_token
     * @param string $menu
     * @return string
     * @throws InvalidArgumentException
     */
    public function createConditionalMenu($access_token, $menu)
    {
        $apiUrl = ApiURL::GetMenuCreateConditionalUrl($access_token);
        $res = $this->sendPostRequest($apiUrl, $menu);

        if (!isset($res['menuid'])) {
            throw new InvalidArgumentException($res['errmsg'], $res['errcode']);
        }

        return $res['menuid'];
    }

    /**
     * 导出公众号菜单
     *
     * @param string $access_token
     * @return array
     */
    public function exportMenu($access_token)
    {
        $apiUrl = ApiURL::GetMenuExportUrl($access_token);
        $res = $this->sendGetRequest($apiUrl);

        if (isset($res['errcode'])) {
            throw new InvalidArgumentException($res['errmsg'], $res['errcode']);
        }

        return $res;
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