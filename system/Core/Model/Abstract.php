<?php

/**
 * 模型抽象父类
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Abstract.php 293 2012-11-06 01:41:03Z jiangjian $
 */

abstract class Core_Model_Abstract
{
    /**
     * 缓存装饰模式
     *
     * @param string $func
     * @param mixed $args
     * @param int $ttl
     * @param int $flushCache 强制刷新缓存
     * @return mixed
     */
    public function cached($func, $args = null, $ttl = 0, $flushCache = false)
    {
        $cacheObj = $this->_memcache;
        $key = md5(get_class($this) . ':' . $func . ($args ? ':' . serialize($args) : ''));

        if ($flushCache || !$data = $cacheObj->get($key)) {
            $data = call_user_func_array(array($this, $func), $args);
            $cacheObj->set($key, $data, $ttl);
        }

        return $data;
    }

    /**
     * 动态获取变量
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        switch ($key) {

            case '_session':
                return $this->_session  = Yaf_Session::getInstance();

            case '_cookie':
                return $this->_cookie   = Com_Cookie::getInstance();

            case '_memcache' :
                return $this->_memcache = Com_Cache::getInstance('Memcache');

            case '_redis':
                return $this->_redis    = Com_Cache::getInstance('Redis');

            case '_lock':
                return $this->_lock     = new Com_Lock($this->_memcache);

            default:
                throw new Core_Exception_Fatal('Undefined model property: ' . get_class($this) . '::' . $key);
        }
    }
}