<?php
/**
 * WechatController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Api\Controller;



use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;

class WechatController extends ApiBaseController
{


    public function indexAction()
    {
        //todo
    }

    /**
     * 发起微信网页授权
     */
    public function oauthAction()
    {
        $wxId = (int)$this->params()->fromRoute('key', 0);

        $redirectUri = urldecode($this->params()->fromQuery('redirect_uri'));
        $urlParams = parse_url($redirectUri);
        $host = @$urlParams['host'];

        $this->getLoggerPlugin()->info('RedirectURI:' . $redirectUri);

        //echo __METHOD__;

        if (empty($host)) {
            $this->getLoggerPlugin()->err(__METHOD__ . ": No redirectURI for oauth");
            //return $this->getResponse();
        }


        $domain = $this->getServerPlugin()->domain();

        $path = $this->url()->fromRoute('wxapi', ['action' => 'oauthed', 'key' => $wxId, 'suffix' => '.html']);


        $url = $domain . $path;
        $this->getLoggerPlugin()->info('url: ' . $url);

        return $this->redirect()->toUrl($url);



        //$wechatManager = $this->getWechatManager();
        //$wechat = $this->

        //return $this->sendHtmlResponse(__METHOD__);

    }

    /**
     * 响应微信的网页授权回调
     */
    public function oauthedAction()
    {
        return $this->sendHtmlResponse('oked');
    }

    public function qrcodeAction()
    {

        $qrcode = new BaconQrCodeGenerator();

        $img = $qrcode->format('png')->size(400)->generate('http://www.baidu.com');

        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('content-type', 'image/png');
        $response->setContent($img);
        return $response;
    }

}