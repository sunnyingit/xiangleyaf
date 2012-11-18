<?php

/**
 * 用户认证模型
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Auth.php 2193 2012-06-18 03:19:30Z jiangjian $
 */

class Model_User_Auth extends Core_Model_Abstract
{
    private $_cookieName = '__voyage_auth_1117';

    /**
     * 获取当前用户 uid
     *
     * @return int $uid
     */
    public function getUid()
    {
        if (! $userToken = $this->_cookie->get($this->_cookieName)) {
            return -1;
        }

        if (! $uid = Dao('User_Index')->getUidByToken($userToken)) {
            return -2;
        }

        return $uid;
    }

    /**
     * 登录
     *
     * @param string $userAccount
     * @param string $password
     * @throws Core_Exception_Logic
     * @return array $user
     */
    public function login($userAccount, $password)
    {
        if (! $userAccount || ! $password) {
            throw new Core_Exception_Logic(__('用户名和密码不能为空'));
        }

        // 用户信息
        $user = Dao('User_Index')->getUserByAccount($userAccount);

        if (! $user) {
            throw new Core_Exception_Logic(__('用户名不存在'));
        }

        // 验证密码
        if ($user['password'] != sha1($password)) {

            // 写错误日志
            Com_Log::write('errPassword', "{$userAccount}\t{$password}\t" . Helper_Client::getUserIp());

            throw new Core_Exception_Logic(__('密码错误，请重试'));
        }

        // 用户详细信息
        $user = array_merge($user, Dao('User')->loadDs($user['id'])->get($user['id']));

        // 是否已被封禁
        if ($user['block_login'] && ($user['block_login'] == -1 || $GLOBALS['_TIME'] - $user['block_login'] < 1)) {
            throw new Core_Exception_Logic(__('该用户已被禁用'));
        }

        // 设置 userToken
        $this->_setUserToken($user['id']);

        return $user;
    }

    /**
     * 设置 userToken
     *
     * @param int $uid
     * @param string $userToken
     * @throws Core_Exception_Logic
     * @return void
     */
    private function _setUserToken($uid, $userToken = null)
    {
        if (! $userToken) {
            $userToken = $this->_getUserToken($uid);
        }

        if (! Dao('User_Index')->updateUserToken($uid, $userToken)) {
            throw new Core_Exception_Logic(__('登陆失败，请稍候再试'));
        }

        // 设置 cookie
        $this->_cookie->set($this->_cookieName, $userToken);
    }

    /**
     * 获取 userToken
     *
     * @param int $uid
     * @return string
     */
    private function _getUserToken($uid)
    {
        return sha1($uid . Helper_String::random(32));
    }

    /**
     * 登出
     *
     * @return void
     */
    public function logout()
    {
        $this->_cookie->del($this->_cookieName);
    }
}