<?php

/**
 * 控制器抽象父类
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Abstract.php 156 2012-11-08 01:37:15Z silverd30@gmail.com $
 */

abstract class Core_Controller_Abstract extends Yaf_Controller_Abstract
{
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
                throw new Core_Exception_Fatal('Undefined controller property: ' . get_class($this) . '::' . $key);
        }
    }

    public function get($key)
    {
        return $this->_request->get($key);
    }

    public function getx($key)
    {
        return strip_tags(trim($this->_request->get($key)));
    }

    public function getInt($key)
    {
        return intval($this->_request->get($key));
    }

    public function isGet()
    {
        return $this->_request->isGet();
    }

    public function isPost()
    {
        return $this->_request->isPost();
    }

    public function isAjax()
    {
        return $this->_request->isXmlHttpRequest();
    }

    public function getQuery($key = null, $default = null)
    {
        return $this->_request->getQuery($key, $default);
    }

    public function getPost($key = null, $default = null)
    {
        return $this->_request->getPost($key, $default);
    }

    public function getPostx($key = null, $default = null)
    {
        return Helper_String::deepFilterDatas($this->_getPost($key, $default), array('strip_tags', 'trim'));
    }

    public function getParam($key = null, $default = null)
    {
        return $this->_request->getParam($key, $default);
    }

    public function getParams()
    {
        return $this->_request->getParams();
    }
}