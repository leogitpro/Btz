<?php
/**
 * WeChatAccountController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Exception\RuntimeException as AdminRuntimeException;
use Admin\Form\WeChatForm;
use Admin\Form\WeChatInvoiceForm;
use Admin\Form\WeChatOrderForm;
use WeChat\Entity\Account;
use WeChat\Entity\Invoice;
use WeChat\Entity\Order;
use WeChat\Exception\InvalidArgumentException as WeChatInvalidArgumentException;
use WeChat\Exception\RuntimeException as WeChatRuntimeException;
use WeChat\Service\NetworkService;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


/**
 * 我的公众号
 *
 * Class WeChatAccountController
 * @package Admin\Controller
 */
class WeChatAccountController extends AdminBaseController
{

    /**
     * 我的公众号
     */
    public function indexAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();

        try {
            $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself, true);
        } catch (WeChatInvalidArgumentException $e) {
            $this->getLoggerPlugin()->exception($e);
            $weChat = null;
        }

        return new ViewModel([
            'weChat' => $weChat,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 配置公众号
     */
    public function addAction()
    {
        $wm = $this->getWeChatAccountService();
        $form = new WeChatForm($wm);

        $error = null;

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {

                $data = $form->getData();
                $appId = $data['appid'];
                $appSecret = $data['appsecret'];

                try {

                    $res = NetworkService::getAccessToken($appId, $appSecret);

                    $accessToken = $res['access_token'];
                    $expiredIn = $res['expires_in'] + time() - 300;

                    $wm->createMemberWeChat($this->getMemberManager()->getCurrentMember(), $appId, $appSecret, $accessToken, $expiredIn);

                    return $this->go(
                        '公众号已经创建',
                        '您的微信公众号: ' . $appId . ' 已经创建成功!',
                        $this->url()->fromRoute('admin/weChatAccount')
                    );

                } catch (WeChatInvalidArgumentException $e) {
                    $this->getLoggerPlugin()->exception($e);
                    $error = '无法通过微信平台验证, AppID 和 AppSecret 无效!';
                } catch (WeChatRuntimeException $e) {
                    $this->getLoggerPlugin()->exception($e);
                    $error = '无法通过微信平台验证, AppID 和 AppSecret 无效!';
                }

            }
        }

        return new ViewModel([
            'form' => $form,
            'error' => $error,
            'activeId' => __CLASS__,
        ]);
    }


    /**
     * 修改公众号
     */
    public function editAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();

        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        $form = new WeChatForm($this->getWeChatAccountService(), $weChat);
        $error = null;
        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {

                $data = $form->getData();

                $appId = $weChat->getWxAppId();
                $appSecret = $data['appsecret'];

                try {

                    $res = NetworkService::getAccessToken($appId, $appSecret);

                    $accessToken = $res['access_token'];
                    $expiredIn = $res['expires_in'] + time() - 300;

                    $weChat->setWxAppSecret($appSecret);
                    $weChat->setWxChecked(Account::STATUS_CHECKED);
                    $weChat->setWxAccessToken($accessToken);
                    $weChat->setWxAccessTokenExpired($expiredIn);

                    $this->getWeChatAccountService()->saveModifiedEntity($weChat);

                    return $this->go(
                        '公众号已经修改',
                        '您的微信公众号 ' . $appId . ' 信息已经创建成功!',
                        $this->url()->fromRoute('admin/weChatAccount')
                    );

                } catch (WeChatInvalidArgumentException $e) {
                    $this->getLoggerPlugin()->exception($e);
                    $error = '无法通过微信平台验证, AppID 和 AppSecret 无效!';
                } catch (WeChatRuntimeException $e) {
                    $this->getLoggerPlugin()->exception($e);
                    $error = '无法通过微信平台验证, AppID 和 AppSecret 无效!';
                }
            }
        }

        return new ViewModel([
            'form' => $form,
            'error' => $error,
            'weChat' => $weChat,
            'activeId' => __CLASS__,
        ]);
    }


    /**
     * 手动更新 AccessToken
     */
    public function refreshTokenAction()
    {
        $result = ['success' => false, 'message' => 'Invalid weChat'];
        $myself = $this->getMemberManager()->getCurrentMember();

        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        $appId = $weChat->getWxAppId();
        $appSecret = $weChat->getWxAppSecret();

        $res = NetworkService::getAccessToken($appId, $appSecret);

        $accessToken = $res['access_token'];
        $expiredIn = $res['expires_in'] + time() - 300;

        $weChat->setWxAccessToken($accessToken);
        $weChat->setWxAccessTokenExpired($expiredIn);
        $weChat->setWxChecked(Account::STATUS_CHECKED);

        $this->getWeChatAccountService()->saveModifiedEntity($weChat);

        $result['success'] = true;
        $result['message'] = '已经成功刷新公众号 AccessToken';

        return new JsonModel($result);
    }


    /**
     * 用户标签
     */
    public function tagsAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();

        try {
            $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);
        } catch (WeChatInvalidArgumentException $e) {
            return $this->go(
                '没有配置公众号',
                '未查询到您的公众号信息, 无法继续操作. 您需要先配置您的公众号信息!',
                $this->url()->fromRoute('admin/weChatAccount')
            );
        }

        // Page configuration
        $size = 100;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $count = $this->getWeChatTagService()->getTagsCountByWeChat($weChat);

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/weChatAccount', ['action' => 'tags', 'key' => '%d']));

        // List data
        $rows = $this->getWeChatTagService()->getTagsWithLimitPageByWeChat($weChat, $page, $size);

        return new ViewModel([
            'weChat' => $weChat,
            'tags' => $rows,
            'activeId' => __METHOD__,
        ]);

    }


    /**
     * 同步用户标签
     */
    public function asyncTagsAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => '公众号无效'];

        $myself = $this->getMemberManager()->getCurrentMember();

        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        $insert = 0;
        $tags = $this->getWeChatService()->getTags($weChat);
        if(count($tags)) {
            $insert = $this->getWeChatTagService()->resetTags($tags, $weChat);
        }

        $result['success'] = true;
        $result['message'] = '成功同步用户标签: ' . (int)$insert . ' 条';

        return new JsonModel($result);
    }


    /**
     * 我的订单
     */
    public function orderAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        return new ViewModel([
            'weChat' => $weChat,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 订购服务
     */
    public function addOrderAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        $form = new WeChatOrderForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                //$data = $form->getData();
                //$second = $data['second'];
                $second = 365 * 24 * 3600;
                $money = 12000;

                $this->getWeChatOrderService()->createOrder($weChat, $second, $money);

                return $this->go(
                    '订单已创建',
                    '您的订单已经创建成功, 待支付完成后公众号服务时间会自动延长.',
                    $this->url()->fromRoute('admin/weChatAccount', ['action' => 'order'])
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
     * 已支付通知
     */
    public function paidOrderAction()
    {
        $key = $this->params()->fromRoute('key', '');

        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        $order = $this->getWeChatOrderService()->getWeChatOrderByNo($weChat, $key);

        if ($order->getPaid() == Order::PAID_STATUS_DEFAULT) {
            $order->setPaid(Order::PAID_STATUS_SENT);
            $this->getWeChatOrderService()->saveModifiedEntity($order);
        }

        return $this->go(
            '订单已更新',
            '已经通知财务进行账号核实, 已经核实付款信息, 公众号将服务有效期会自动延长.',
            $this->url()->fromRoute('admin/weChatAccount', ['action' => 'order'])
        );

    }


    /**
     * 发票记录
     */
    public function invoiceAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        $invoices = $weChat->getInvoices();
        $orders = $weChat->getOrders();

        $paiedMoney = 0;
        foreach ($orders as $order) {
            if ($order instanceof Order) {
                if (Order::PAID_STATUS_RECEIVED == $order->getPaid()) {
                    $paiedMoney += $order->getMoney();
                }
            }
        }

        $askedMoney = 0;
        foreach ($invoices as $invoice) {
            if ($invoice instanceof Invoice) {
                if ($invoice->getStatus() != Invoice::STATUS_INVOICE_REFUSED) {
                    $askedMoney += $invoice->getMoney();
                }
            }
        }

        $lastMoney = $paiedMoney - $askedMoney;


        return new ViewModel([
            'weChat' => $weChat,
            'lastMoney' => $lastMoney,
            'invoices' => $invoices,
            'activeId' => __METHOD__,
        ]);
    }

    /**
     * 请求开票
     */
    public function askInvoiceAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        $invoices = $weChat->getInvoices();
        $orders = $weChat->getOrders();

        $paiedMoney = 0;
        foreach ($orders as $order) {
            if ($order instanceof Order) {
                if (Order::PAID_STATUS_RECEIVED == $order->getPaid()) {
                    $paiedMoney += $order->getMoney();
                }
            }
        }

        $askedMoney = 0;
        foreach ($invoices as $invoice) {
            if ($invoice instanceof Invoice) {
                if ($invoice->getStatus() != Invoice::STATUS_INVOICE_REFUSED) {
                    $askedMoney += $invoice->getMoney();
                }
            }
        }

        $lastMoney = $paiedMoney - $askedMoney;
        if ($lastMoney < 1) {
            throw new AdminRuntimeException('已付款的金额已全部被申请开具过发票, 无开票金额可用.');
        }

        $form = new WeChatInvoiceForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                if ($data['money'] > $lastMoney) {
                    return $this->go(
                        '开票请求无效',
                        '您的开票最大为: ' . $lastMoney . '元, 不能申请超过此金额的发票.',
                        $this->url()->fromRoute('admin/weChatAccount', ['action' => 'invoice'])
                    );
                }


                $this->getWeChatInvoiceService()->createInvoice(
                    $weChat,
                    $data['title'],
                    $data['money'],
                    $data['receiver_name'],
                    $data['receiver_phone'],
                    $data['receiver_address'],
                    $data['note']
                );

                return $this->go(
                    '开票请求已收到',
                    '您的开票请求我们已经收到, 请耐心等待. 我们将尽快确认并寄出您的发票.',
                    $this->url()->fromRoute('admin/weChatAccount', ['action' => 'invoice'])
                );
            }
        }

        return new ViewModel([
            'weChat' => $weChat,
            'form' => $form,
            'activeId' => __CLASS__,
        ]);
    }




    /**
     * ACL 注册
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '我的公众号', 'admin/weChatAccount', 1, 'wechat', 22);

        $item['actions']['index'] = self::CreateActionRegistry('index', '我的公众号', 1, 'university', 9);
        $item['actions']['tags'] = self::CreateActionRegistry('tags', '用户标签', 1, 'tags', 8);

        $item['actions']['order'] = self::CreateActionRegistry('order', '我的订单', 1, 'shopping-cart', 6);
        $item['actions']['invoice'] = self::CreateActionRegistry('invoice', '我的发票', 1, 'ticket', 4);

        $item['actions']['add'] = self::CreateActionRegistry('add', '配置公众号');
        $item['actions']['edit'] = self::CreateActionRegistry('edit', '修改公众号');
        $item['actions']['refresh-token'] = self::CreateActionRegistry('refresh-token', '手动更新 AccessToken');

        $item['actions']['add-order'] = self::CreateActionRegistry('add-order', '订购服务');
        $item['actions']['paid-order'] = self::CreateActionRegistry('paid-order', '订单支付完成');

        $item['actions']['ask-invoice'] = self::CreateActionRegistry('ask-invoice', '申请发票');

        $item['actions']['async-tags'] = self::CreateActionRegistry('async-tags', '同步用户标签');

        return $item;
    }


}