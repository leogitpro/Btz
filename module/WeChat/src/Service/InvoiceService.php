<?php
/**
 * InvoiceService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat\Service;


use Ramsey\Uuid\Uuid;
use WeChat\Entity\Account;
use WeChat\Entity\Invoice;
use WeChat\Exception\InvalidArgumentException;


class InvoiceService extends BaseEntityService
{


    /**
     * @return int
     */
    public function getInvoicesCount()
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(Invoice::class, 't');

        return $this->getEntitiesCount();
    }

    /**
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getInvoicesByLimitPage($page = 1, $size = 10)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(Invoice::class, 't');
        $qb->setMaxResults($size)->setFirstResult(($page -1) * $size);
        $qb->orderBy('t.status', 'ASC');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * @param string $id
     * @return Invoice
     * @throws InvalidArgumentException
     */
    public function getInvoice($id)
    {
        $qb = $this->resetQb();

        $qb->select('t');
        $qb->from(Invoice::class, 't');

        $qb->where($qb->expr()->eq('t.id', '?1'));
        $qb->setParameter(1, $id);

        $obj = $this->getEntityFromPersistence();
        if (!$obj instanceof Invoice) {
            throw new InvalidArgumentException('无法获取相关的发票信息');
        }
        return $obj;
    }



    /**
     * @param Account $weChat
     * @param string $title
     * @param integer $money
     * @param string $receiver
     * @param string $phone
     * @param string $address
     * @param string $note
     */
    public function createInvoice($weChat, $title, $money, $receiver, $phone, $address, $note)
    {
        $entity = new Invoice();

        $entity->setId(Uuid::uuid1()->toString());
        $entity->setTitle($title);
        $entity->setWeChat($weChat);
        $entity->setMoney($money);
        $entity->setReceiver($receiver);
        $entity->setPhone($phone);
        $entity->setAddress($address);
        $entity->setNote($note);
        $entity->setCreated(new \DateTime());

        $entity->setStatus(Invoice::STATUS_INVOICE_APPLY);

        $this->saveModifiedEntity($entity);
    }

}