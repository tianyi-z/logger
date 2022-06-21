<?php
namespace YuanxinHealthy\Logger;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                \Hyperf\Contract\StdoutLoggerInterface::class => StdoutLoggerFactory::class,
            ],
            'middlewares' => [
                'http' => [
                    BusinessLogMiddleware::class,
                ],
            ],
            'logger' => [
                'businessLog' => [
                    'handler' => [
                        'class' => Monolog\Handler\RotatingFileHandler::class,
                        'constructor' => [
                            'dateFormat' => "Y-m-d H:i:s",
                            'filename' => BASE_PATH . '/runtime/business-logs/hyperf.log',
                            'level' => intval(env('APP_LOGGER_LEVEL', Monolog\Logger::INFO)),
                        ],
                    ],
                    'formatter' => [
                        'class' => Monolog\Formatter\JsonFormatter::class,
                        'constructor' => [
                            'allowInlineLineBreaks' => true,
                        ],
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => '配置文件', // 描述
                    'source' => __DIR__ . '/../publish/logger.php',
                    'destination' => BASE_PATH . '/config/autoload/logger.php',
                ],
            ],
        ];
    }
}