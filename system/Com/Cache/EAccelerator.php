<?php

/**
 * EA 缓存封装类
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: EAccelerator.php 274 2012-11-01 01:41:53Z jiangjian $
 */

class Com_Cache_EAccelerator
{
    /**
     * 构造函数
     * 检测eAccelerator扩展是否开启
     */
    public function __construct()
    {
        if (! extension_loaded('eaccelerator')) {
            throw new Exception('The eAccelerator extension must be loaded.');
        }
    }

    /**
     * 设置一个缓存变量
     *
     * @param string $key    缓存Key
     * @param mixed $value   缓存内容
     * @param int $ttl       缓存时间(秒)
     * @return boolean       是否缓存成功
     */
    public function set($key, $value, $ttl = 60)
    {
        return eaccelerator_put($key, $value, $ttl);
    }

    /**
     * 获取一个已经缓存的变量
     *
     * @param string $key  缓存Key
     * @return mixed       缓存内容
     */
    public function get($key)
    {
        return eaccelerator_get($key);
    }

    /**
     * 删除一个已经缓存的变量
     *
     * @param  string $key  缓存Key
     * @return boolean      是否删除成功
     */
    public function del($key)
    {
        return eaccelerator_rm($key);
    }

    /**
     * 删除全部缓存变量
     *
     * @return boolean       是否删除成功
     */
    public function delAll()
    {
        return eaccelerator_clean();
    }

    /**
     * 检测是否存在对应的缓存
     *
     * @param  string $key  缓存Key
     * @return boolean      是否存在key
     */
    public function has($key)
    {
        return (eaccelerator_get($key) === null ? false : true);
    }
}