<?php
/**
 * WechatController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Entity\Wechat;
use Admin\Entity\WechatClient;
use Admin\Entity\WechatQrcode;
use Admin\Form\WechatClientForm;
use Admin\Form\WechatForm;
use Admin\Form\WechatQrcodeForm;
use Admin\Wechat\Service;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;



class WechatsController extends AdminBaseController
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
    }


    /**
     * Create wechat
     */
    public function addAction()
    {
    }

    /**
     * Edit wechat
     */
    public function editAction()
    {
    }


    /**
     * Test can get wechat server ip list
     *
     * @return JsonModel
     */
    public function checkAction()
    {

    }



    /**
     * 客户端清单
     */
    public function clientAction()
    {
    }




    /**
     * 添加一个 Client
     */
    public function addclientAction()
    {

    }


    /**
     * 修改 Client
     */
    public function editclientAction()
    {
    }


    /**
     * 删除客户端
     */
    public function removeAction()
    {

    }




    public function qrcodelistAction()
    {
    }


    /**
     * 添加一个二维码
     */
    public function qrcodeaddAction()
    {
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    public function qrcodedownAction()
    {

        $qrcodeId = (string)$this->params()->fromRoute('key');

        $wechatManager = $this->getWechatManager();
        $wechatQrcode = $wechatManager->getWechatQrcode($qrcodeId);
        if (!$wechatQrcode instanceof WechatQrcode) {
            throw new \Exception('无法查询到此二维码信息!');
        }

        $myself = $this->getMemberManager()->getCurrentMember();
        if ($myself->getMemberId() != $wechatQrcode->getWechat()->getMember()->getMemberId()) {
            throw new \Exception('厉害了我的哥, 你怎么去拿别人的二维码了?');
        }

        return new ViewModel([
            'qrcode' => $wechatQrcode,
            'activeId' => __CLASS__ . '::qrcodelistAction',
        ]);
    }


    /**
     * 生成二维码
     */
    public function qrcodemakeAction()
    {
    }



    /**
     * Controller and actions registry
     *
     * @return array
     */
    public static function ComponentRegistryX()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '微信服务', 'admin/wechat', 1, 'wechat', 22);

        $item['actions']['index'] = self::CreateActionRegistry('index', '公众号管理', 1, 'university', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '创建公众号');
        $item['actions']['check'] = self::CreateActionRegistry('check', '验证 AppId');

        $item['actions']['client'] = self::CreateActionRegistry('client', '公众号客户端', 1, 'laptop', 8);
        $item['actions']['addclient'] = self::CreateActionRegistry('addclient', '添加访问客户端');


        $item['actions']['qrcodelist'] = self::CreateActionRegistry('qrcodelist', '二维码管理', 1, 'qrcode', 6);
        $item['actions']['qrcodeadd'] = self::CreateActionRegistry('qrcodeadd', '增加二维码');

        return $item;
    }




}