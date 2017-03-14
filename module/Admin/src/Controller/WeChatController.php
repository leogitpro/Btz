<?php
/**
 * WeChatController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Zend\View\Model\ViewModel;



class WeChatController extends AdminBaseController
{

    /**
     * 所有公众号清单
     */
    public function indexAction()
    {
        // Page configuration
        $size = 100;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $wm = $this->getWeChatManager();

        $count = $wm->getWeChatCount();

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/weChat', ['action' => 'index', 'key' => '%d']));

        // List data
        $rows = $wm->getWeChatLimitByPage($page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     *  ACL 注册
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '公众号管理', 'admin/weChat', 1, 'wechat', 22);

        $item['actions']['index'] = self::CreateActionRegistry('index', '公众号列表', 1, 'bars', 9);

        return $item;
    }



}