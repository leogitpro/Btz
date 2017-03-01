<?php
/**
 * WeChatQrCodeManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Service;


use Admin\Entity\WeChat;
use Admin\Entity\WeChatQrCode;
use Ramsey\Uuid\Uuid;


class WeChatQrCodeManager extends BaseEntityManager
{


    /**
     * @param string $qrCodeId
     * @return WeChatQrCode
     */
    public function getWeChatQrCode($qrCodeId)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(WeChatQrCode::class, 't');
        $qb->where($qb->expr()->eq('t.id', '?1'));
        $qb->setParameter(1, $qrCodeId);

        return $this->getEntityFromPersistence();
    }



    /**
     * @param WeChat $weChat
     * @param string $name
     * @param string $type
     * @param integer $expired
     * @param string $scene
     * @param string $url
     */
    public function createWeChatQrCode($weChat, $name, $type, $expired, $scene, $url)
    {
        $qrCode = new WeChatQrCode();
        $qrCode->setId(Uuid::uuid1()->toString());
        $qrCode->setName($name);
        $qrCode->setType($type);
        $qrCode->setExpired((time() + $expired));
        $qrCode->setScene($scene);
        $qrCode->setUrl($url);
        $qrCode->setCreated(new \DateTime());
        $qrCode->setWechat($weChat);

        $this->saveModifiedEntity($qrCode);
    }



}