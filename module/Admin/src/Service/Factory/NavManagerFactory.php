<?php
/**
 * Admin module menu manager factory
 *
 * User: leo
 */

namespace Admin\Service\Factory;



use Admin\Service\AdminerManager;
use Admin\Service\AuthService;
use Admin\Service\NavManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class NavManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $authService = $serviceManager->get(AuthService::class);
        $adminerManager = $serviceManager->get(AdminerManager::class);

        $viewHelperManager = $serviceManager->get('ViewHelperManager');
        $urlHelper = $viewHelperManager->get('url');

        return new NavManager($authService, $adminerManager, $urlHelper);
    }


}