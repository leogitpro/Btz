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
use Zend\Http\Header\Referer;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class WeixinController extends ApiBaseController
{

    /**
     * IP address verify
     *
     * @param Account $account
     * @return bool
     */
    private function ipVerify(Account $account)
    {
        $ip = $this->getServerPlugin()->ipAddress();
        if (empty($ip)) {
            return false;
        }

        $clients = $account->getClients();
        $allowedIps = [];
        $currentTime  = time();
        $unlimited = false;
        foreach ($clients as $client) {
            if ($client instanceof Client) {
                if ($currentTime > $client->getActiveTime() && $currentTime < $client->getExpireTime()) {
                    $allowedIp = $client->getIp();
                    if ('0.0.0.0' == $allowedIp) {
                        $unlimited = true;
                        break;
                    }
                    $allowedIps[$allowedIp] = $allowedIp;
                }
            }
        }
        if ($unlimited) {
            return true;
        }

        return in_array($ip, $allowedIps);
    }


    /**
     * Domain verify
     *
     * @param Account $account
     * @param string $domain
     * @return bool
     */
    private function domainVerify(Account $account, $domain = '')
    {
        if (empty($domain)) {
            return false;
        }

        $clients = $account->getClients();
        $allowedDomains = [];
        $currentTime  = time();
        $unlimited = false;
        foreach ($clients as $client) {
            if ($client instanceof Client) {
                if ($currentTime > $client->getActiveTime() && $currentTime < $client->getExpireTime()) {
                    $allowedDomain = $client->getDomain();
                    if (preg_match("/anonymous\\.com/", $allowedDomain)) {
                        $unlimited = true;
                        break;
                    }
                    $allowedDomains[$allowedDomain] = $allowedDomain;
                }
            }
        }
        if ($unlimited) {
            return true;
        }

        return in_array($domain, $allowedDomains);
    }


    /**
     * Verify ip and domain
     *
     * @param Account $weChat
     * @param string $domain
     * @return bool
     */
    private function ipAndDomainVerify(Account $weChat, $domain = '')
    {
        if (empty($domain)) {
            return false;
        }

        $ip = $this->getServerPlugin()->ipAddress();
        if (empty($ip)) {
            return false;
        }

        $clients = $weChat->getClients();

        $allowedDomains = [];
        $allowedIps = [];

        $currentTime  = time();

        $ipUnlimited = false;
        $domainUnlimited = false;

        foreach ($clients as $client) {
            if ($client instanceof Client) {
                if ($currentTime > $client->getActiveTime() && $currentTime < $client->getExpireTime()) {

                    $allowedDomain = $client->getDomain();
                    $allowedIp = $client->getIp();

                    if (preg_match("/anonymous\\.com/", $allowedDomain)) {
                        $domainUnlimited = true;
                    }
                    if ('0.0.0.0' == $allowedIp) {
                        $ipUnlimited = true;
                    }

                    $allowedDomains[$allowedDomain] = $allowedDomain;
                    $allowedIps[$allowedIp] = $allowedIp;
                }
            }
        }
        if ($ipUnlimited && $domainUnlimited) {
            return true;
        }

        return in_array($ip, $allowedIps) && in_array($domain, $allowedDomains);
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
     * Path: /weixin/accesstoken/wxid.json
     *
     */
    public function accesstokenAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_JSON);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }

        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        if (!$this->ipVerify($weChat)) {
            throw new RuntimeException('Access disabled!');
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
     * Path: /weixin/jsapiticket/wxid.json
     */
    public function jsapiticketAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_JSON);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }

        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        if (!$this->ipVerify($weChat)) {
            throw new RuntimeException('Access disabled!');
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
     * Path: /weixin/apiticket/wxid.json
     */
    public function apiticketAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_JSON);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }

        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        if (!$this->ipVerify($weChat)) {
            throw new RuntimeException('Access disabled!');
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
     * Path: /weixin/userinfo/wxid/openid.json
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

        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        if (!$this->ipVerify($weChat)) {
            throw new RuntimeException('Access disabled!');
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
     * //Path: /weixin/jssign/wxid.json => For client ajax call. to be removed
     * Path: /weixin/jssign/wxid.json?url=urlencode('http://www.example.com/demo.html') => For server api call.
     */
    public function jssignAction()
    {
        $this->declareResponseContentType(self::MEDIA_TYPE_JSON);

        $wxid = (int)$this->params()->fromRoute('wxid', 0);
        if (!$wxid) {
            throw new InvalidArgumentException('Invalid weChat service number.');
        }

        // Ajax call use referer sign
        /**
        $request = $this->getRequest();
        if(!$request instanceof Request) {
            throw new InvalidArgumentException('Invalid access request');
        }

        $referer = $request->getHeaders()->get('Referer');
        if (!$referer instanceof Referer) {
            throw new InvalidArgumentException('Invalid request Referer');
        }

        $url = $referer->getFieldValue();
        $pos = stripos($url, '#');
        if(false !== $pos) {
            $url = substr($url, 0, $pos);
        }

        $urlParams = parse_url($url);
        if (empty($urlParams['host'])) {
            throw new InvalidArgumentException('Invalid Referer Domain');
        }

        // Domain validate
        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        if (!$this->domainVerify($weChat, $urlParams['host'])) {
            throw new RuntimeException('Access disabled!');
        }
        //*/

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
        if (!$this->ipAndDomainVerify($weChat, $urls['host'])) {
            throw new RuntimeException('Access disabled!');
        }

        // signature params
        $jsapi_ticket = $this->getWeChatService()->getJsapiTicket($weChat);
        $noncestr = $this->createNonceStr();
        $timestamp = time();

        $sha1Source = "jsapi_ticket=$jsapi_ticket&noncestr=$noncestr&timestamp=$timestamp&url=$url";
        $signature = sha1($sha1Source);

        $data = [
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
     * Path: /weixin/oauth/wxid/base|userinfo.html?url=urlencode('http://www.example.com/demo.html')
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
        $weChat = $this->getWeChatAccountService()->getWeChat($wxid);
        if ($this->domainVerify($weChat)) {
            throw new RuntimeException('Access disabled!');
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
        echo sys_get_temp_dir();
        $this->getLoggerPlugin()->info(sys_get_temp_dir());
        return new ViewModel();
    }

}