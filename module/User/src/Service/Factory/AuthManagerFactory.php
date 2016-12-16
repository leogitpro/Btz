<?php
/**
 * AuthManagerFactory
 *
 * User: leo
 */

namespace User\Service\Factory;


use Interop\Container\ContainerInterface;
use User\Service\AuthManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;

class AuthManagerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authenticationService = $container->get(\Zend\Authentication\AuthenticationService::class);
        $sessionManager = $container->get(SessionManager::class);

        $config = $container->get('Config');
        if(isset($config['access_filter'])) {
            $config = $config['access_filter'];
        } else {
            $config = [];
        }

        // Instantiate the AuthManager service and inject dependencies to its constructor.
        return new AuthManager($authenticationService, $sessionManager, $config);
    }


}