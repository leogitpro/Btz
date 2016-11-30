<?php

namespace Application;

use Zend\Log\Logger;

return [
    'firephp' => false,
    'chromephp' => false,
    'writers' => [
        [
            'name' => 'stream',
            'uri' => rtrim(sys_get_temp_dir(), "/\\") . DIRECTORY_SEPARATOR . 'log-' . date('Ymd') . '.txt',
            'formatters' => [
                'simple' => [
                    'format' => $format = '%priorityName%(%priority%) %timestamp%:' . PHP_EOL. '%message%' . PHP_EOL . '################################################################################' . PHP_EOL,
                    'datetimeformat' => 'Y-m-d H:i:s',
                ]
            ],
            'filters' => [
                'priority' => Logger::DEBUG,
                //'regex' => '/wx/',
            ],
        ],
    ],
];