<?php
/**
 * Department with member relationship manager factory
 *
 * User: leo
 */

namespace Admin\Service\Factory;


use Admin\Service\DepartmentManager;
use Admin\Service\DepartmentMemberRelationManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class DMRelationManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        // Get entity manager
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
;
        // Get logger support
        $logger = $serviceManager->get('Logger');

        // Get department manager
        $departmentManager = $serviceManager->get(DepartmentManager::class);

        return new DepartmentMemberRelationManager($entityManager, $logger, $departmentManager);
    }


}