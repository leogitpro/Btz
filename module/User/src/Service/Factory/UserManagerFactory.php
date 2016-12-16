<?php
/**
 * The user service manager factory
 *
 * User: leo
 */

namespace User\Service\Factory;


use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use User\Service\UserManager;


class UserManagerFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $sm service manager
     * @param string $requestedName
     * @param array|null $options
     */
    public function __invoke(ContainerInterface $sm, $requestedName, array $options = null)
    {
        $entityManager = $sm->get('doctrine.entitymanager.orm_default');
        return new UserManager($entityManager);
    }

}