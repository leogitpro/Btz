<?php
/**
 * WeChatClientController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Entity\WeChat;
use Admin\Entity\WeChatClient;
use Admin\Form\WeChatClientForm;
use Zend\View\Model\ViewModel;


class WeChatClientController extends AdminBaseController
{

    /**
     * 客户端列表
     */
    public function indexAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();

        $weChat = $this->getWeChatManager()->getWeChatByMember($myself);

        if (!$weChat instanceof WeChat) {
            return $this->go(
                '没有配置公众号',
                '未查询到您的公众号信息, 无法继续操作. 您需要先配置您的公众号信息!',
                $this->url()->fromRoute('admin/weChat')
            );
        }

        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $wcm = $this->getWeChatClientManager();

        $count = $wcm->getClientCountByWeChat($weChat);

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/weChatClient', ['action' => 'index', 'key' => '%d']));

        // List data
        $rows = $wcm->getClientsWithLimitPageByWeChat($weChat, $page, $size);

        return new ViewModel([
            'weChat' => $weChat,
            'clients' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 添加客户端
     */
    public function addAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();

        $weChat = $this->getWeChatManager()->getWeChatByMember($myself);

        if (!$weChat instanceof WeChat) {
            return $this->go(
                '没有配置公众号',
                '未查询到您的公众号信息, 无法继续操作. 您需要先配置您的公众号信息!',
                $this->url()->fromRoute('admin/weChat')
            );
        }

        $form = new WeChatClientForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {

                $data = $form->getData();

                $start = new \DateTime($data['active']);
                $activeTime = $start->format('U');

                $end = new \DateTime($data['expire']);
                $end->modify("+1 day");
                $expireTime = $end->format('U') - 1;

                $this->getWeChatClientManager()->createWeChatClient(
                    $weChat,
                    $data['name'],
                    $data['domain'],
                    $data['ip'],
                    $activeTime,
                    $expireTime
                );

                return $this->go(
                    '客户端已经创建',
                    '您的微信公众号访问客户端已经创建成功!',
                    $this->url()->fromRoute('admin/weChatClient')
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'activeId' => __CLASS__,
        ]);
    }


    /**
     * 删除客户端
     */
    public function deleteAction()
    {
        $clientId = (string)$this->params()->fromRoute('key');

        $client = $this->getWeChatClientManager()->getWeChatClient($clientId);
        if (!$client instanceof WeChatClient) {
            throw new \Exception('无法查询到此客户端信息!');
        }

        $myself = $this->getMemberManager()->getCurrentMember();
        if ($myself->getMemberId() != $client->getWechat()->getMember()->getMemberId()) {
            throw new \Exception('厉害了我的哥, 你这是删除别人的客户端配置啊, 要逆天了!');
        }

        $name = $client->getName();

        $this->getWeChatClientManager()->removeEntity($client);

        return $this->go(
            '客户端信息已经删除',
            '您的微信公众号访问客户端 ' . $name . ' 已经删除! 相关访问已经被禁止.',
            $this->url()->fromRoute('admin/weChatClient')
        );
    }



    /**
     *  ACL 登记
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '公众号客户端', 'admin/weChatClient', 1, 'laptop', 22);

        $item['actions']['index'] = self::CreateActionRegistry('index', '客户端列表', 1, 'bars', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '添加客户端', 1, 'plus', 8);
        $item['actions']['delete'] = self::CreateActionRegistry('delete', '删除客户端');

        return $item;
    }



}