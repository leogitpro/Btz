<?php
/**
 * RemoteFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Wechat\Factory;


use Admin\Wechat\Http;
use Admin\Wechat\Remote;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class RemoteFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Remote($container->get(Http::class), $container->get('Logger'));
    }

}