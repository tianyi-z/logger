<?php
declare(strict_types=1);
return [
    'default' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/hyperf.log',
                'level' => intval(env('APP_LOGGER_LEVEL', Monolog\Logger::NOTICE)),
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\JsonFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => "Y-m-d H:i:s",
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
];