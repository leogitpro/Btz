<?php
/**
 * WechatController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Entity\Wechat;
use Admin\Form\WechatForm;
use Admin\Wechat\Service;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;



class WechatController extends AdminBaseController
{

    /**
     * @return Service
     */
    private function getWechatService($wxId)
    {
        return $this->buildSm(Service::class, ['wx_id' => $wxId]);
    }

    /**
     * Myself wechat configuration
     */
    public function indexAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();

        $wechatManager = $this->getWechatManager();
        $wechat = $wechatManager->getWechatByMember($myself);
        if (!$wechat instanceof Wechat) {
            return new ViewModel([
                'wechat' => null,
                'activeId' => __METHOD__,
            ]);
        }

        //$wechatService = $this->getWechatService($wechat->getWxId());

        //var_dump($wechatService->getAccessToken());



        return new ViewModel([
            'wechat' => $wechat,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * Create wechat
     */
    public function addAction()
    {
        $wechatManager = $this->getWechatManager();
        $form = new WechatForm($wechatManager);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {

                $data = $form->getData();
                $appid = $data['appid'];
                $appsecret = $data['appsecret'];

                $wechatManager->createMemberWechat($this->getMemberManager()->getCurrentMember(), $appid, $appsecret);

                return $this->getMessagePlugin()->show(
                    '公众号已经创建',
                    '您的微信公众号: ' . $appid . ' 已经创建成功!',
                    $this->url()->fromRoute('admin/wechat'),
                    '返回',
                    3
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'activeId' => __CLASS__,
        ]);
    }


    public function checkAction()
    {
        $result = ['success' => false, 'message' => '', 'hosts' => []];

        $myself = $this->getMemberManager()->getCurrentMember();

        $wechatManager = $this->getWechatManager();
        $wechat = $wechatManager->getWechatByMember($myself);

        if (Wechat::STATUS_CHECKED == $wechat->getWxChecked()) {
            $result['success'] = true;
            return new JsonModel($result);
        }

        if (!$wechat instanceof Wechat) {
            return new JsonModel($result);
        }

        $wechatService = $this->getWechatService($wechat->getWxId());
        $wxHosts = $wechatService->getCallbackHosts();
        if(!empty($wxHosts)) {
            $wechat->setWxChecked(Wechat::STATUS_CHECKED);
            $wechatManager->saveModifiedEntity($wechat);
        }

        $result['success'] = true;
        $result['hosts'] = $wxHosts;
        return new JsonModel($result);
    }


    public function clientAction()
    {
        //todo
    }


    /**
     * Controller and actions registry
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '微信服务', 'admin/wechat', 1, 'wechat', 22);

        $item['actions']['index'] = self::CreateActionRegistry('index', '公众号管理', 1, 'university', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '创建公众号');
        $item['actions']['check'] = self::CreateActionRegistry('check', '验证 AppId');

        return $item;
    }




}