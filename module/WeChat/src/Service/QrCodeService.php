<?php
/**
 * QrCodeService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat\Service;


use Ramsey\Uuid\Uuid;
use WeChat\Entity\Account;
use WeChat\Entity\QrCode;
use WeChat\Exception\InvalidArgumentException;


class QrCodeService extends BaseEntityService
{

    /**
     * @param string $qrCodeId
     * @return QrCode
     */
    public function getWeChatQrCode($qrCodeId)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(QrCode::class, 't');
        $qb->where($qb->expr()->eq('t.id', '?1'));
        $qb->setParameter(1, $qrCodeId);

        $qrCode = $this->getEntityFromPersistence();
        if (!$qrCode instanceof QrCode) {
            throw new InvalidArgumentException("无效的二维码编号: " . $qrCodeId);
        }
        return $qrCode;
    }


    /**
     * @param Account $weChat
     * @return int
     */
    public function getQrCodeCountByWeChat($weChat)
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(QrCode::class, 't');

        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);

        return $this->getEntitiesCount();
    }


    /**
     * @param Account $weChat
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getQrCodesWithLimitPageByWeChat($weChat, $page = 1, $size = 10)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(QrCode::class, 't');

        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);

        $qb->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $qb->orderBy('t.expired', 'DESC');

        return $this->getEntitiesFromPersistence();
    }



    /**
     * @param Account $weChat
     * @param string $name
     * @param string $type
     * @param integer $expired
     * @param string $scene
     * @param string $url
     */
    public function createWeChatQrCode($weChat, $name, $type, $expired, $scene, $url)
    {
        $qrCode = new QrCode();
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