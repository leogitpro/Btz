<?php
/**
 * Index controller factory class
 *
 * User: leo
 */

namespace Application\Controller\Factory;


use Application\Service\ContactManager;
use Application\Service\MailManager;
use Application\Service\NavManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $controllerName, array $options = null)
    {
        $contactManager = $serviceManager->get(ContactManager::class);
        $mailManager = $serviceManager->get(MailManager::class);
        $navManager = $serviceManager->get(NavManager::class);

        return new $controllerName($contactManager, $mailManager, $navManager);
    }

}