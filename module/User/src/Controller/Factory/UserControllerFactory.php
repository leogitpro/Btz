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
        $userManager = $sm->get(UserManager::class);

        return new $controllerName($userManager);
    }

}