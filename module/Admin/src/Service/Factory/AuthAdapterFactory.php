<?php
/**
 * Administrator authentication adapter factory
 * Inject objects to adapter
 *
 * User: leo
 */

namespace Admin\Service\Factory;


use Admin\Service\AuthAdapter;
use Admin\Service\MemberManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $memberManager = $serviceManager->get(MemberManager::class);

        return new AuthAdapter($memberManager);
    }


}