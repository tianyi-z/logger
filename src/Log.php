<?php
namespace YuanxinHealthy\Logger;

use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;

class Log
{
    public static function get(string $name = 'app', $group = 'default')
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($name, $group);
    }
}