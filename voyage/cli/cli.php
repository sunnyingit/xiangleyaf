<?php

define('APP_PATH', dirname(__FILE__) . '/../');

$app = new Yaf_Application(APP_PATH . 'conf/app.ini');
$response = $app->bootstrap()
                ->getDispatcher()
                ->dispatch(new Yaf_Request_Simple());