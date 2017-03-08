<?php
/**
 * WeChatService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\WeChat;


use Admin\WeChat\Exception\ExpiredException;
use Admin\WeChat\Exception\InvalidArgumentException;
use Admin\WeChat\Exception\RuntimeException;
use Application\Service\AppLogger;


class WeChatService
{

    /**
     * @var integer
     */
    private $wxId;

    /**
     * @var Local
     */
    private $local;

    /**
     * @var AppLogger
     */
    private $logger;


    public function __construct($wxId, Local $local, AppLogger $logger)
    {
        $this->wxId = $wxId;
        $this->local = $local;
        $this->logger = $logger;
    }


    /**
     * 获取微信基础 Access Token
     *
     * @return string|false
     */
    public function getAccessToken()
    {
        return $this->local->getAccessToken();
    }


    /**
     * 查询微信服务器 IP 地址
     *
     * @return array
     */
    public function getCallbackHosts()
    {
        try {
            return $this->local->getCallbackHosts();
        } catch (RuntimeException $e) {
            $this->logger->excaption($e);
        }

        return [];
    }


    /**
     * 微信公众号用户标签列表
     *
     * @return array
     */
    public function getTags()
    {
        return $this->local->getTags();
    }


    /**
     * 生成带参数微信二维码
     *
     * @param string $type
     * @param string|int $scene
     * @param int $expired
     * @return array
     */
    public function getQrCode($type, $scene, $expired = 0)
    {
        try {
            return $this->local->createQrCode($type, $scene, $expired);
        } catch (RuntimeException $e) {
            $this->logger->excaption($e);
        }
        return [];
    }





}