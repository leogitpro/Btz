<?php
/**
 * WeChatClientController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Form\WeChatClientForm;
use Zend\View\Model\ViewModel;


/**
 * 公众号客户端管理
 *
 * Class WeChatClientController
 * @package Admin\Controller
 */
class WeChatClientController extends AdminBaseController
{

    /**
     * 客户端列表
     */
    public function indexAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $count = $this->getWeChatClientService()->getClientCountByWeChat($weChat);

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/weChatClient', ['action' => 'index', 'key' => '%d']));

        // List data
        $rows = $this->getWeChatClientService()->getClientsWithLimitPageByWeChat($weChat, $page, $size);

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
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        $form = new WeChatClientForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {

                $data = $form->getData();

                $start = new \DateTime($data['active']);
                $activeTime = $start->format('U') + 1;

                $end = new \DateTime($data['expire']);
                $end->modify("+1 day");
                $expireTime = $end->format('U') - 1;

                $this->getWeChatClientService()->createWeChatClient(
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
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 删除客户端
     */
    public function deleteAction()
    {
        $clientId = (string)$this->params()->fromRoute('key');

        $client = $this->getWeChatClientService()->getWeChatClient($clientId);

        $myself = $this->getMemberManager()->getCurrentMember();
        if ($myself->getMemberId() != $client->getWechat()->getMember()->getMemberId()) {
            throw new \Exception('厉害了我的哥, 你这是删除别人的客户端配置啊, 要逆天了!');
        }

        $name = $client->getName();

        $this->getWeChatClientService()->removeEntity($client);

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
        $item = self::CreateControllerRegistry(__CLASS__, '公众号客户端', 'admin/weChatClient', 1, 'laptop', 21);

        $item['actions']['index'] = self::CreateActionRegistry('index', '客户端列表', 1, 'bars', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '添加客户端', 1, 'plus', 8);
        $item['actions']['delete'] = self::CreateActionRegistry('delete', '删除客户端');

        return $item;
    }



}