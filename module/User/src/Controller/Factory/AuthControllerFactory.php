<?php
/**
 * Auth controller factory
 *
 * User: leo
 */

namespace User\Controller\Factory;


use Interop\Container\ContainerInterface;
use User\Controller\AuthController;
use User\Service\AuthManager;
use User\Service\UserManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceManager
     * @param string $controllerName
     * @param array|null|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $serviceManager, $controllerName, array $options = null)
    {
        $authManager = $serviceManager->get(AuthManager::class);
        $authService = $serviceManager->get(\Zend\Authentication\AuthenticationService::class);
        $userManager = $serviceManager->get(UserManager::class);

        return new AuthController($authManager, $authService, $userManager);
    }


}