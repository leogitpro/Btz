<?php
/**
 * Admin module menu manager factory
 *
 * User: leo
 */

namespace Admin\Service\Factory;


use Admin\Service\AuthService;
use Admin\Service\MemberManager;
use Admin\Service\NavManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class NavManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $authService = $serviceManager->get(AuthService::class);
        $memberManager = $serviceManager->get(MemberManager::class);

        $viewHelperManager = $serviceManager->get('ViewHelperManager');
        $urlHelper = $viewHelperManager->get('url');

        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

        return new NavManager($authService, $memberManager, $urlHelper, $entityManager);
    }


}