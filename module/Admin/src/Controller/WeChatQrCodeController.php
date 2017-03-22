<?php
/**
 * WeChatQrCodeController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Form\WeChatQrCodeForm;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;
use WeChat\Entity\QrCode;
use Zend\View\Model\ViewModel;


class WeChatQrCodeController extends AdminBaseController
{

    /**
     * 二维码列表
     */
    public function indexAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $count = $this->getWeChatQrCodeService()->getQrCodeCountByWeChat($weChat);

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/weChatQrCode', ['action' => 'index', 'key' => '%d']));

        // List data
        $rows = $this->getWeChatQrCodeService()->getQrCodesWithLimitPageByWeChat($weChat, $page, $size);

        return new ViewModel([
            'weChat' => $weChat,
            'qrCodes' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 申请二维码
     */
    public function addAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        $form = new WeChatQrCodeForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $name = $data['name']; //二维码名称
                $type = array_key_exists($data['type'], QrCode::getTypeList()) ? $data['type'] : QrCode::TYPE_TEMP;

                if (QrCode::TYPE_TEMP == $type) {
                    $scene = intval($data['scene']);
                } else {
                    if(strlen($data['scene']) > 6 && strlen($data['scene']) <= 64) {
                        $type = 'QR_LIMIT_STR_SCENE';
                        $scene = (string)$data['scene'];
                    } else {
                        if (strlen($data['scene']) < 6) {
                            if (preg_match("/^[0-9]+$/", $data['scene'])) {
                                $type = 'QR_LIMIT_SCENE';
                                $scene = intval($data['scene']);
                                if ($scene > 100000) {
                                    $scene = 100000;
                                }
                            } else {
                                $type = 'QR_LIMIT_STR_SCENE';
                                $scene = (string)$data['scene'];
                            }
                        }
                    }
                }

                if (empty($scene)) {
                    throw new \Exception('二维码参数不合适, 无法进行申请!');
                }

                if (QrCode::TYPE_TEMP != $type) {
                    $expired = 0;
                } else {
                    $expired = (int)$data['expired'];
                    if ($expired < 30) { $expired = 30; }
                }

                $res = $this->getWeChatService()->qrCodeCreate($weChat, $type, $scene, $expired);

                $this->getWeChatQrCodeService()->createWeChatQrCode($weChat, $name, $type, $expired, $scene, $res['url']);

                $title = '二维码创建成功';
                $message = '您申请的二维码已经创建成功!';

                return $this->go($title, $message, $this->url()->fromRoute('admin/weChatQrCode'));
            }
        }

        return new ViewModel([
            'form' => $form,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 删除二维码
     */
    public function deleteAction()
    {
        $qrCodeId = (string)$this->params()->fromRoute('key');

        $qrCode = $this->getWeChatQrCodeService()->getWeChatQrCode($qrCodeId);

        $myself = $this->getMemberManager()->getCurrentMember();
        if ($myself->getMemberId() != $qrCode->getWechat()->getMember()->getMemberId()) {
            throw new \Exception('厉害了我的哥, 你这是删除别人的二维码啊, 要逆天了!');
        }

        $name = $qrCode->getName();

        $this->getWeChatQrCodeService()->removeEntity($qrCode);

        return $this->go(
            '二维码已经删除',
            '您的微信公众号二维码 ' . $name . ' 已经删除! ',
            $this->url()->fromRoute('admin/weChatQrCode')
        );
    }


    /**
     * 制作二维码
     */
    public function makeAction()
    {
        $qrCodeId = (string)$this->params()->fromRoute('key');
        $qrCode = $this->getWeChatQrCodeService()->getWeChatQrCode($qrCodeId);

        $myself = $this->getMemberManager()->getCurrentMember();
        if ($myself->getMemberId() != $qrCode->getWechat()->getMember()->getMemberId()) {
            throw new \Exception('厉害了我的哥, 这是别人的二维码啊!');
        }

        return new ViewModel([
            'qrCode' => $qrCode,
            'activeId' => __CLASS__,
        ]);
    }


    /**
     * 下载二维码
     */
    public function downloadAction()
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
                    $margin = 2;
                    if ($margin > $size / 2) {
                        $margin = 2;
                    }
                }

                if(!preg_match("/^[0-9A-Fa-f]{6}$/", $color)) {
                    $color = '000000';
                }
                list($colorR, $colorG, $colorB) = array_map('hexdec', str_split($color, 2));

                if(!preg_match("/^[0-9A-Fa-f]{6}$/", $bgcolor)) {
                    $bgcolor = 'FFFFFF';
                }
                list($bgcolorR, $bgcolorG, $bgcolorB) = array_map('hexdec', str_split($bgcolor, 2));

                if(!in_array($error, ['L', 'M', 'Q', 'H'])) {
                    $error = 'H';
                }

                $qrCodeMaker = new BaconQrCodeGenerator();
                $qrCodeMaker->format($type); //二维码格式
                $qrCodeMaker->size($size); //二维码尺寸
                $qrCodeMaker->margin($margin); //二维码边距
                $qrCodeMaker->color($colorR, $colorG, $colorB); //二维码颜色
                $qrCodeMaker->backgroundColor($bgcolorR, $bgcolorG, $bgcolorB); //二维码背景颜色
                $qrCodeMaker->encoding('UTF-8'); //二维码内容编码
                $qrCodeMaker->errorCorrection($error); //二维码容错设置

                // Lots of memory request. shutdown the error display
                ini_set('display_errors', false);
                error_reporting(0);

                // Register shutdown callback
                register_shutdown_function(function () {
                    $error = error_get_last();
                    if(null !== $error) {
                        //echo '<pre>'; print_r($error); echo '</pre>';
                        if (E_ERROR == $error['type']) {
                            if(preg_match("/^Allowed memory/", $error['message'])) {
                                echo '系统无法支持制作您设定的超大尺寸二维码. ' .  intval(memory_get_peak_usage(false)/1024/1024) . 'M' ;
                            } else {
                                echo 'System error!';
                            }
                        }
                    }
                });

                $data = $qrCodeMaker->generate($url);

                $response = $this->getResponse();
                $headers = $response->getHeaders();
                $response->setContent($data);
                $headers->addHeaderLine('Content-Type', $mimes[$type]);
                $headers->addHeaderLine('Content-Disposition', 'attachment; filename="qrCode.' . $type . '"');
                $headers->addHeaderLine('Content-Length', strlen($data));

                return $response;
            }
        }

        return $this->getResponse();
    }


    /**
     *  ACL 登记
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '公众号二维码', 'admin/weChatQrCode', 1, 'qrcode', 20);

        $item['actions']['index'] = self::CreateActionRegistry('index', '二维码列表', 1, 'bars', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '申请二维码', 1, 'plus', 8);
        $item['actions']['delete'] = self::CreateActionRegistry('delete', '删除二维码');
        $item['actions']['make'] = self::CreateActionRegistry('make', '制作二维码');
        $item['actions']['download'] = self::CreateActionRegistry('download', '下载二维码');

        return $item;
    }


}