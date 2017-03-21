<?php
/**
 * WeChatFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat\Service\Factory;


use Interop\Container\ContainerInterface;
use WeChat\Service\AccountService;
use WeChat\Service\WeChatService;
use Zend\ServiceManager\Factory\FactoryInterface;

class WeChatFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $account = $container->get(AccountService::class);

        return new WeChatService($account);
    }


}