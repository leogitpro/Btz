<?php
/**
 * LocalFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Wechat\Factory;


use Admin\Service\WechatManager;
use Admin\Wechat\Local;
use Admin\Wechat\Remote;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class LocalFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $wechatManager = $container->get(WechatManager::class);
        $remote = $container->get(Remote::class);
        $logger = $container->get('Logger');

        return new Local($wechatManager, $remote, $logger);
    }


}