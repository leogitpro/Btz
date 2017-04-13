<?php
/**
 * WeChatController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Exception\InvalidArgumentException;
use Admin\Form\WeChatExpiredForm;
use WeChat\Entity\Invoice;
use WeChat\Entity\Order;
use Zend\View\Model\ViewModel;


/**
 * 微信公众号管理
 *
 * Class WeChatController
 * @package Admin\Controller
 */
class WeChatController extends AdminBaseController
{

    /**
     * 公众号列表
     */
    public function indexAction()
    {
        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $count = $this->getWeChatAccountService()->getWeChatCount();

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/weChat', ['action' => 'index', 'key' => '%d']));

        // List data
        $rows = $this->getWeChatAccountService()->getWeChatLimitByPage($page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 设置过期时间
     */
    public function expiredAction()
    {
        $weChadId = (int)$this->params()->fromRoute('key', 0);
        if (!$weChadId) {
            throw new InvalidArgumentException('微信 ID 为空, 不能继续操作!');
        }

        $weChat = $this->getWeChatAccountService()->getWeChat($weChadId, true);

        $form = new WeChatExpiredForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {

                $data = $form->getData();

                $expired = new \DateTime($data['expired']);

                $weChat->setWxExpired($expired->format('U') + 24 * 3600 - 1);
                $this->getWeChatAccountService()->saveModifiedEntity($weChat);

                return $this->go(
                    '公众号已更新',
                    '微信公众号: ' . $weChat->getWxAppId() . ' 的过期时间已调整为: ' . $expired->format('Y-m-d'),
                    $this->url()->fromRoute('admin/weChat')
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'weChat' => $weChat,
            'activeId' => __CLASS__,
        ]);
    }


    /**
     * 订单列表
     */
    public function orderAction()
    {
        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $count = $this->getWeChatOrderService()->getOrdersCount();

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/weChat', ['action' => 'order', 'key' => '%d']));

        // List data
        $rows = $this->getWeChatOrderService()->getOrdersByLimitPage($page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 确认订单付款
     */
    public function confirmOrderPaidAction()
    {
        $orderId = $this->params()->fromRoute('key', '');

        $order = $this->getWeChatOrderService()->getOrder($orderId);

        if($this->getRequest()->isPost()) {
            $second = $order->getSecond();
            $order->setPaid(Order::PAID_STATUS_RECEIVED);

            $weChat = $order->getWeChat();
            $weChatExpired = $weChat->getWxExpired();
            $weChat->setWxExpired(($weChatExpired + $second));

            $member = $order->getWeChat()->getMember();
            $memberExpired = $member->getMemberExpired()->format('U');
            $date = new \DateTime();
            $date->setTimestamp(($memberExpired + $second));
            $member->setMemberExpired($date);

            $this->getWeChatOrderService()->saveModifiedEntities([$order, $weChat, $member]);

            return $this->go(
                '订单付款已确认',
                '订单号: ' . $order-> getNo() . ' 已确认收款',
                $this->url()->fromRoute('admin/weChat', ['action' => 'order'])
            );
        }

        return new ViewModel([
            'order' => $order,
            'activeId' => __CLASS__,
        ]);

    }


    /**
     * 发票列表
     */
    public function invoiceAction()
    {
        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $count = $this->getWeChatInvoiceService()->getInvoicesCount();

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/weChat', ['action' => 'invoice', 'key' => '%d']));

        // List data
        $rows = $this->getWeChatInvoiceService()->getInvoicesByLimitPage($page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 更新发票信息
     */
    public function updateInvoiceAction()
    {
        $invoiceId = $this->params()->fromRoute('key', '');

        $invoice = $this->getWeChatInvoiceService()->getInvoice($invoiceId);

        if($this->getRequest()->isPost()) {

            $status = (int)$this->params()->fromPost('status');
            $note = $this->params()->fromPost('note');

            if(!in_array($status, [
                Invoice::STATUS_INVOICE_PRINT,
                Invoice::STATUS_INVOICE_DELIVER,
                Invoice::STATUS_INVOICE_REFUSED
            ])) {
                return $this->go(
                    '发票信息更新失败',
                    '未收到有效的发票状态更新信息, 无法更新发票状态.',
                    $this->url()->fromRoute('admin/weChat', ['action' => 'invoice'])
                );
            }

            $note = strip_tags($note);
            if(!empty($note)) {
                $new_note = $invoice->getNote() . PHP_EOL . '[' . $note . ']';
                $invoice->setNote($new_note);
            }
            $invoice->setStatus($status);

            $this->getWeChatInvoiceService()->saveModifiedEntity($invoice);

            return $this->go(
                '发票信息已更新',
                '发票信息已经更新成功!',
                $this->url()->fromRoute('admin/weChat', ['action' => 'invoice'])
            );
        }

        return new ViewModel([
            'invoice' => $invoice,
            'activeId' => __CLASS__,
        ]);

    }



    /**
     *  ACL 注册
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '微信服务管理', 'admin/weChat', 1, 'wechat', 22);

        $item['actions']['index'] = self::CreateActionRegistry('index', '公众号管理', 1, 'bars', 9);
        $item['actions']['order'] = self::CreateActionRegistry('order', '订单管理', 1, 'shopping-cart', 8);
        $item['actions']['invoice'] = self::CreateActionRegistry('invoice', '发票管理', 1, 'ticket', 7);

        $item['actions']['confirm-order-paid'] = self::CreateActionRegistry('confirm-order-paid', '确认订单付款');
        $item['actions']['update-invoice'] = self::CreateActionRegistry('update-invoice', '更新发票状态');
        $item['actions']['expired'] = self::CreateActionRegistry('expired', '设置过期时间');

        return $item;
    }



}