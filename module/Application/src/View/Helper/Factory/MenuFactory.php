<?php
/**
 * View helper menu factory
 *
 * User: leo
 */


namespace Application\View\Helper\Factory;


use Application\Service\NavManager;
use Application\View\Helper\Menu;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MenuFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $navManager = $container->get(NavManager::class);

        $items = $navManager->getMenuItems();

        return new Menu($items);
    }

}
