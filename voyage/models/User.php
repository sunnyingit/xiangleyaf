<?php

/**
 * 用户模型基类
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: User.php 6 2012-11-16 02:55:04Z jiangjian $
 */

class Model_User extends Model_User_Abstract
{
    const
        STATUS_IN_PORT = 0, // 港口中
        STATUS_SAILING = 1; // 航行中

    /**
     * 用户信息是否已被更新
     *
     * @var array
     */
    protected $_isUpdated = false;

    public function __construct($uid)
    {
        $this->_user = $this->_getUser($uid);

        // 升级检测
        $this->base->levelUp();

        // 首次登陆、连续N天登陆奖励
        $this->base->loginReward();

        // 生命值、行动力、精力等自动恢复
        $this->restore->regular();
    }

    public function __get($var)
    {
        static $varsMap = array(
            'base'    => 1,
            'sail'    => 1,
            'restore' => 1,
            'ship'    => 1,
            'captain' => 1,
            'battle'  => 1,
            'tavern'  => 1,
        );

        if (isset($varsMap[$var])) {
            $class = 'Model_User_Trait_' . ucfirst($var);
            return $this->{$var} = new $class($this);
        }

        if (isset($this->_user[$var])) {
            return $this->_user[$var];
        }

        parent::__get();
    }

    protected function _getUser($uid)
    {
        // 基本信息
        $user = Dao('User')->loadDs($uid)->get($uid);

        if (! $user) {
            throw new Core_Exception_Logic(__('用户信息不存在'));
        }

        // 等级信息
        $user['level'] = Dao('Static_Level')->get($user['level_id']);
        $user['next_level'] = Dao('Static_Level')->get($user['level_id'] + 1);

        // 官职信息
        if ($user['position_id']) {
            $user['position'] = Dao('Static_Position')->get($user['position_id']);
        }

        // 国家信息
        if ($user['nation_id']) {
            $user['nation'] = Dao('Static_Nation')->get($user['nation_id']);
        }

        return $user;
    }

    public function assertInPort($msg = '')
    {
        if ($this->_user['status'] != self::STATUS_IN_PORT || $this->_user['port_from'] < 1) {
            throw new Core_Exception_Logic($msg ?: __('你不在码头，无法进行该项操作'));
        }
    }

    public function assertSailing($msg = '')
    {
        if ($this->_user['status'] != self::STATUS_SAILING) {
            throw new Core_Exception_Logic($msg ?: __('你尚未出海，无法进行该项操作'));
        }
    }

    public function refresh()
    {
        $this->_user = $this->_getUser($this->_user['uid']);
        return $this;
    }

    public function isUpdated()
    {
        return (bool) $this->_isUpdated;
    }

    public function setUpdated($bool = true)
    {
        $this->_isUpdated = (bool) $bool;
    }

    /**
     * 更新用户（封装）
     *
     * @param string/array $key
     * @param mixed/null $value
     * @return void
     */
    public function update($key, $value = null)
    {
        if (! $key) {
            return false;
        }

        $setArr = $key;
        if (! is_array($key)) {
            $setArr = array($key => $value);
        }

        // 更新用户索引表
        if (! $this->_updateUserIndex($setArr)) {
            throw new Core_Exception_SQL('更新用户索引表失败');
        }

        // 更新用户基本信息表
        if (! $this->_updateUserBase($setArr)) {
            throw new Core_Exception_SQL('更新用户基本信息表失败');
        }

        // 设置为已更新
        $this->setUpdated();

        return true;
    }

    /**
     * 更新用户索引表
     *
     * @param array &$setArr
     * @return void
     */
    protected function _updateUserIndex(&$setArr)
    {
        $updateArr = array();

        foreach (array('level_id', 'postion_id', 'nation_id', 'user_name') as $field) {
            if (isset($setArr[$field])) {
                $updateArr[$field] = $setArr[$field];
            }
        }

        if ($updateArr) {
            return Dao('User_Index')->updateByPk($updateArr, $this->_user['uid']);
        }

        return true;
    }

    /**
     * 更新用户基本信息表
     *
     * @param array &$setArr
     * @return bool
     */
    protected function _updateUserBase(&$setArr)
    {
        return $this->Dao('User')->updateByPk($setArr, $this->_user['uid']);
    }
}