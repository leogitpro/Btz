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


class InvoiceService extends BaseEntityService
{


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