<?php
/**
 * Local.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\WeChat;

use Admin\Entity\WeChat;
use Admin\Service\WeChatManager;
use Admin\WeChat\Exception\RuntimeException;
use Application\Service\AppLogger;


class Local
{

    /**
     * @var WeChatManager
     */
    private $weChatManager;

    /**
     * @var Remote
     */
    private $remote;

    /**
     * @var AppLogger
     */
    private $logger;

    /**
     * @var integer
     */
    private $wxId;


    public function __construct($wxId, WeChatManager $weChatManager, Remote $remote, AppLogger $appLogger)
    {
        $this->wxId = $wxId;
        $this->weChatManager = $weChatManager;
        $this->remote = $remote;
        $this->logger = $appLogger;
    }


    /**
     * @return string|false
     */
    public function getAccessToken()
    {
        $weChat = $this->weChatManager->getWeChat($this->wxId);
        if (!$weChat instanceof WeChat) {
            $this->logger->err('无效的微信公众号 ID: ' . $this->wxId);
            return false;
        }

        if (time() > $weChat->getWxExpired()) {
            $this->logger->err('公众号服务已经过期, 公众号 ID:' . $this->wxId);
            return false;
        }

        if (time() > $weChat->getWxAccessTokenExpired()) {

            $data = $this->remote->getAccessToken($weChat->getWxAppId(), $weChat->getWxAppSecret());

            if (isset($data['access_token']) && isset($data['expires_in'])) {

                $expired = intval($data['expires_in'] * 0.9) + time();

                $weChat->setWxAccessTokenExpired(intval($expired));
                $weChat->setWxAccessToken($data['access_token']);

                $this->weChatManager->saveModifiedEntity($weChat);

                return $data['access_token'];
            } else {
                $this->logger->err('与微信服务器通信错误: ' . $data['errmsg'] . ' : '. $data['errcode']);
                return false;
            }

        } else {
            return $weChat->getWxAccessToken();
        }
    }


    /**
     * @return array|bool
     * @throws RuntimeException
     */
    public function getCallbackHosts()
    {
        $token = $this->getAccessToken();
        $data = $this->remote->getCallbackHosts($token);
        if (isset($data['ip_list'])) {
            return $data['ip_list'];
        } else {
            throw new RuntimeException('与微信服务器通信错误: ' . $data['errmsg'], $data['errcode']);
        }
    }


    /**
     * Get WeChat Tags
     *
     * @return array
     */
    public function getTags()
    {
        $token = $this->getAccessToken();
        $data = $this->remote->getTags($token);
        return isset($data['tags']) ? $data['tags'] : [];
    }



    /**
     * @param string $type
     * @param string|integer $scene
     * @param integer $expired
     * @return array|bool
     * @throws RuntimeException
     */
    public function createQrCode($type, $scene, $expired)
    {
        $token = $this->getAccessToken();
        $data = $this->remote->createQrCode($token, $type, $scene, $expired);
        if (isset($data['url'])) {
            return $data;
        } else {
            throw new RuntimeException('与微信服务器通信错误: ' . $data['errmsg'], $data['errcode']);
        }
    }


    /**
     * @return array
     */
    public function deleteAllMenus()
    {
        $token = $this->getAccessToken();
        return $this->remote->deleteAllMenus($token);
    }

    /**
     * @param string $menu
     * @return array
     */
    public function createDefaultMenu($menu)
    {
        $token = $this->getAccessToken();
        return $this->remote->createDefaultMenu($token, $menu);
    }



}