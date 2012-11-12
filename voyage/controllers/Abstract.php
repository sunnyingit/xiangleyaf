<?php

/**
 * 控制器抽象父类
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Abstract.php 309 2012-11-12 06:01:52Z jiangjian $
 */

abstract class Controller_Abstract extends Core_Controller_Web
{
    /**
     * 当前用户对象
     *
     * @var Model_User
     */
    protected $_user;

    // 全局数组
    protected $_global = array();

    // 是否检测登录
    protected $_checkAuth = true;

    // 从手机端接收的参数
    protected $_mobileParams = array();

    /**
     * 构造函数
     */
    public function init()
    {
        // 初始化全局数组
        $this->_initGlobalVar();

        // 接收手机端参数
        $this->_initMobileParams();

        // 获取当前用户信息
        $uid = $this->_getCurrentUid();
        if ($uid > 0) {
            // 实例化一个玩家
            $this->_user = new Model_User($uid);
        }

        // 检测登录（是否允许游客访问）
        if (!$this->_user && $this->_checkAuth) {
            throw new Core_Exception_Logic(__('Access Denied - Need Login'));
        }
    }

    /**
     * 初始化全局数组
     */
    protected function _initGlobalVar()
    {
        // 当前纪年、季节
        list($this->_global['YEAR'], $this->_global['SEASON']) = MyHelper_Year::get();
    }

    /**
     * 获取当前用户uid
     */
    protected function _getCurrentUid()
    {
        // 先从 Cookie 中取，有则表示已登录
        $cookieUser = Model('User_Auth')->get();
        if ($cookieUser && isset($cookieUser['uid']) && $cookieUser['uid'] > 0) {
            return $cookieUser['uid'];
        }

        // demo
        if (isset($_GET['test'])) {

            $mac = $this->getx('test');
            $mac = $mac ? $mac : 'test';

        } else {

            // 用户设备号
            $mac = $this->_mobileParams['udid'];
            if (!$mac || strlen($mac) > 50) {
                throw new Core_Exception_Logic(__('非法访问，用户MAC为空'));
            }

            // 验证设备号是否篡改
            $macArr = array(
                'mac' => $mac,
                'xkey' => $this->_mobileParams['hashCode'],
            );
            if (!Model('User_Auth')->valid($macArr)) {
                throw new Core_Exception_Logic(__('非法访问，用户MAC不合法'));
            }
        }

        // 根据设备号取uid
        if (!$uid = Model('User_Api')->getUidByMac($mac)) {
            // 如果取不到，则注册新用户
            if (!$uid = Model('User_Api')->register($mac)) {
                throw new Core_Exception_Logic(__('用户初始化失败，请联系管理员'));
            }
        }

        // 写 Cookie
        $cookieUser = array(
            'uid' => $uid,
            'mac' => $mac,
        );
        Model('User_Auth')->set($cookieUser);

        // 记录日志
        if (isDebug()) {
            Com_Log::write('mobileAccess', $_SERVER['REQUEST_URI']);
        }

        return $uid;
    }

    protected function _initMobileParams()
    {
        $data = $this->getQuery();
        $this->_mobileParams = Model('User_Api')->initMobileParams($data);
    }

    public function preResponse()
    {
        // 模板中是否加载顶部用户信息栏（开发阶段）
        if ($this->getx('head')) {
            $this->_cookie['head'] = true;
        }

        // 获取最新的用户信息
        // 因为 $this->_user 最初是在构造函数中初始化赋值的，可能在 Action 处理中已被更改
        if ($this->_user->isUpdated()) {
            $this->_user->refresh();
        }

        // 传出模板变量
        $this->assign($this->_global, null);
        $this->assign('cookie', $this->_cookie);
        $this->assign('user', $this->_user->__toArray());
    }
}