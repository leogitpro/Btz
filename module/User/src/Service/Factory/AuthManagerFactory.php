<?php
/**
 * User authentication manager factory
 *
 * User: leo
 */

namespace User\Service\Factory;


use Interop\Container\ContainerInterface;
use User\Service\AuthManager;
use User\Service\AuthService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;

class AuthManagerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $authService = $serviceManager->get(AuthService::class);
        $sessionManager = $serviceManager->get(SessionManager::class);
        $logger = $serviceManager->get('Logger');

        $config = $serviceManager->get('Config');
        if(isset($config['access_filter'])) {
            $config = $config['access_filter'];
        } else {
            $config = [];
        }

        // Instantiate the AuthManager service and inject dependencies to its constructor.
        return new AuthManager($authService, $sessionManager, $logger, $config);
    }


}