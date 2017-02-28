<?php
/**
 * Local.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Wechat;


use Admin\Entity\Wechat;
use Admin\Service\WechatManager;
use Admin\Wechat\Exception\ExpiredException;
use Admin\Wechat\Exception\InvalidArgumentException;
use Admin\Wechat\Exception\RuntimeException;
use Application\Service\AppLogger;

class Local
{

    /**
     * @var WechatManager
     */
    private $wechatManager;

    /**
     * @var Remote
     */
    private $remote;

    /**
     * @var AppLogger
     */
    private $logger;


    public function __construct(WechatManager $wechatManager, Remote $remote, AppLogger $appLogger)
    {
        $this->wechatManager = $wechatManager;
        $this->remote = $remote;
        $this->logger = $appLogger;
    }


    /**
     * @param integer $wx_id
     * @return string
     * @throws InvalidArgumentException
     * @throws ExpiredException
     * @throws RuntimeException
     */
    public function getAccessToken($wx_id)
    {
        $wechat = $this->wechatManager->getWechat($wx_id);
        if (!$wechat instanceof Wechat) {
            throw new InvalidArgumentException('无效的微信公众号 ID: ' . $wx_id);
        }

        if (time() > $wechat->getWxExpired()) {
            throw new ExpiredException('公众号服务已经过期, 公众号 ID:' . $wx_id);
        }

        if (time() > $wechat->getWxAccessTokenExpired()) {

            $data = $this->remote->getAccessToken($wechat->getWxAppId(), $wechat->getWxAppSecret());

            if (isset($data['access_token']) && isset($data['expires_in'])) {
                $wechat->setWxAccessToken($data['access_token']);
                $expired = intval($data['expires_in'] * 0.9) + time();
                $wechat->setWxAccessTokenExpired(intval($expired));
                $this->wechatManager->saveModifiedEntity($wechat);
                return $data['access_token'];
            } else {
                throw new RuntimeException('与微信服务器通信错误: [' . $data['errcode'] . '] ' . $data['errmsg']);
            }

        } else {
            return $wechat->getWxAccessToken();
        }
    }


    /**
     * @param integer $wx_id
     * @return array|bool
     * @throws RuntimeException
     */
    public function getCallbackHosts($wx_id)
    {
        try {
            $token = $this->getAccessToken($wx_id);
        } catch (InvalidArgumentException $e) {
            $this->logger->excaption($e);
            return false;
        } catch (ExpiredException $e) {
            $this->logger->excaption($e);
            return false;
        } catch (RuntimeException $e) {
            $this->logger->excaption($e);
            return false;
        }

        $data = $this->remote->getCallbackHosts($token);
        if (isset($data['ip_list'])) {
            return $data['ip_list'];
        } else {
            throw new RuntimeException('与微信服务器通信错误: [' . $data['errcode'] . '] ' . $data['errmsg']);
        }
    }


    /**
     * @param string $wx_id
     * @param string $type
     * @param string|integer $scene
     * @param integer $expired
     * @return array|bool
     * @throws RuntimeException
     */
    public function createQrCode($wx_id, $type, $scene, $expired)
    {
        try {
            $token = $this->getAccessToken($wx_id);
        } catch (InvalidArgumentException $e) {
            $this->logger->excaption($e);
            return false;
        } catch (ExpiredException $e) {
            $this->logger->excaption($e);
            return false;
        } catch (RuntimeException $e) {
            $this->logger->excaption($e);
            return false;
        }

        $data = $this->remote->createQrCode($token, $type, $scene, $expired);
        if (isset($data['url'])) {
            return $data;
        } else {
            throw new RuntimeException('与微信服务器通信错误: [' . $data['errcode'] . '] ' . $data['errmsg']);
        }
    }



}