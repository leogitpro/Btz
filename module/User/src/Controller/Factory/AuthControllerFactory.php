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
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $serviceManager, $controllerName, array $options = null)
    {
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
        $authManager = $serviceManager->get(AuthManager::class);
        $authService = $serviceManager->get(\Zend\Authentication\AuthenticationService::class);
        $userManager = $serviceManager->get(UserManager::class);

        return new AuthController($entityManager, $authManager, $authService, $userManager);
    }


}