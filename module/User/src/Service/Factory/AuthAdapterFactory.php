<?php
/**
 * Custom inject dependency authadapter factory
 *
 * User: leo
 */

namespace User\Service\Factory;


use Interop\Container\ContainerInterface;
use User\Service\AuthAdapter;
use Zend\ServiceManager\Factory\FactoryInterface;


class AuthAdapterFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

        //Create the AuthAdapter and inject dependency to its constructor
        return new AuthAdapter($entityManager);
    }

}