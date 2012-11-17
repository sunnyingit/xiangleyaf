<?php

/**
 * 缓存经理人
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Cache.php 276 2012-11-01 06:03:20Z jiangjian $
 */

class Com_Cache
{
    /**
     * 使用的缓存类名
     *
     * @var string
     */
    private $_className;

    /**
     * 默认模块
     *
     * @var string
     */
    private $_defaultModule = 'default';

    /**
     * 单例模式
     *
     * @var array
     */
    private static $_instances = array();

    public static function getInstance($className)
    {
        if (! isset(self::$_instances[$className])) {
            self::$_instances[$className] = new self($className);
        }

        return self::$_instances[$className];
    }

    public function __construct($className)
    {
        $this->_className = 'Com_Cache_' . $className;
    }

    /**
     * 每个模块都有一个缓存实例
     *
     * @param string $module
     * @return object Com_Cache_*
     */
    public function __get($module)
    {
        return $this->{$module} = new $this->_className($module);
    }

    /**
     * 默认模块的魔术方法（那么调用时可以省略默认模块名）
     * 例如：$this->get('key') 等价于 $this->default->get('key')
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        $cacheObj = $this->{$this->_defaultModule};
        return call_user_func_array(array($cacheObj, $method), $args);
    }
}