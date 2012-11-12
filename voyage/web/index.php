<?php

// 定义路径常量
define('APP_PATH', dirname(__DIR__) . '/');
define('SYS_PATH', dirname(APP_PATH) . '/system/');

// 调试模式密钥
define('DEBUG_XKEY', 'xiangleyaf');

$app = new Yaf_Application(APP_PATH . 'conf/app.ini');
$app->bootstrap()->run();