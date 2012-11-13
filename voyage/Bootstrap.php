<?php

// 框架初始化
Core_Bootstrap::init();

class Bootstrap extends Yaf_Bootstrap_Abstract
{
    public function _initPlugin(Yaf_Dispatcher $dispatcher)
    {
        $dispatcher->registerPlugin(new Plugin_Common());
    }

    public function _initView(Yaf_Dispatcher $dispatcher)
    {
        $dispatcher->setView(Core_View::getInstance());
    }
}