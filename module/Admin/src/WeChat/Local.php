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
use Admin\WeChat\Exception\InvalidArgumentException;
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
     * 公众号 AccessToken
     *
     * @return string
     * @throws RuntimeException
     */
    public function getAccessToken()
    {
        $weChat = $this->weChatManager->getWeChat($this->wxId);
        if (!$weChat instanceof WeChat) {
            throw new RuntimeException('无效的微信公众号ID', 9999);
        }

        if (time() > $weChat->getWxExpired()) {
            throw new RuntimeException('公众号服务已经过期, ID:' . $this->wxId, 9999);
        }

        if (time() > $weChat->getWxAccessTokenExpired()) {

            try {
                $data = $this->remote->getAccessToken($weChat->getWxAppId(), $weChat->getWxAppSecret());
            } catch (InvalidArgumentException $e) {
                throw new RuntimeException($e->getMessage(), $e->getCode());
            }

            $expired = intval($data['expires_in'] * 0.9) + time();
            $weChat->setWxAccessTokenExpired(intval($expired));
            $weChat->setWxAccessToken($data['access_token']);
            $this->weChatManager->saveModifiedEntity($weChat);

            return $data['access_token'];
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
     * 删除自定义菜单
     *
     * @return bool
     */
    public function deleteDefaultMenu()
    {
        try {
            $token = $this->getAccessToken();
        } catch (RuntimeException $e) {
            $this->logger->excaption($e);
            return false;
        }

        try {
            return $this->remote->deleteDefaultMenu($token);
        } catch (InvalidArgumentException $e) {
            $this->logger->excaption($e);
        }

        return false;
    }


    /**
     * 创建自定义菜单
     *
     * @param string $menu
     * @return bool
     */
    public function createDefaultMenu($menu)
    {
        try {
            $token = $this->getAccessToken();
        } catch (RuntimeException $e) {
            $this->logger->excaption($e);
            return false;
        }

        try {
            return $this->remote->createDefaultMenu($token, $menu);
        } catch (InvalidArgumentException $e) {
            $this->logger->excaption($e);
        }
        return false;
    }


    /**
     * 删除个性化菜单
     *
     * @param string $menuid
     * @return bool
     */
    public function deleteConditionalMenu($menuid)
    {
        try {
            $token = $this->getAccessToken();
        } catch (RuntimeException $e) {
            $this->logger->excaption($e);
            return false;
        }

        try {
            return $this->remote->deleteConditionalMenu($token, $menuid);
        } catch (InvalidArgumentException $e) {
            $this->logger->excaption($e);
        }

        return false;
    }


    /**
     * 创建个性化菜单
     *
     * @param string $menu
     * @return string
     */
    public function createConditionalMenu($menu)
    {
        try {
            $token = $this->getAccessToken();
        } catch (RuntimeException $e) {
            $this->logger->excaption($e);
            return null;
        }

        try {
            return $this->remote->createConditionalMenu($token, $menu);
        } catch (InvalidArgumentException $e) {
            $this->logger->excaption($e);
        }

        return null;
    }




}