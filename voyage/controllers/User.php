<?php

class Controller_User extends Controller_Abstract
{
    /**
     * 顶部用户信息
     */
    public function indexAction()
    {
        $this->assign('user', $this->_user->__toArray());
    }
}