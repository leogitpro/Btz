<?php
/**
 * module.config.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Logger;


return [

    'service_manager' => [
        'factories' => [
            Service\LoggerService::class => Service\Factory\LoggerFactory::class,
        ],
        'aliases' => [
            'Logger' => Service\LoggerService::class,
        ],
    ],


    'logger' => [
        'writers' => [
            'default' => [
                'name' => \Zend\Log\Writer\Stream::class,
                'storage' => 'file', // Only for stream is save to file. needless for other writers.
                'options' => [
                    'stream' => rtrim(sys_get_temp_dir(), "/\\") . DIRECTORY_SEPARATOR . 'php-log-' . date('Ymd') . '.txt',
                    'mode' => 'a',
                    'log_separator' => PHP_EOL,
                    'chmod' => null,
                ],
                'filters' => [
                    'priority' => [
                        'name' => \Zend\Log\Filter\Priority::class,
                        'options' => [
                            'priority' => \Zend\Log\Logger::ERR,
                            'operator' => '<=',
                        ],
                    ],
                    /**
                    'regex' => [
                        'name' => \Zend\Log\Filter\Regex::class,
                        'options' => [
                            'regex' => '/.+/i', // Make sure execute: preg_match($regex, '') no error.
                        ],
                    ],
                    //*/
                    // ... other filter
                ],
                'formatter' => [ // Every writer only own one formatter!
                    'name' => \Zend\Log\Formatter\Simple::class,
                    'options' => [
                        'format' => '%priorityName%(%priority%):%timestamp% ' . PHP_EOL . '%message% %extra%' . PHP_EOL  . '========================================' . PHP_EOL . PHP_EOL,
                        'dateTimeFormat' => 'Y-m-d H:i:s A D',
                    ],
                ],
            ],
            // ... other writer
        ],
    ],

];