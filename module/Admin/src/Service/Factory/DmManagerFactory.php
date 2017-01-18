<?php
/**
 * Member and department manager factory
 */

namespace Admin\Service\Factory;


use Admin\Service\DMRelationManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class DmManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $logger = $container->get('Logger');
        $dmrManager = $container->get(DMRelationManager::class);

        return new $requestedName($dmrManager, $entityManager, $logger);
    }

}