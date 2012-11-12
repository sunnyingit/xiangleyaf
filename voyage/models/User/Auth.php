<?php

/**
 * 用户 Auth 加解密
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Auth.php 298 2012-11-06 07:30:23Z jiangjian $
 */

class Model_User_Auth extends Core_Model_Abstract
{
    // Cookie 名称
    private static $_cookieName = '__voyage_authUser_1106';

    // Cookie 加、解密私钥
    private static $_cookieSerectKey = '0deb8c3b74678a14fd7551abc309c722';

    /**
     * 获取当前登录用户信息
     *
     * @return array
     */
    public function get()
    {
        if (!isset($_COOKIE[self::$_cookieName]) || empty($_COOKIE[self::$_cookieName])) {
            return array();
        }

        $authUser = json_decode(stripslashes($_COOKIE[self::$_cookieName]), true);
        if (!$authUser || !$this->valid($authUser)) {
            return array();
        }

        unset($authUser['xkey']);

        return $authUser;
    }

    public function valid($authUser)
    {
        return ($authUser['mac'] && $authUser['xkey'] && $authUser['xkey'] == $this->_xkey($authUser));
    }

    /**
     * 设置当前用户信息
     *
     * @param array $authUser
     * @return void
     */
    public function set(array $authUser)
    {
        $authUser['xkey'] = $this->_xkey($authUser);
        setcookie(self::$_cookieName, json_encode($authUser), null, '/');
    }

    /**
     * 注销
     *
     * @return void
     */
    public function logout()
    {
        setcookie(self::$_cookieName, '', time() - 86400, '/');
    }

    /**
     * 生成组合key
     *
     * @param array $authUser
     * @return string
     */
    private function _xkey($authUser)
    {
        $string = '';
        if (isset($authUser['uid'])) {
            $string .= $authUser['uid'] . ':';
        }

        $string .= $authUser['mac'] . ':' . self::$_cookieSerectKey;
        return strtoupper(md5(strtoupper(sha1($string))));
    }
}