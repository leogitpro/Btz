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

    public function __invoke(ContainerInterface $serviceManager, $controllerName, array $options = null)
    {
        $userManager = $serviceManager->get(UserManager::class);
        $authService = $serviceManager->get(\Zend\Authentication\AuthenticationService::class);

        return new $controllerName($userManager, $authService);
    }

}