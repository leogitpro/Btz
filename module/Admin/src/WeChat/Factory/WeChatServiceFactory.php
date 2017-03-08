<?php
/**
 * WeChatServiceFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Wechat\Factory;


use Admin\WeChat\Local;
use Admin\WeChat\WeChatService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;


class WeChatServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $wxId = 0;
        if (isset($options['wx_id'])) {
            $wxId = $options['wx_id'];
        }

        $local = $container->build(Local::class, ['wx_id' => $wxId]);
        $logger = $container->get('Logger');

        return new WeChatService($wxId, $local, $logger);

    }


}