<?php
/**
 * UserController Factory
 *
 * User: leo
 */

namespace User\Controller\Factory;


use User\Service\UserManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;



class UserControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $sm, $controllerName, array $options = null)
    {
        $entityManager = $sm->get('doctrine.entitymanager.orm_default');
        $userManager = $sm->get(UserManager::class);

        return new $controllerName($entityManager, $userManager);
    }

}