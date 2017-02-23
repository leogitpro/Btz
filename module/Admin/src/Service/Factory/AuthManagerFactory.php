<?php
/**
 * Factory for auth manager
 *
 * User: leo
 */


namespace Admin\Service\Factory;


use Admin\Service\AuthManager;
use Admin\Service\AuthService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;


class AuthManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $authService = $serviceManager->get(AuthService::class);
        $sessionManager = $serviceManager->get(SessionManager::class);
        $logger = $serviceManager->get('Logger');

        return new AuthManager($authService, $sessionManager, $logger);
    }

}