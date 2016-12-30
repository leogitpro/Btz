<?php
/**
 * User authentication service factory
 *
 * User: leo
 */

namespace User\Service\Factory;


use Interop\Container\ContainerInterface;
use User\Service\AuthAdapter;
use User\Service\AuthService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session as SessionStorage;

class AuthServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $sessionManager = $serviceManager->get(SessionManager::class);
        $authStorage = new SessionStorage('User_Auth', 'identity', $sessionManager);
        $authAdapter = $serviceManager->get(AuthAdapter::class);

        return new AuthService($authStorage, $authAdapter);
    }
}