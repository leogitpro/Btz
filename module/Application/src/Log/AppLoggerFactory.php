<?php

namespace Application\Log;


use Interop\Container\ContainerInterface;
use Zend\Log\Filter\Priority;
use Zend\Log\Filter\Regex;
use Zend\Log\Formatter\Simple;
use Zend\Log\Logger;
use Zend\Log\Writer\ChromePhp;
use Zend\Log\Writer\FirePhp;
use Zend\Log\Writer\Stream;
use Zend\ServiceManager\Factory\FactoryInterface;


class AppLoggerFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {


        $config = $container->get('config');
        if (empty($config['applogger'])) {
            return new AppLogger(new Logger());
        }
        $appLogConfig = $config['applogger'];

        $logger = new Logger();

        if (isset($appLogConfig['firephp']) && $appLogConfig['firephp']) {
            //Need firefox with firephp extension
            $writeFirePhp = new FirePhp();
            $logger->addWriter($writeFirePhp);
        }

        if (isset($appLogConfig['chromephp']) && $appLogConfig['chromephp']) {
            //Need chrome with chromephp extension
            $writeChromePhp = new ChromePhp();
            $logger->addWriter($writeChromePhp);
        }

        if(empty($appLogConfig['writers'])) {
            return new AppLogger($logger);
        }

        foreach($appLogConfig['writers'] as $writerArr) {
            if('stream' == $writerArr['name']) {
                $writer = new Stream($writerArr['uri']);

                if(isset($writerArr['formatters'])) {
                    foreach($writerArr['formatters'] as $key => $opt) {
                        if('simple' == $key) {
                            $formatter = new Simple($opt['format'], $opt['datetimeformat']);
                            $writer->setFormatter($formatter);
                        }
                        //to be continue ...
                    }
                }

                if(isset($writerArr['filters'])) {
                    foreach($writerArr['filters'] as $key => $opt) {
                        if('priority' == $key) {
                            $filter = new Priority($opt);
                            $writer->addFilter($filter);
                        }
                        if('regex' == $key) {
                            $filter = new Regex($opt);
                            $writer->addFilter($filter);
                        }
                        //to be continue ...
                    }
                }

                $logger->addWriter($writer);
            }
            //to be continue
        }

        $appLogger = new AppLogger($logger);
        return $appLogger;
    }

}