<?php

/**
 * Cookie 封装
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Cookie.php 319 2012-11-12 09:24:54Z jiangjian $
 */

class Com_Cookie implements ArrayAccess
{
    private static $_instance;
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
    }

    public function offsetSet($name, $value)
    {
        return $this->set($name, $value);
    }

    public function offsetGet($name)
    {
        return $this->get($name);
    }

    public function offsetExists($name)
    {
        return $this->has($name);
    }

    public function offsetUnset($name)
    {
        return $this->del($name);
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    public function __unset($name)
    {
        return $this->del($name);
    }

    public function set($name, $value, $expire = null, $path = '/', $domain = null, $secure = false, $httponly = false)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
        $_COOKIE[$name] = $value;
    }

    public function get($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    public function has($name)
    {
        return isset($_COOKIE[$name]);
    }

    public function del($name)
    {
        unset($_COOKIE[$name]);
    }

    public function __toString()
    {
        return print_r($_COOKIE, true);
    }
}