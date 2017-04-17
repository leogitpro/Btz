<?php
/**
 * WeixinController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Api\Controller;


use Api\Exception\InvalidArgumentException;
use Api\Exception\RuntimeException;
use WeChat\Entity\Account;
use WeChat\Entity\Client;
use WeChat\Service\NetworkService;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class WeixinController extends ApiBaseController
{

    /**
     * @var Client
     */
    private $currentClient = null;


    /**
     * @param Account $weChat
     * @param string $identifier
     * @return Client
     */
    private function getCurrentClient(Account $weChat, $identifier)
    {
        if (null == $this->currentClient) {
            $client = $this->getWeChatClientService()->getWeChatClientByWeChatAndIdentifier($weChat, $identifier);
            $nowTime = time();
            if($nowTime > $client->getActiveTime() && $nowTime < $client->getExpireTime()) {
                $this->currentClient = $client;
            } else {
                throw new RuntimeException('客户端: ' . $identifier . ' 已过期!');
            }
        }

        return $this->currentClient;
    }


    /**
     * IP address verify
     *
     * @param Account $weChat
     * @param string $identifier
     * @return bool
     */
    private function ipVerify(Account $weChat, $identifier)
    {
        $ip = $this->getServerPlugin()->ipAddress();
        if (empty($ip)) {
            return false;
        }

        $client = $this->getCurrentClient($weChat, $identifier);
        if (null == $client) {
            return false;
        }

        if ('0.0.0.0' == $client->getIp() || $ip == $client->getIp()) {
            return true;
        }

        return false;
    }


    /**
     * Domain verify
     *
     * @param Account $weChat
     * @param string $identifier
     * @param string $domain
     * @return bool
     */
    private function domainVerify(Account $weChat, $identifier, $domain = '')
    {
        if (empty($domain)) {
            return false;
        }

        $client = $this->getCurrentClient($weChat, $identifier);
        if (null == $client) {
            return false;
        }
        if($domain == $client->getDomain() || 'anonymous.com' == $client->getDomain()) {
            return true;
        }
        return false;
    }


    /**
     * @param Account $weChat
     * @param string $identifier
     * @param string $api
     * @return bool
     */
    private function apiVerify(Account $weChat, $identifier, $api)
    {
        if (empty($api)) {
            return false;
        }

        $client = $this->getCurrentClient($weChat, $identifier);
        if (null == $client) {
            return false;
        }

        $apis = explode(',', $client->getApiList());

        return in_array($api, $apis);
    }



    /**
     * 生成随机字符串
     *
     * @param int $length
     * @return string
     */
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }



    /**
     * 获取公众号 AccessToken
     * 接口访问限制 IP 地址. 需要在后台进行配置.
     *
     * Path: /weixin/accesstoken/wxid/identifier.json
     *
     */
    public function accesstokenAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_JSON);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }
        $identifier = (string)$this->params()->fromRoute('key', '');
        if (empty($identifier)) {
            throw new InvalidArgumentException('Invalid client identifier.');
        }

        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        if (!$this->ipVerify($weChat, $identifier)) {
            throw new RuntimeException('Ip address access disabled!');
        }
        if (!$this->apiVerify($weChat, $identifier, str_replace('Action', '', __FUNCTION__))) {
            throw new RuntimeException('Api access disabled!');
        }

        $data = [
            'success' => true,
            'access_token' => $this->getWeChatService()->getAccessToken($weChat),
        ];

        return new JsonModel($data);
    }


    /**
     * 获取公众号 JsapiTicket
     * 接口访问限制 IP 地址. 需要在后台进行配置.
     *
     * Path: /weixin/jsapiticket/wxid/identifier.json
     */
    public function jsapiticketAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_JSON);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }
        $identifier = (string)$this->params()->fromRoute('key', '');
        if (empty($identifier)) {
            throw new InvalidArgumentException('Invalid client identifier.');
        }

        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        if (!$this->ipVerify($weChat, $identifier)) {
            throw new RuntimeException('Ip address access disabled!');
        }
        if (!$this->apiVerify($weChat, $identifier, str_replace('Action', '', __FUNCTION__))) {
            throw new RuntimeException('Api access disabled!');
        }

        $data = [
            'success' => true,
            'jsapi_ticket' => $this->getWeChatService()->getJsapiTicket($weChat),
        ];

        return new JsonModel($data);
    }


    /**
     * 获取公众号 ApiTicket
     * 接口访问限制 IP 地址. 需要在后台进行配置.
     *
     * Path: /weixin/apiticket/wxid/identifier.json
     */
    public function apiticketAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_JSON);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }
        $identifier = (string)$this->params()->fromRoute('key', '');
        if (empty($identifier)) {
            throw new InvalidArgumentException('Invalid client identifier.');
        }

        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        if (!$this->ipVerify($weChat, $identifier)) {
            throw new RuntimeException('Ip address access disabled!');
        }
        if (!$this->apiVerify($weChat, $identifier, str_replace('Action', '', __FUNCTION__))) {
            throw new RuntimeException('Api access disabled!');
        }

        $data = [
            'success' => true,
            'api_ticket' => $this->getWeChatService()->getCardTicket($weChat),
        ];

        return new JsonModel($data);
    }


    /**
     * 获取粉丝信息. 空信息为非关注状态
     * 接口访问限制 IP 地址. 需要在后台进行配置.
     *
     *
     * Path: /weixin/userinfo/wxid/identifier.json?openid=OPENID
     */
    public function userinfoAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_JSON);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }
        $identifier = (string)$this->params()->fromRoute('key', '');
        if (empty($identifier)) {
            throw new InvalidArgumentException('Invalid client identifier.');
        }

        $openid = (string)$this->params()->fromQuery('openid');
        if(empty($openid)) {
            throw new InvalidArgumentException('Invalid user openid.');
        }

        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        if (!$this->ipVerify($weChat, $identifier)) {
            throw new RuntimeException('Ip address access disabled!');
        }
        if (!$this->apiVerify($weChat, $identifier, str_replace('Action', '', __FUNCTION__))) {
            throw new RuntimeException('Api access disabled!');
        }

        $data = [
            'success' => true,
            'userinfo' => $this->getWeChatService()->getUserInfo($weChat, $openid),
        ];

        return new JsonModel($data);
    }


    /**
     * Jsapi 签名服务
     *
     * Path: /weixin/jssign/wxid/identifier.json?url=urlencode('http://www.example.com/demo.html') => For server api call.
     */
    public function jssignAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_JSON);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }
        $identifier = (string)$this->params()->fromRoute('key', '');
        if (empty($identifier)) {
            throw new InvalidArgumentException('Invalid client identifier.');
        }

        // Server call use query url
        $url = $this->params()->fromQuery('url', '');
        $url = urldecode($url);
        if (empty($url)) {
            throw new InvalidArgumentException('Invalid signature url');
        }
        $urls = parse_url($url);
        if (empty($urls['host'])) {
            throw new InvalidArgumentException('Invalid signature url');
        }

        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        if (!$this->ipVerify($weChat, $identifier)) {
            throw new RuntimeException('Ip address access disabled!');
        }
        if (!$this->domainVerify($weChat, $identifier, $urls['host'])) {
            throw new RuntimeException('Domain not allowed!');
        }
        if (!$this->apiVerify($weChat, $identifier, str_replace('Action', '', __FUNCTION__))) {
            throw new RuntimeException('Api access disabled!');
        }

        // signature params
        $jsapi_ticket = $this->getWeChatService()->getJsapiTicket($weChat);
        $noncestr = $this->createNonceStr();
        $timestamp = time();

        $sha1Source = "jsapi_ticket=$jsapi_ticket&noncestr=$noncestr&timestamp=$timestamp&url=$url";
        $signature = sha1($sha1Source);

        $data = [
            'success' => true,
            'appId' => $weChat->getWxAppId(),
            'timestamp' => $timestamp,
            'nonceStr' => $noncestr,
            'signature' => $signature,
            'rawString' => $sha1Source,
            'url' => $url
        ];

        return new JsonModel($data);
    }



    /**
     * 网页授权接口
     *
     * Path: /weixin/oauth/wxid/identifier.html?type=base|userinfo&url=urlencode('http://www.example.com/demo.html')
     */
    public function oauthAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_HTML);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }
        $identifier = (string)$this->params()->fromRoute('key', '');
        if (empty($identifier)) {
            throw new InvalidArgumentException('Invalid client identifier.');
        }

        $type = (string)$this->params()->fromQuery('type', '');
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
        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        if (!$this->domainVerify($weChat, $identifier, $urls['host'])) {
            throw new RuntimeException('Domain not allowed!');
        }
        if (!$this->apiVerify($weChat, $identifier, str_replace('Action', '', __FUNCTION__))) {
            throw new RuntimeException('Api access disabled!');
        }

        //save url
        $oauthUrl = $this->getWeChatOauthService()->saveOauthUrl($url);
        $state = $oauthUrl->getId();

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

        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        $appId = $weChat->getWxAppId();
        $appSecret = $weChat->getWxAppSecret();

        $res = NetworkService::SnsAccessToken($appId, $appSecret, $code);
        $accessToken = $res['access_token'];
        $openid = $res['openid'];

        $extra = [
            'openid=' . $openid,
        ];

        if('userinfo' == $type) {
            $res = NetworkService::SnsUserInfo($accessToken, $openid);
            $extra[] = 'nickname=' . urlencode($res['nickname']);
            $extra[] = 'sex=' . $res['sex'];
            $extra[] = 'province=' . urlencode($res['province']);
            $extra[] = 'city=' . urlencode($res['city']);
            $extra[] = 'country=' . urlencode($res['country']);
            $extra[] = 'headimgurl=' . urlencode($res['headimgurl']);
        }

        $oauthUrl = $this->getWeChatOauthService()->getOauthUrl($state);
        $goUrl = $oauthUrl->getUrl();

        $goUrlFragment = '';
        $pos = stripos($goUrl, '#');
        if(false !== $pos) {
            $goUrlFragment = substr($goUrl, $pos);
            $goUrl = substr($goUrl, 0, $pos);
        }

        $goUrlQuery = '';
        $pos = stripos($goUrl, '?');
        if(false !== $pos) {
            $goUrlQuery = substr($goUrl, ($pos + 1));
            $goUrl = substr($goUrl, 0, $pos);
        }

        if(!empty($goUrlQuery)) {
            $queries = explode('&', $goUrlQuery);
            foreach($queries as $param) {
                if(!empty($param)) {
                    $extra[] = $param;
                }
            }
        }

        $goUrl .= '?' . implode('&', $extra) . $goUrlFragment;

        $this->redirect()->toUrl($goUrl);
    }



    public function testAction()
    {
        return new ViewModel();
    }

}