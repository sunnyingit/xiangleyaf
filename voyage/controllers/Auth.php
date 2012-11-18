<?php

class Controller_Auth extends Controller_Abstract
{
    protected $_checkAuth = false;

    /**
     * 登录
     */
    public function loginAction()
    {
        // 已登录的跳走
        if ($this->_user) {
            $this->redirect('/');
        }

        // 表单提交后
        if ($this->isPost()) {

            $userAccount = $this->getx('user_account');
            $password    = $this->getx('password');

            // 登陆验证
            try {
                Model('User_Auth')->login($userAccount, $password);
            } catch (Core_Exception_Logic $e) {
                exit($e->getMessage());
            }

            // 登陆成功跳转
            $this->redirect('/');
        }
    }

    /**
     * 登出
     */
    public function logoutAction()
    {
        if ($this->_user) {
            Model('User_Auth')->logout();
        }

        $this->redirect('/');
    }

    /**
     * 注册
     */
    public function registerAction()
    {

    }
}