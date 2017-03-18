<?php
/**
 * MailFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Mail\Service\Factory;


use Interop\Container\ContainerInterface;
use Mail\Service\MailService;
use Zend\ServiceManager\Factory\FactoryInterface;


class MailFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        if(isset($config['mail'])) {
            $config = $config['mail'];
        } else {
            $config = [];
        }

        return new MailService($config);
    }
}