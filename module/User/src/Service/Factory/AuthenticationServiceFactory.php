<?php
/**
 * Custom  inject dependency authentication service factory
 *
 * User: leo
 */

namespace User\Service\Factory;


use Interop\Container\ContainerInterface;
use User\Service\AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session as SessionStorage;



class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionManager = $container->get(SessionManager::class);
        $authStorage = new SessionStorage('User_Auth', 'usersess', $sessionManager);
        $authAdapter = $container->get(AuthAdapter::class);

        //Create the service and inject dependencies into its constructor
        return new AuthenticationService($authStorage, $authAdapter);
    }

}