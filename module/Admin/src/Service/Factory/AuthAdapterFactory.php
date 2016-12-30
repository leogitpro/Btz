<?php
/**
 * Administrator authentication adapter factory
 * Inject objects to adapter
 *
 * User: leo
 */

namespace Admin\Service\Factory;


use Admin\Service\AdminerManager;
use Admin\Service\AuthAdapter;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $adminerManager = $serviceManager->get(AdminerManager::class);

        return new AuthAdapter($adminerManager);
    }


}