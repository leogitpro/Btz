<?php
/**
 * WeChatController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Form\WeChatForm;
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
     * Controller and actions registry
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '微信公众号', 'admin/weChat', 1, 'wechat', 22);

        $item['actions']['index'] = self::CreateActionRegistry('index', '我的公众号', 1, 'university', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '创建公众号');

        return $item;
    }



}