<?php

/**
 * 配置类相关
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Config.php 2 2012-10-12 04:14:32Z jiangjian $
 */

class Core_Config
{
    public static function load($name)
    {
        return Yaf_Registry::get('config')->get($name)->toArray();
    }

    public static function loadEnv($name)
    {
        return self::load($name);
    }
}