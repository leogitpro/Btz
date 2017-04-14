<?php
/**
 * WeixinController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Api\Controller;


use Api\Exception\InvalidArgumentException;
use WeChat\Service\NetworkService;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class WeixinController extends ApiBaseController
{

    /**
     * 获取公众号 AccessToken
     *
     * Path: /weixin/token/wxid
     *
     */
    public function tokenAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_JSON);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }

        $weChatService = $this->getWeChatService();

        $data = [
            'success' => true,
            'access_token' => $weChatService->getAccessToken($wxid),
        ];

        return new JsonModel($data);
    }


    /**
     * 获取粉丝信息. 空信息为非关注状态
     *
     * Path: /weixin/userinfo/wxid/openid
     */
    public function userinfoAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_JSON);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }

        $openid = (string)$this->params()->fromRoute('key');
        if(empty($openid)) {
            throw new InvalidArgumentException('Invalid user openid.');
        }

        $weChatService = $this->getWeChatService();

        $data = [
            'success' => true,
            'userinfo' => $weChatService->getUserInfo($wxid, $openid),
        ];

        return new JsonModel($data);
    }


    /**
     * 网页授权接口
     *
     * Path: /weixin/oauth/wxid.html?url=urlencode('http://www.example.com/demo.html')
     */
    public function oauthAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_HTML);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }

        $type = (string)$this->params()->fromRoute('key', '');
        if(!in_array($type, ['base', 'userinfo'])) {
            throw new InvalidArgumentException('Invalid request.');
        }

        $url = $this->params()->fromQuery('url', '');
        $url = urldecode($url);
        $urls = parse_url($url);
        if (empty($urls['scheme']) || empty($urls['host'])) {
            throw new InvalidArgumentException('The callback url format invalid[' . $url . ']');
        }

        // Domain validate

        //save url
        $oauthUrl = $this->getWeChatOauthService()->saveOauthUrl($url);
        $state = $oauthUrl->getId();

        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        $appId = $weChat->getWxAppId();

        $scope = 'snsapi_' . $type;

        $wxCallbackUrl = $this->url()->fromRoute('weixin', [
            'action' => 'oauthed',
            'wxid' => $wxid,
            'key' => $type,
            'suffix' => '.html'
        ]);
        $redirectUri = $this->getServerPlugin()->domain() . $wxCallbackUrl;

        $goUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
        $goUrl .= 'appid=' . $appId;
        $goUrl .= '&redirect_uri=' . urlencode($redirectUri);
        $goUrl .= '&response_type=code';
        $goUrl .= '&scope=' . $scope;
        $goUrl .= '&state=' . $state;
        $goUrl .= '#wechat_redirect';

        $this->redirect()->toUrl($goUrl);
    }


    /**
     * 微信回调接口
     */
    public function oauthedAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_HTML);

        $code = $this->params()->fromQuery('code', '');
        $state = $this->params()->fromQuery('state', '');

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }

        $type = (string)$this->params()->fromRoute('key', '');
        if(!in_array($type, ['base', 'userinfo'])) {
            throw new InvalidArgumentException('Invalid request.');
        }

        $oauthUrl = $this->getWeChatOauthService()->getOauthUrl($state);

        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        $appId = $weChat->getWxAppId();
        $appSecret = $weChat->getWxAppSecret();

        $res = NetworkService::SnsAccessToken($appId, $appSecret, $code);
        $accessToken = $res['access_token'];
        $openid = $res['openid'];

        if('userinfo' == $type) {
            $res = NetworkService::SnsUserInfo($accessToken, $openid);
        }


    }



    public function testAction()
    {
        return new ViewModel();
    }

}