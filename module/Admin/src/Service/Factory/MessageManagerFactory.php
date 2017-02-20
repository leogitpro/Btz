<?php
/**
 * MessageManagerFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Service\Factory;


use Admin\Service\DepartmentManager;
use Admin\Service\MemberManager;
use Admin\Service\MessageManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MessageManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $logger = $container->get('Logger');
        $memberManager = $container->get(MemberManager::class);
        $deptManager = $container->get(DepartmentManager::class);

        return new MessageManager($memberManager, $deptManager, $entityManager, $logger);
    }


}