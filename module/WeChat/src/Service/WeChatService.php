<?php
/**
 * WeChatService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace WeChat\Service;



use WeChat\Entity\Account;
use WeChat\Exception\InvalidArgumentException;
use WeChat\Exception\RuntimeException;


class WeChatService
{

    /**
     * @var AccountService
     */
    private $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }


    /**
     * 提取公众号 AccessToken
     *
     * @param int|Account $account
     * @return string
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function getAccessToken($account)
    {
        if (!$account instanceof Account) {
            $account = (int)$account;
            $account = $this->accountService->getWeChat($account);
        }

        if (time() > $account->getWxExpired()) {
            throw new RuntimeException('公众号服务已经过期, AppID:' . $account->getWxAppId());
        }

        if (time() > $account->getWxAccessTokenExpired()) {

            $res = NetworkService::getAccessToken($account->getWxAppId(), $account->getWxAppSecret());
            $token = $res['access_token'];
            $expires = $res['expires_in'] + time() - 300;

            $account->setWxAccessToken($token);
            $account->setWxAccessTokenExpired($expires);

            $this->accountService->saveModifiedEntity($account);

            return $token;
        } else {
            return $account->getWxAccessToken();
        }
    }


    /**
     * 提取公众号 Jsapi Ticket
     *
     * @param int|Account $account
     * @return string
     */
    public function getJsapiTicket($account)
    {
        if (!$account instanceof Account) {
            $account = (int)$account;
            $account = $this->accountService->getWeChat($account);
        }

        $accessToken = $this->getAccessToken($account);

        if (time() > $account->getWxJsapiTicketExpired()) {
            $res = NetworkService::getJsapiTicket($accessToken);

            $ticket = $res['ticket'];
            $expires = $res['expires_in'] + time() - 300;

            $account->setWxJsapiTicket($ticket);
            $account->setWxJsapiTicketExpired($expires);

            $this->accountService->saveModifiedEntity($account);

            return $ticket;
        } else {
            return $account->getWxJsapiTicket();
        }
    }


    /**
     * 提取公众号 Card Ticket
     *
     * @param int|Account $account
     * @return string
     */
    public function getCardTicket($account)
    {
        if (!$account instanceof Account) {
            $account = (int)$account;
            $account = $this->accountService->getWeChat($account);
        }

        $accessToken = $this->getAccessToken($account);

        if (time() > $account->getWxCardTicketExpired()) {
            $res = NetworkService::getCardTicket($accessToken);

            $ticket = $res['ticket'];
            $expires = $res['expires_in'] + time() - 300;

            $account->setWxCardTicket($ticket);
            $account->setWxCardTicketExpired($expires);

            $this->accountService->saveModifiedEntity($account);

            return $ticket;
        } else {
            return $account->getWxCardTicket();
        }
    }



    /**
     * 读取用户个人资料
     *
     * @param Account $account
     * @param string $openid
     * @return array
     */
    public function getUserInfo(Account $account, $openid)
    {
        $token = $this->getAccessToken($account);

        return NetworkService::userInfo($token, $openid);
    }




    /**
     * 读取公众号 用户标签
     *
     * @param Account $account
     * @return array
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function getTags(Account $account)
    {
        $token = $this->getAccessToken($account);

        return NetworkService::userTags($token);
    }


    /**
     * 创建公众号二维码
     *
     * @param Account $account
     * @param string $type
     * @param string $scene
     * @param int $expired
     * @return array
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function qrCodeCreate(Account $account, $type, $scene, $expired)
    {
        $token = $this->getAccessToken($account);

        return NetworkService::QrCodeCreate($token, $type, $scene, $expired);
    }


    /**
     * 删除公众号菜单
     *
     * @param Account $account
     * @return true
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function menuRemoveDefault(Account $account)
    {
        $token = $this->getAccessToken($account);
        return NetworkService::menuRemoveDefault($token);
    }

    /**
     * 删除个性化菜单
     *
     * @param Account $account
     * @param $menuid
     * @return true
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function menuRemoveConditional(Account $account, $menuid)
    {
        $token = $this->getAccessToken($account);
        return NetworkService::menuRemoveConditional($token, $menuid);
    }

    /**
     * 导出公众号菜单
     *
     * @param Account $account
     * @return array
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function menuExport(Account $account)
    {
        $token = $this->getAccessToken($account);
        return NetworkService::menuExport($token);
    }

    /**
     * 创建微信自定义菜单
     *
     * @param Account $account
     * @param $menu
     * @return true
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function menuCreateDefault(Account $account, $menu)
    {
        $token = $this->getAccessToken($account);
        return NetworkService::menuCreateDefault($token, $menu);
    }

    /**
     * 创建微信个性化菜单
     *
     * @param Account $account
     * @param $menu
     * @return string 菜单编号
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function menuCreateConditional(Account $account, $menu)
    {
        $token = $this->getAccessToken($account);
        return NetworkService::menuCreateConditional($token, $menu);
    }


}