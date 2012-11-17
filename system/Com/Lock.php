<?php

/**
 * 流程锁
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Lock.php 156 2012-11-08 01:37:15Z silverd30@gmail.com $
 */

class Com_Lock
{
    const CACHE_PREFIX = 'Lock::';
    const LOOP_TIMES   = 100;

    private $_cache;

    /**
     * 构造函数
     *
     * @param Com_Cache $cache
     * @return void
     */
    public function __construct($cache)
    {
        $this->_cache = $cache;
    }

    /**
     * 是否被锁（如果没有被锁，则立即加锁）
     *
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    public function isLocked($key, $ttl = 10)
    {
        $result = $this->_cache->add(self::CACHE_PREFIX . $key, time(), $ttl);
        return $result ? false : true;
    }

    /**
     * 加锁（如果发现已被锁，则循环尝试N次）
     *
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    public function doLock($key, $ttl = 10)
    {
        for ($i = 1; $i < self::LOOP_TIMES; $i++) {
            if (! $this->isLocked($key, $ttl)) {
                return true;
            }
            usleep(100000); // 休息100ms
        }

        return false;
    }

    /**
     * 解锁
     *
     * @param string $key
     * @return bool
     */
    public function doUnlock($key)
    {
        return $this->_cache->delete(self::CACHE_PREFIX . $key);
    }
}