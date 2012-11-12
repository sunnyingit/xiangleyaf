<?php

// 定义路径常量
define('APP_PATH', dirname(__DIR__) . '/');
define('SYS_PATH', dirname(APP_PATH) . '/system/');

$app = new Yaf_Application(APP_PATH . 'conf/app.ini');
$app->bootstrap()->run();