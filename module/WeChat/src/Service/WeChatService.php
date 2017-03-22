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
     * @param Account $account
     * @return string
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function getAccessToken(Account $account)
    {
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