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
        $size = 100;
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
     *  ACL 注册
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '微信公众号管理', 'admin/weChat', 1, 'wechat', 22);

        $item['actions']['index'] = self::CreateActionRegistry('index', '公众号列表', 1, 'bars', 9);

        $item['actions']['expired'] = self::CreateActionRegistry('expired', '设置过期时间');

        return $item;
    }



}