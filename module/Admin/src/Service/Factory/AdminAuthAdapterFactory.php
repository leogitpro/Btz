<?php
/**
 * Administrator authentication adapter factory
 * Inject objects to adapter
 *
 * User: leo
 */

namespace Admin\Service\Factory;


use Admin\Service\AdminAuthAdapter;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AdminAuthAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        // Get the entity manger inject to the adapter
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

        return new AdminAuthAdapter($entityManager);
    }


}