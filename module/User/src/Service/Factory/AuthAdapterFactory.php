<?php
/**
 * Custom inject dependency authentication adapter factory
 *
 * User: leo
 */

namespace User\Service\Factory;


use Interop\Container\ContainerInterface;
use User\Service\AuthAdapter;
use User\Service\UserManager;
use Zend\ServiceManager\Factory\FactoryInterface;


class AuthAdapterFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $serviceManager
     * @param string $requestedName
     * @param array|null|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        //Create the AuthAdapter and inject dependency to its constructor
        return new AuthAdapter($serviceManager->get(UserManager::class));
    }

}