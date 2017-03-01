<?php
/**
 * LocalFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\WeChat\Factory;


use Admin\Service\WeChatManager;
use Admin\WeChat\Local;
use Admin\WeChat\Remote;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;


class LocalFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $wcm = $container->get(WeChatManager::class);
        $remote = $container->get(Remote::class);
        $logger = $container->get('Logger');

        return new Local($wcm, $remote, $logger);
    }


}