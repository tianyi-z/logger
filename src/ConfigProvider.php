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