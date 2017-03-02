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
        $myself = $this->getMemberManager()->getCurrentMember();

        $wechatManager = $this->getWechatManager();
        $wechat = $wechatManager->getWechatByMember($myself);
        if (!$wechat instanceof Wechat) {
            throw new \Exception('未查询到您的公众号信息, 无法继续操作. 您需要先配置您的公众号信息!');
        }

        return new ViewModel([
            'wechat' => $wechat,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 添加一个二维码
     */
    public function qrcodeaddAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();

        $wechatManager = $this->getWechatManager();
        $wechat = $wechatManager->getWechatByMember($myself);

        if (!$wechat instanceof Wechat) {
            return $this->go('公众号还未创建', '您的微信公众号还未创建, 不能执行当前的操作!', $this->url()->fromRoute('admin/wechat'));
        }

        $form = new WechatQrcodeForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $name = $data['name']; //二维码名称
                $type = array_key_exists($data['type'], WechatQrcode::getTypeList()) ? $data['type'] : WechatQrcode::TYPE_TEMP;

                if (preg_match("/^[0-9]+$/", $data['scene'])) {
                    $scene = intval($data['scene']);
                } else {
                    $scene = (string)$data['scene'];
                }
                if (empty($scene)) {
                    throw new \Exception('二维码参数不合适, 无法进行申请!');
                }

                if (WechatQrcode::TYPE_FOREVER == $type) {
                    $expired = 0;
                } else {
                    $expired = (int)$data['expired'];
                    if ($expired < 30) { $expired = 30; }
                }

                $wechatService = $this->getWechatService($wechat->getWxId());
                $result = $wechatService->getQrCode($type, $scene, $expired);

                if (!empty($result) && isset($result['url'])) {
                    $wechatManager->createWechatQrcode($wechat, $name, $type, $expired, $scene, $result['url']);
                    $title = '二维码创建成功';
                    $message = '您申请的二维码已经创建成功!';
                } else {
                    $title = '二维码创建失败';
                    $message = '您申请的二维码已经创建失败, 请重新再试!';
                }

                return $this->go($title, $message, $this->url()->fromRoute('admin/wechat', ['action' => 'qrcodelist']));
            }
        }

        return new ViewModel([
            'form' => $form,
            'activeId' => __CLASS__,
        ]);
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
        if($this->getRequest()->isPost()) {

            $value = $this->params()->fromPost('qr_value', '');
            $type = $this->params()->fromPost('qr_type', '');
            $size = (int)$this->params()->fromPost('qr_size', '');
            $margin = (int)$this->params()->fromPost('qr_margin', '');
            $color = $this->params()->fromPost('qr_color', '');
            $bgcolor = $this->params()->fromPost('qr_bgcolor', '');
            $error = strtoupper($this->params()->fromPost('qr_error', ''));

            $mimes = [
                'png' => 'image/png',
                'eps' => 'application/postscript',
                'svg' => 'image/svg+xml',
            ];

            if (!empty($value)) {
                $url = urldecode($value);
                if (!array_key_exists($type, $mimes)) {
                    $type = 'png';
                }
                if (empty($size)) {
                    $size = 400;
                }
                if (empty($margin)) {
                    $margin = 20;
                    if ($margin > $size / 2) {
                        $margin = intval($size * 0.1);
                    }
                }
                if(!preg_match("/^[0-9A-F]{6}$/", $color)) {
                    $color = '000000';
                }
                list($colorR, $colorG, $colorB) = array_map('hexdec', str_split($color, 2));

                if(!preg_match("/^[0-9A-F]{6}$/", $bgcolor)) {
                    $bgcolor = 'FFFFFF';
                }
                list($bgcolorR, $bgcolorG, $bgcolorB) = array_map('hexdec', str_split($bgcolor, 2));

                if(!in_array($error, ['L', 'M', 'Q', 'H'])) {
                    $error = 'H';
                }

                $qrcodeMaker = new BaconQrCodeGenerator();
                $qrcodeMaker->format($type); //二维码格式
                $qrcodeMaker->size($size); //二维码尺寸
                $qrcodeMaker->margin($margin); //二维码边距
                $qrcodeMaker->color($colorR, $colorG, $colorB); //二维码颜色
                $qrcodeMaker->backgroundColor($bgcolorR, $bgcolorG, $bgcolorB); //二维码背景颜色
                $qrcodeMaker->encoding('UTF-8'); //二维码内容编码
                $qrcodeMaker->errorCorrection($error); //二维码容错设置

                $data = $qrcodeMaker->generate($url);

                $response = $this->getResponse();
                $headers = $response->getHeaders();
                $headers->addHeaderLine('content-type', $mimes[$type]);
                $response->setContent($data);
                return $response;
            }
        }

        return $this->getResponse();
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