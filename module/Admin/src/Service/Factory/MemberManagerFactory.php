<?php
/**
 * MemberManagerFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Service\Factory;


use Admin\Service\AuthService;
use Admin\Service\DMRelationManager;
use Admin\Service\MemberManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MemberManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $logger = $container->get('Logger');
        $dmrManager = $container->get(DMRelationManager::class);
        $authService = $container->get(AuthService::class);

        return new MemberManager($authService, $dmrManager, $entityManager, $logger);
    }
}