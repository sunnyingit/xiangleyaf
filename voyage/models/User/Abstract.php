<?php

abstract class Model_User_Abstract extends Core_Model_Abstract implements ArrayAccess
{
    /**
     * 当前用户信息
     *
     * @var array
     */
    protected $_user = array();

    public function offsetSet($key, $value)
    {
        $this->_user[$key] = $value;
    }

    public function offsetExists($key)
    {
        return isset($this->_user[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->_user[$key]);
    }

    public function offsetGet($key)
    {
        return isset($this->_user[$key]) ? $this->_user[$key] : null;
    }

    public function __toString()
    {
        return print_r($this->_user, true);
    }

    public function __toArray()
    {
        return $this->_user;
    }

    public function Dao($class)
    {
        return Dao($class)->loadDs($this->_user['uid']);
    }
}