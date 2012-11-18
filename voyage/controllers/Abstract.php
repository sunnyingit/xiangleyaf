<?php

/**
 * 控制器抽象父类
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Abstract.php 6 2012-11-16 02:55:04Z jiangjian $
 */

abstract class Controller_Abstract extends Core_Controller_Web
{
    /**
     * 当前用户 uid
     *
     * @var int
     */

    protected $_uid;

    /**
     * 全局数组
     *
     * @var array
     */
    protected $_global = array();

    /**
     * 是否检测登录
     *
     * @var bool
     */
    protected $_checkAuth = true;

    /**
     * 从手机端接收的参数
     *
     * @var array
     */
    protected $_mobileParams = array();

    /**
     * 构造函数
     */
    public function init()
    {
        parent::init();

        // 初始化全局数组
        $this->initGlobalVar();

        // 接收手机端参数
        $this->initMobileParams();

        // 获取当前用户 uid
        $this->_uid = Model('User_Auth')->getUid();

        // 检测登录（是否允许游客访问）
        if ($this->_uid < 1 && $this->_checkAuth) {
            $this->redirect('/auth/login');
        }
    }

    public function __get($var)
    {
        // 获取当前用户实例
        if ($var == '_user') {

            if ($this->_uid < 1) {
                return null;
            }

            try {

                // 实例化一个玩家
                $user = new Model_User($this->_uid);
                $this->assign('user', $user->__toArray());

            } catch (Exception $e) {

                // 强制退出、重新登录
                Model('User_Auth')->logout();
                $this->redirect('/auth/login');
            }

            return $user;
        }

        return parent::__get($var);
    }

    /**
     * 初始化全局数组
     */
    public function initGlobalVar()
    {
        // 当前纪年、季节
        list($this->_global['YEAR'], $this->_global['SEASON']) = MyHelper_Year::get();

        // 传出模板变量
        $this->assign($this->_global, null);
        $this->assign('cookie', $this->_cookie);
    }

    /**
     * 接收手机端参数
     */
    public function initMobileParams()
    {
        $data = $this->getQuery();
        $this->_mobileParams = Model('User_Api')->initMobileParams($data);
    }

    /**
     * 获取用户盐
     *
     * @param string $extraKey 格外密钥
     * @return string
     */
    public function getSalt($extraKey = null)
    {
        return 'VoyageMobile:' . date('md') . ':' . $this->_uid . ($extraKey ? ':' . $extraKey : '');
    }

    /**
     * 加密
     *
     * @param string $content 待加密内容
     * @param string $extraKey 格外密钥
     * @return string
     */
    public function encrypt($content, $extraKey = null)
    {
        return Helper_Cryption_Rijndael::encrypt($content, $this->getSalt($extraKey));
    }

    /**
     * 解密
     *
     * @param string $content 待解密内容
     * @param string $extraKey 格外密钥
     * @return string
     */
    public function decrypt($content, $extraKey = null)
    {
        return Helper_Cryption_Rijndael::decrypt($content, $this->getSalt($extraKey));
    }

    /**
     * 批量加密指定列表的某些字段
     *
     * @param string $list
     * @param string/array $idFields 待加密字段名，可设多个
     * @param string $extraKey 格外密钥
     * @return string
     */
    public function encryptIds($list, $idFields = 'id', $extraKey = null)
    {
        if ($list) {
            foreach ($list as &$value) {
                foreach ((array) $idFields as $idField) {
                    $value[$idField] = $this->encrypt($value[$idField], $extraKey);
                }
            }
        }

        return $list;
    }

    // public function preResponse()
    public function assignUser()
    {
        // 获取最新的用户信息
        // 因为 $this->_user 最初是在构造函数中初始化赋值的，可能在 Action 处理中已被更改
        if ($this->_user->isUpdated()) {
            $this->_user->refresh();
        }
    }
}