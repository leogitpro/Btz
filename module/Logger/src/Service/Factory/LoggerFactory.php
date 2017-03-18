<?php
/**
 * LoggerFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Logger\Service\Factory;


use Interop\Container\ContainerInterface;
use Logger\Service\LoggerService;
use Zend\Log\Logger;
use Zend\ServiceManager\Factory\FactoryInterface;


class LoggerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $logger = new Logger();

        $config = $container->get('config');
        $writers = @$config['logger']['writers'];
        foreach ((array)$writers as $writerConfig) {

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

        return new LoggerService($logger);
    }
}