<?php
/**
 * WeChatService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace WeChat\Service;


use Logger\Service\LoggerService;


class WeChatService
{

    /**
     * @var LoggerService
     */
    private $loggerService;


    public function __construct(LoggerService $loggerService)
    {
        $this->loggerService = $loggerService;
    }



    public function getAccessToken($wxId)
    {
        //todo
    }

}