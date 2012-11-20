<?php

abstract class Core_Model_ArrayAccess extends Core_Model_Abstract implements ArrayAccess
{
    /**
     * 单条信息 ($userRow, $shipRow)
     *
     * @var array
     */
    protected $_array = array();

    public function setArrayAccess(&$array)
    {
        $this->_array = &$array;

        return $this;
    }

    public function offsetSet($key, $value)
    {
        $this->_array[$key] = $value;
    }

    public function offsetExists($key)
    {
        return isset($this->_array[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->_array[$key]);
    }

    public function offsetGet($key)
    {
        return isset($this->_array[$key]) ? $this->_array[$key] : null;
    }

    public function __toString()
    {
        return print_r($this->_array, true);
    }

    public function __toArray()
    {
        return $this->_array;
    }

    public function __get($var)
    {
        if (isset($this->_array[$var])) {
            return $this->_array[$var];
        }
    }
}