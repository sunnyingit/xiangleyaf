<?php

class Model_User_Trait_Restore extends Model_User_Trait_Abstract
{
    /**
     * 能力值恢复配置
     *
     * @var array
     */
    protected $_restoreConfig = array(
        // 生命值：每3分钟恢复1点
        'hp' => array(
            'point' => 1,
            'interval' => 180,
        ),
        // 行动力：每10分钟恢复5点
        'move' => array(
            'point' => 5,
            'interval' => 600,
        ),
        // 精力：每2分钟恢复1点
        'energy' => array(
            'point' => 1,
            'interval' => 120,
        ),
    );

    /**
     * 用户属性定期自动恢复
     *
     * @return bool
     */
    public function regular()
    {
        $setArr = array();

        foreach (array('hp', 'move', 'energy') as $field) {

            // 修复因并发问题造成的不合法数据
            if ($this->_fix($field, $setArr) < 1) {
                continue;
            }

            // 已是最满，无需恢复
            if ($this->_user[$field] == $this->_user[$field . '_max']) {
                continue;
            }

            // 下一恢复时间未到
            if ($this->_user[$field . '_in_next_time'] > $GLOBALS['_TIME']) {
                continue;
            }

            // 每隔多久恢复一次
            $interval = $this->_restoreConfig[$field]['interval'];

            // 计算：截止现在，应恢复多少次
            // 特别注意：
            //      手机三国这里用 ceil，会有个问题：
            //      当前时间恰好等于 hp_in_next_time 时，会算出 $times 结果为 0，即不会增加点数，
            //      这将导致本次倒计时失败，需要等到页面再刷新一次才会增加。
            //      所以我们这里用 1 + floor，使逻辑正常，时间到了至少加1点
            $times = 1 + floor(($GLOBALS['_TIME'] - $this->_user[$field . '_in_next_time']) / $interval); // ☆关键

            if ($times < 1) {
                continue;
            }

            // 计算：应恢复多少点=应恢复次数*每次恢复点数
            $addNum = $times * $this->_restoreConfig[$field]['point'];
            $setArr[$field] = array('+', $addNum, $this->_user[$field . '_max']);

            // 再下一次的恢复时间点（此处存在并发问题，所以需要 _fixRestore）
            if ($this->_user[$field] + $addNum < $this->_user[$field . '_max']) {    // 未加满
                $setArr[$field . '_in_next_time'] = array('+', $times * $interval); // ☆关键
            } else {    // 已加满则下次无需更新
                $setArr[$field . '_in_next_time'] = 0;
            }
        }

        if (!$setArr) {
            return false;
        }

        if (!$this->_user->update($setArr)) {
            return false;
        }

        // TODO
        // 更新战斗对手索引表
        // 用于筛选符合条件的战斗对手，例如生命值不小于25，必须不同国家等
    }

    /**
     * 修复因并发等原因造成的不合法数据
     *
     * @param string $field
     * @param array &$setArr
     * @return bool
     */
    protected function _fix($field, &$setArr)
    {
        // 当 hp > hp_max 时的不合法数据
        if ($this->_user[$field] > $this->_user[$field . '_max']) {
            $setArr[$field] = $this->_user[$field . '_max'];
            $setArr[$field . '_in_next_time'] = 0;
            return -1;
        }

        // 当 hp < hp_max 时，hp_in_next_time 却等于 0
        if ($this->_user[$field] < $this->_user[$field . '_max'] && !$this->_user[$field . '_in_next_time']) {
            $setArr[$field . '_in_next_time'] = $GLOBALS['_TIME'];
            return -2;
        }

        // 当 hp = hp_max 时，hp_in_next_time 却不为 0
        if ($this->_user[$field] == $this->_user[$field . '_max'] && $this->_user[$field . '_in_next_time']) {
            $setArr[$field . '_in_next_time'] = 0;
            return -3;
        }

        return 1;
    }

    public function full($fields)
    {
        if (!$fields) {
            $fields = array('hp', 'move', 'energy');
        }

        foreach ((array) $fields as $field) {
            $setArr[$field] = $this->_user[$field . '_max'];
            $setArr[$field . 'in_next_time'] = 0;
        }

        return $this->_user->update($setArr);
    }

    public function change($updateArr)
    {
        $setArr = array();

        foreach ($updateArr as $field => $offset) {

            if ($value > 0) {   // 增加
                $setArr[$field] = array('+', $offset, $this->_user[$field . '_max']);

            } else {    // 减少
                $setArr[$field] = array('-', abs($offset), 0);
            }

            // 计算下次恢复时间点
            $setArr[$field . '_in_next_time'] = $this->calcNextUpdateTime($field, $offset);
        }

        return $this->_user->update($setArr);
    }

    /**
     * 计算下次恢复时间点
     *
     * @param string $field
     * @param int $offset
     * @return timestamp/false
     */
    public function calcNextUpdateTime($field, $offset = 0)
    {
        // 默认不更新
        $nextUpdateTime = false;

        // 加属性
        if ($offset > 0) {

            // 如果已加满
            if ($this->_user[$field] + $offset >= $this->_user[$field . '_max']) {
                $nextUpdateTime = 0;
            }

        // 减属性
        } elseif ($offset < 0) {

            // 其实这里 hp_in_next_time 只会为 0，即从满血执行扣血操作时
            // 因为一般在 $this->restore->regular() 中已经重置过 hp_in_next_time
            // 所以此处 hp_in_next_time 要么为 0，要么一定大于当前时间
            if ($this->_user[$field . '_in_next_time'] < $GLOBALS['_TIME']) {
                $nextUpdateTime = $GLOBALS['_TIME'] + $this->_restoreConfig[$field]['interval'];
            }
        }

        return $nextUpdateTime;
    }
}