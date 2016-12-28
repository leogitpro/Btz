<?php
/**
 * Default common entity manager factory
 *
 * User: leo
 */

namespace Application\Service\Factory;


use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class EntityManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
        $logger = $serviceManager->get('Logger');

        return new $requestedName($entityManager, $logger);
    }


}