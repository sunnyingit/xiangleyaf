<?php

/**
 * 用户模型基类
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: User.php 309 2012-11-12 06:01:52Z jiangjian $
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

    public function __construct($uid)
    {
        $this->_user = $this->_getUser($uid);

        $this->_useTrait(array('Base', 'Sail', 'Restore', 'Ship', 'Captain', 'Fight', 'Tavern'));

        // 升级检测
        $this->base->levelUp();

        // 首次登陆、连续N天登陆奖励
        $this->base->loginReward();

        // 生命值、行动力、精力等自动恢复
        $this->restore->regular();
    }

    protected function _useTrait($traits)
    {
        foreach ((array) $traits as $trait) {
            $class = 'Model_User_Trait_' . ucfirst($trait);
            $this->{lcfirst($trait)} = new $class($this);
        }

        return $this;
    }

    protected function _getUser($uid)
    {
        // 基本信息
        $user = Dao('User')->loadDs($uid)->get($uid);
        if (!$user) {
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
        if ($user['country_id']) {
            $user['country'] = Dao('Static_Country')->get($user['country_id']);
        }

        return $user;
    }

    public function refresh()
    {
        // 合并数组（加号合并：后者如有重复键不会覆盖前者）
        $this->_user = $this->_getUser($this->_user['uid']) + $this->_user;

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
        if (!$key) {
            return false;
        }

        $setArr = $key;
        if (!is_array($key)) {
            $setArr = array($key => $value);
        }

        // 更新用户索引表
        if (!$this->_updateUserIndex($setArr)) {
            throw new Core_Exception_SQL('更新用户索引表失败');
        }

        // 更新用户基本信息表
        if (!$this->_updateUserBase($setArr)) {
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
        $setArrIndex = array();

        foreach (array('level_id', 'postion_id', 'country_id', 'user_name') as $field) {
            if (isset($setArr[$field])) {
                $setArrIndex[$field] = $setArr[$field];
            }
        }

        if ($setArrIndex) {
            return Dao('User_Index')->updateByPk($setArrIndex, $this->_user['uid']);
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
        // 扣生命值、行动力、精力后，触发自动恢复机制 TODO 统一放到 Model_User_Trait_Restore 中
        foreach (array('hp', 'move', 'energy') as $field) {
            if (isset($setArr[$field]) && '-' == $setArr[$field][0]) {
                // 更新下次恢复时间
                $setArr[$field . '_in_next_time'] = $this->restore->calcNextUpdateTime($field, -abs($setArr[$field][1]));
            }
        }

        return $this->DaoDs('User')->updateByPk($setArr, $this->_user['uid']);
    }
}