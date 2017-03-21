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

}