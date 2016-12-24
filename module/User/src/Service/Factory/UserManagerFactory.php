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

    public function __invoke(ContainerInterface $sm, $requestedName, array $options = null)
    {
        $entityManager = $sm->get('doctrine.entitymanager.orm_default');
        $logger = $sm->get('Logger');

        return new UserManager($entityManager, $logger);
    }

}