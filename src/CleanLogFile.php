#!/usr/bin/env php
<?php
// 日志目录.
$logPath = dirname(dirname(dirname(dirname(__DIR__)))).DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
$paths = [
    'logs' => [
        'rule' => '/hyperf-(.{10})\.log/',
        'retain' => getenv('CLEAN_LOG_EXCEPTIONS_RETAIN_DAY') ? getenv('CLEAN_LOG_EXCEPTIONS_RETAIN_DAY') : 7, // 保留天数.
    ],
    'business-logs' => [
        'rule' => '/hyperf-(.{10})\.log/',
        'retain' => getenv('CLEAN_LOG_BUSINESS_RETAIN_DAY') ? getenv('CLEAN_LOG_BUSINESS_RETAIN_DAY') : 2, // 保留天数.
    ],
];
$nowTime = strtotime(date('Y-m-d'));
foreach ($paths as $path => $items) {
    $path = $logPath.$path.DIRECTORY_SEPARATOR;
    if (!is_dir($path)) {
        continue;
    }
    $files = scandir($path);
    foreach ($files as $file) {
        if (in_array($file, ['.', '..'])) {
            continue;
        }
        $absolutely = $path.$file;
        $match = [];
        preg_match($items['rule'], $file, $match);
        if (empty($match[1])) {
            continue;
        }
        $fileTime = strtotime($match[1]);
        if (!$fileTime) {
            continue;
        }
        if (!is_file($absolutely)) {
            continue;
        }
        if (!is_writable($absolutely)) {
            // linux
            var_dump($absolutely.'没权限');
            continue;
        }
        $diff = ($nowTime - $fileTime) / 24 / 3600;
        if ($diff > $items['retain']) {
            unlink($absolutely);
        }
    }
}