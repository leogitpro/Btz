<?php
/**
 * DoctrineSqlLoggerFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Service\Factory;


use Application\Service\DoctrineSqlLogger;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class DoctrineSqlLoggerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $logger = $container->get('Logger');

        return new DoctrineSqlLogger($logger);
    }


}