<?php
/**
 * Controller Plugin for Logger
 * Quick log tracking in action.
 *
 * User: leo
 */

namespace Application\Controller\Plugin;


use Logger\Service\LoggerService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class LoggerPlugin extends AbstractPlugin
{

    /**
     * @var LoggerService
     */
    private $logger;


    /**
     * LoggerPlugin constructor.
     *
     * @param LoggerService $logger
     */
    public function __construct(LoggerService $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @return LoggerService
     */
    function __invoke()
    {
        return $this->logger;
    }


}