<?php
/**
 *
 *
 * User: leo
 */

namespace Admin\Service\Factory;


use Admin\Service\AuthAdapter;
use Admin\Service\AuthService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session as SessionStorage;


class AuthServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $sessionManager = $serviceManager->get(SessionManager::class);
        $authStorage = new SessionStorage('Admin_Auth', 'adminsess', $sessionManager);
        $authAdapter = $serviceManager->get(AuthAdapter::class);

        return new AuthService($authStorage, $authAdapter);
    }
}