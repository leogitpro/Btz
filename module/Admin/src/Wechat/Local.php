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
            throw new InvalidArgumentException('The register wechat id is invalid.');
        }

        if (time() > $wechat->getWxExpired()) {
            throw new ExpiredException('The wechat api service is expired.');
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
                throw new RuntimeException($data['errcode'] . ':' . $data['errmsg']);
            }

        } else {
            return $wechat->getWxAccessToken();
        }
    }


    /**
     * @param integer $wx_id
     * @return array
     * @throws RuntimeException
     */
    public function getCallbackHosts($wx_id)
    {
        $token = $this->getAccessToken($wx_id);
        $data = $this->remote->getCallbackHosts($token);
        if (isset($data['ip_list'])) {
            return $data['ip_list'];
        } else {
            throw new RuntimeException($data['errcode'] . ':' . $data['errmsg']);
        }
    }


}