<?php
/**
 * Common entity manager factory
 * Inject entityManager and logger dependencies
 *
 * User: leo
 */

namespace Admin\Service\Factory;


use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;


class EntityManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        // Get entity manager
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

        // Get logger support
        $logger = $serviceManager->get('Logger');

        return new $requestedName($entityManager, $logger);
    }


}