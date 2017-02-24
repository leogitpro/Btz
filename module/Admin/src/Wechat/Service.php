<?php
/**
 * Service.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Wechat;


use Admin\Wechat\Exception\ExpiredException;
use Admin\Wechat\Exception\InvalidArgumentException;
use Admin\Wechat\Exception\RuntimeException;
use Application\Service\AppLogger;


class Service
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
     * @return string
     */
    public function getAccessToken()
    {
        try {
            return $this->local->getAccessToken($this->wxId);
        } catch (InvalidArgumentException $e) {
            $this->logger->err($e->getMessage());
        } catch (ExpiredException $e) {
            $this->logger->err($e->getMessage());
        } catch (RuntimeException $e) {
            $this->logger->err($e->getMessage());
        }

        return '';
    }


    /**
     * 查询微信服务器 IP 地址
     *
     * @return array
     */
    public function getCallbackHosts()
    {
        try {
            return $this->local->getCallbackHosts($this->wxId);
        } catch (RuntimeException $e) {
            $this->logger->err($e->getMessage());
        }

        return [];
    }





}