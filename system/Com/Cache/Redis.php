<?php

/**
 * Redis 封装类
 *
 * @author JiangJian <silverd@sohu.com>
 * @uses https://github.com/nicolasff/phpredis
 * $Id: Redis.php 268 2012-10-31 03:45:23Z jiangjian $
 */

class Com_Cache_Redis
{
    /**
     * 连接实例
     *
     * @var new Redis()
     */
    private $_redis;

    /**
     * 缺省配置
     *
     * @var array
     */
    private $_config = array(
        'host'       => '127.0.0.1',
        'port'       => '6379',
        'database'   => 0,
        'timeout'    => 0,
        'persistent' => true,
        'options'    => array(),
    );

    /**
     * 加载模块配置
     *
     * @param string $module
     * @throws Core_Exception_Fatal
     */
    public function __construct($module = 'global')
    {
        $config = Core_Config::loadEnv('redis');
        if (! isset($config[$module])) {
            throw new Core_Exception_Fatal('没有找到 ' . $module . ' 模块的 redis 配置信息，请检查 redis.conf.php');
        }

        $this->_config = $config[$module] + $this->_config;
    }

    /**
     * 释放连接
     */
    public function __destruct()
    {
        if ($this->_redis && is_object($this->_redis)) {

            if (method_exists($this->_redis, 'close')) {
                $this->_redis->close();
            } elseif (method_exists($this->_redis, 'quit')) {
                $this->_redis->quit();
            }

            $this->_redis = null;
        }
    }

    /**
     * 建立连接
     *
     * @return bool
     */
    private function _connect()
    {
        if ($this->_redis === null || ! is_object($this->_redis)) {

            $this->_redis = new Redis();

            $func = $this->_config['persistent'] ? 'pconnect' : 'connect';
            $this->_redis->$func($this->_config['host'], $this->_config['port'], $this->_config['timeout']);

            // 附加参数
            if ($this->_config['options']) {
                foreach ($this->_config['options'] as $key => $value) {
                    $this->_redis->setOption($key, $value);
                }
            }

            // 选择数据库
            if (isset($this->_config['database']) && $this->_config['database'] > 0) {
                $this->_redis->select($this->_config['database']);
            }
        }
    }

    /**
     * 调用魔术方法
     *
     * @param string $method
     * @param mixed $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        $this->_connect();
        return call_user_func_array(array($this->_redis, $method), $args);
    }
}