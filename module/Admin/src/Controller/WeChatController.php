<?php
/**
 * WeChatController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Entity\WeChat;
use Admin\Form\WeChatForm;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;



class WeChatController extends AdminBaseController
{

    /**
     * Current member weChat public account detail
     */
    public function indexAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();

        $wm = $this->getWeChatManager();

        $weChat = $wm->getWeChatByMember($myself);

        return new ViewModel([
            'weChat' => $weChat,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * Add a weChat public account
     */
    public function addAction()
    {
        $wm = $this->getWeChatManager();
        $form = new WeChatForm($wm);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {

                $data = $form->getData();
                $appid = $data['appid'];
                $appsecret = $data['appsecret'];

                $wm->createMemberWeChat($this->getMemberManager()->getCurrentMember(), $appid, $appsecret);

                return $this->go(
                    '公众号已经创建',
                    '您的微信公众号: ' . $appid . ' 已经创建成功!',
                    $this->url()->fromRoute('admin/weChat')
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'activeId' => __CLASS__,
        ]);
    }


    /**
     * Edit the weChat public account info
     */
    public function editAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();
        $wm = $this->getWeChatManager();

        $weChat = $wm->getWeChatByMember($myself);
        if (!$weChat instanceof WeChat) {
            throw new \Exception('这个公众号已经失效了好像! 查无此人!');
        }

        $form = new WeChatForm($wm, $weChat);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {

                $data = $form->getData();

                if ($weChat->getWxChecked() != WeChat::STATUS_CHECKED) {
                    $appid = $data['appid'];
                    $weChat->setWxAppId($data['appid']);
                } else {
                    $appid = $weChat->getWxAppId();
                }

                $weChat->setWxAppSecret($data['appsecret']);
                $wm->saveModifiedEntity($weChat);

                return $this->go(
                    '公众号已经修改',
                    '您的微信公众号 ' . $appid . ' 信息已经创建成功!',
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
     * Validate weChat
     */
    public function validateAction()
    {
        $result = ['success' => false, 'message' => '', 'hosts' => []];

        $myself = $this->getMemberManager()->getCurrentMember();

        $wm = $this->getWeChatManager();
        $weChat = $wm->getWeChatByMember($myself);

        if (WeChat::STATUS_CHECKED == $weChat->getWxChecked()) {
            $result['success'] = true;
            return new JsonModel($result);
        }

        if (!$weChat instanceof WeChat) {
            return new JsonModel($result);
        }

        $ws = $this->getWeChatService($weChat->getWxId());
        $hosts = $ws->getCallbackHosts();

        if(!empty($hosts)) {
            $weChat->setWxChecked(WeChat::STATUS_CHECKED);
            $wm->saveModifiedEntity($weChat);
            $result['success'] = true;
            $result['hosts'] = $hosts;
        }

        return new JsonModel($result);
    }


    /**
     * Controller and actions registry
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '微信公众号', 'admin/weChat', 1, 'wechat', 22);

        $item['actions']['index'] = self::CreateActionRegistry('index', '我的公众号', 1, 'university', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '创建公众号');
        $item['actions']['edit'] = self::CreateActionRegistry('edit', '编辑公众号');
        $item['actions']['validate'] = self::CreateActionRegistry('validate', '验证公众号');

        return $item;
    }



}