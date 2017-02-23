<?php
/**
 * AppLoggerFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Service\Factory;


use Application\Service\AppLogger;
use Interop\Container\ContainerInterface;
use Zend\Log\Logger;
use Zend\ServiceManager\Factory\FactoryInterface;

class AppLoggerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        //if (empty($config['logger']['writers'])) {
            //throw new \Exception('无法配置系统 Logger 记录器, 系统宕机!');
        //}

        $logger = new Logger();

        foreach ((array)$config['logger']['writers'] as $writerConfig) {

            $options = $writerConfig['options'];
            if (isset($writerConfig['storage']) && 'file' == $writerConfig['storage']) {
                $path = dirname($options['stream']);
                if (empty($path)) {
                    continue;
                }
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
            }

            $writer = new $writerConfig['name']($options);

            if (!empty($writerConfig['formatter'])) {
                $formatter = new $writerConfig['formatter']['name']($writerConfig['formatter']['options']);
                $writer->setFormatter($formatter);
            }

            if(!empty($writerConfig['filters'])) {
                foreach ($writerConfig['filters'] as $filterConfig) {
                    $filter = new $filterConfig['name']($filterConfig['options']);
                    $writer->addFilter($filter);
                }
            }

            $logger->addWriter($writer);
        }

        return new AppLogger($logger);
    }

}