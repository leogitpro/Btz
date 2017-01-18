<?php
/**
 * Member manager factory
 *
 * User: leo
 */

namespace Admin\Service\Factory;


use Admin\Service\DMRelationManager;
use Admin\Service\MemberManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;


class MemberManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        // Get entity manager
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

        // Get logger support
        $logger = $serviceManager->get('Logger');

        $dmrManager = $serviceManager->get(DMRelationManager::class);

        return new MemberManager($dmrManager, $entityManager, $logger);
    }


}