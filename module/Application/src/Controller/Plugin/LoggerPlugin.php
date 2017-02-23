<?php
/**
 * Controller Plugin for Logger
 * Quick log tracking in action.
 *
 * User: leo
 */

namespace Application\Controller\Plugin;


use Application\Service\AppLogger;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class LoggerPlugin extends AbstractPlugin
{

    /**
     * @var AppLogger
     */
    private $logger;


    /**
     * LoggerPlugin constructor.
     *
     * @param AppLogger $logger
     */
    public function __construct(AppLogger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @return AppLogger
     */
    function __invoke()
    {
        return $this->logger;
    }


}