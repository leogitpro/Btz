<?php
/**
 * Controller Plugin for Logger
 * Quick log tracking in action.
 *
 * User: leo
 */

namespace Application\Controller\Plugin;


use Zend\Log\Logger;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class LoggerPlugin extends AbstractPlugin
{

    /**
     * @var Logger
     */
    private $logger;


    /**
     * LoggerPlugin constructor.
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @return Logger
     */
    function __invoke()
    {
        return $this->logger;
    }


}