<?php
/**
 * OrderService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat\Service;


use Ramsey\Uuid\Uuid;
use WeChat\Entity\Account;
use WeChat\Entity\Order;
use WeChat\Exception\InvalidArgumentException;


class OrderService extends BaseEntityService
{


    /**
     * @param Account $weChat
     * @param string $no
     * @return Order
     * @throws InvalidArgumentException
     */
    public function getWeChatOrderByNo($weChat, $no)
    {
        $qb = $this->resetQb();

        $qb->select('t');
        $qb->from(Order::class, 't');

        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('t.weChat', '?1'),
            $qb->expr()->eq('t.no', '?2')
        ));
        $qb->setParameter(1, $weChat)->setParameter(2, $no);

        $obj = $this->getEntityFromPersistence();
        if (!$obj instanceof Order) {
            throw new InvalidArgumentException('无法获取相关的订单信息');
        }
        return $obj;
    }



    /**
     * @param Account $weChat
     * @param int $second
     * @param int $money
     * @return Order
     */
    public function createOrder($weChat, $second, $money)
    {
        $order = new Order();
        $order->setId(Uuid::uuid1()->toString());
        $order->setWeChat($weChat);
        $order->setNo($this->createOrderNo(1));
        $order->setMoney($money);
        $order->setSecond($second);
        $order->setPaid(Order::PAID_STATUS_DEFAULT);
        $order->setCreated(new \DateTime());

        $this->saveModifiedEntity($order);

        return $order;
    }


    /**
     * @param int $type
     * @return string
     */
    private function createOrderNo($type = 1)
    {
        return date('Ymd') . mt_rand(11111, 99999) . (int)$type;
    }
}