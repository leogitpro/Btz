<?php
/**
 * Admin menu helper factory
 *
 * User: leo
 */

namespace Admin\View\Helper\Factory;


use Admin\Service\NavManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MenuFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $navManager = $serviceManager->get(NavManager::class);

        $items = [];

        if(preg_match('/TopRightMenu$/', $requestedName)) {
            $items = $navManager->getTopRightItems();
        }
        if(preg_match('/SideTreeMenu/', $requestedName)) {
            $items = $navManager->getSideTreeItems();
        }
        if(preg_match('/PageBreadcrumbBar/', $requestedName)) {
            $items = $navManager->getBreadcrumbItems();
        }

        return new $requestedName($items);
    }


}