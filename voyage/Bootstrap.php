<?php

class Bootstrap extends Yaf_Bootstrap_Abstract
{
    public function _initApp()
    {
        Yaf_Loader::getInstance()->import(SYS_PATH . 'Bootstrap.php');
    }

    public function _initConfig()
    {
        // 把配置保存起来
        Yaf_Registry::set('config', Yaf_Application::app()->getConfig());
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher)
    {
        // 注册一个插件
        $objSamplePlugin = new Plugin_Sample();
        $dispatcher->registerPlugin($objSamplePlugin);
    }

    public function _initRoute(Yaf_Dispatcher $dispatcher)
    {
        // 在这里注册自己的路由协议,默认使用简单路由
    }

    public function _initView(Yaf_Dispatcher $dispatcher)
    {
        // 在这里注册自己的view控制器，例如smarty,firekylin
    }
}