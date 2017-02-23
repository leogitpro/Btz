<?php
/**
 * AclManagerFactory.php
 *
 * Factory for acl manager
 *
 * User: leo
 */

namespace Admin\Service\Factory;


use Admin\Service\AclManager;
use Admin\Service\ComponentManager;
use Admin\Service\MemberManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;


class AclManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $logger = $container->get('Logger');
        $memberManager = $container->get(MemberManager::class);
        $componentManager = $container->get(ComponentManager::class);

        return new AclManager($memberManager, $componentManager, $entityManager, $logger);

    }

}