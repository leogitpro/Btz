<?php
/**
 * BaseManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace WeChat\Service;


use Logger\Service\LoggerService;


class BaseManager
{

    /**
     * @var LoggerService
     */
    protected $logger;

    public function __construct(LoggerService $logger)
    {
        $this->logger = $logger;
    }


}