<?php
/**
 * WeChatAccountController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Entity\WeChat;
use Admin\Form\WeChatForm;
use WeChat\Entity\Account;
use WeChat\Exception\InvalidArgumentException;
use WeChat\Exception\RuntimeException;
use WeChat\Service\NetworkService;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class WeChatAccountController extends AdminBaseController
{

    /**
     * 当前用户的公众号
     */
    public function indexAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();

        try {
            $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);
        } catch (InvalidArgumentException $e) {
            $this->getLoggerPlugin()->exception($e);
            $weChat = null;
        }

        return new ViewModel([
            'weChat' => $weChat,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 登记当前用户的公众号
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

                } catch (InvalidArgumentException $e) {
                    $this->getLoggerPlugin()->exception($e);
                    $error = '无法通过微信平台验证, AppID 和 AppSecret 无效!';
                } catch (RuntimeException $e) {
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
     * 编辑当前用户的公众号
     */
    public function editAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();

        $wm = $this->getWeChatAccountService();

        $weChat = $wm->getWeChatByMember($myself);
        if (!$weChat instanceof Account) {
            throw new \Exception('这个公众号已经失效了好像! 查无此人!');
        }

        $form = new WeChatForm($wm, $weChat);
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

                    $wm->saveModifiedEntity($weChat);

                    return $this->go(
                        '公众号已经修改',
                        '您的微信公众号 ' . $appId . ' 信息已经创建成功!',
                        $this->url()->fromRoute('admin/weChatAccount')
                    );

                } catch (InvalidArgumentException $e) {
                    $this->getLoggerPlugin()->exception($e);
                    $error = '无法通过微信平台验证, AppID 和 AppSecret 无效!';
                } catch (RuntimeException $e) {
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
     * 刷新 AccessToken
     *
     * @return JsonModel
     * @throws \Exception
     */
    public function refreshTokenAction()
    {
        $result = ['success' => false, 'message' => 'Invalid weChat'];
        $myself = $this->getMemberManager()->getCurrentMember();

        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);
        if (!$weChat instanceof Account) {
            throw new \Exception('这个公众号已经失效了好像! 查无此人!');
        }

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
     * Tag 列表
     */
    public function tagsAction()
    {

        $myself = $this->getMemberManager()->getCurrentMember();

        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        if (!$weChat instanceof Account) {
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
        if (!$weChat instanceof Account) {
            throw new \Exception('这个公众号已经失效了好像! 查无此人!');
        }

        $insert = 0;

        try {
            $tags = $this->getWeChatService()->getTags($weChat);
            if(count($tags)) {
                $insert = $this->getWeChatTagService()->resetTags($tags, $weChat);
            }
        } catch (InvalidArgumentException $e) {
            throw new \Exception($e->getMessage());
        } catch (RuntimeException $e) {
            throw new \Exception($e->getMessage());
        }

        $result['success'] = true;
        $result['message'] = '成功同步用户标签: ' . (int)$insert . ' 条';

        return new JsonModel($result);
    }



    /**
     * ACL 注册
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '微信公众号', 'admin/weChatAccount', 1, 'wechat', 22);

        $item['actions']['index'] = self::CreateActionRegistry('index', '我的公众号', 1, 'university', 9);

        $item['actions']['tags'] = self::CreateActionRegistry('tags', '用户标签', 1, 'tags', 8);

        $item['actions']['add'] = self::CreateActionRegistry('add', '创建公众号');
        $item['actions']['edit'] = self::CreateActionRegistry('edit', '编辑公众号');
        $item['actions']['refresh-token'] = self::CreateActionRegistry('refresh-token', '刷新AccessToken');

        $item['actions']['async-tags'] = self::CreateActionRegistry('async-tags', '同步用户标签');

        return $item;
    }


}