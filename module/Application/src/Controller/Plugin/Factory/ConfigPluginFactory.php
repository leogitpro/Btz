<?php
/**
 * Quickly access config plugin factory
 *
 * User: leo
 */

namespace Application\Controller\Plugin\Factory;


use Application\Controller\Plugin\ConfigPlugin;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ConfigPluginFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {

        $config = $serviceManager->get('Config');

        return new ConfigPlugin($config);

    }

}