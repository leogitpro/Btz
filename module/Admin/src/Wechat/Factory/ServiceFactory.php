<?php
/**
 * ServiceFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Wechat\Factory;


use Admin\Wechat\Local;
use Admin\Wechat\Service;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $local = $container->get(Local::class);

        $wxId = 0;
        if (isset($options['wx_id'])) {
            $wxId = $options['wx_id'];
        }

        $logger = $container->get('Logger');

        return new Service($wxId, $local, $logger);

    }


}