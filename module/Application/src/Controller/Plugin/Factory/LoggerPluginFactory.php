<?php
/**
 * LoggerPluginFactory
 *
 * User: leo
 */

namespace Application\Controller\Plugin\Factory;


use Application\Controller\Plugin\LoggerPlugin;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class LoggerPluginFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {

        return new LoggerPlugin($serviceManager->get('Logger'));

    }


}