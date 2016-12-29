<?php
/**
 *
 *
 * User: leo
 */

namespace Admin\Service\Factory;


use Admin\Service\AdminAuthAdapter;
use Admin\Service\AdminAuthService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;
use Zend\Session\Storage\SessionStorage;

class AdminAuthServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $sessionManager = $serviceManager->get(SessionManager::class);
        $authStorage = new SessionStorage('Admin_Auth', 'adminsess', $sessionManager);
        $authAdapter = $serviceManager->get(AdminAuthAdapter::class);

        return new AdminAuthService($authStorage, $authAdapter);
    }
}