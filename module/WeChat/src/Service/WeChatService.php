<?php
/**
 * WeChatService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace WeChat\Service;




class WeChatService
{

    private $accountService;


    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }



    public function getAccessToken($wxId)
    {

    }


    //todo

}