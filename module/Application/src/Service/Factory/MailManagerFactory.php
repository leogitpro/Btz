<?php
/**
 * Mail manager factory
 *
 * User: leo
 */

namespace Application\Service\Factory;


use Application\Service\MailManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MailManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $config = $serviceManager->get('Config');
        if(isset($config['mail'])) {
            $config = $config['mail'];
        } else {
            $config = [];
        }

        return new MailManager($config, $serviceManager->get('Logger'));
    }


}