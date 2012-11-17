<?php

/**
 * 加载类相关
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Loader.php 2 2012-10-12 04:14:32Z jiangjian $
 */

class Core_Loader
{
    /**
     * 单例模式
     *
     * @param string $className
     * @return object
     */
    private static $_loadedClass = array();
    public static function getSingleton($className)
    {
        if (! isset(self::$_loadedClass[$className])) {
            self::$_loadedClass[$className] = new $className;
        }

        return self::$_loadedClass[$className];
    }
}