<?php

/**
 * 战斗记录器模型
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Recorder.php 309 2012-11-12 06:01:52Z jiangjian $
 */

class Model_Fight_Recorder extends Core_Model_Abstract
{

    /**
     * 最终战果（胜/负/平）
     *
     * @var const Model_Fight::WIN/LOSE/DRAW
     */
    private $_fightResult;

    /**
     * 最终战果影响文字
     *
     * @var string
     */
    private $_fightResultMsg = array();

    /**
     * 每回合开炮次数统计
     * 可据此得出：
     *     本次战斗共打了几个回合
     *     本次战斗共开了几次炮
     *
     * @var int
     */
    private $_roundCount = array();

    /**
     * 详细的开炮攻击记录
     *
     * @var int
     */
    private $_fireLogs = array();

    /**
     * 双方战前战船阵列
     *
     * @var array
     */
    private $_ships = array();

    /**
     * 战斗记录器初始化
     *
     * @param Model_User $self
     * @param Model_User $enemy
     */
    public function init(Model_User $self, Model_User $enemy)
    {
        if (!$self || !$enemy) {
            throw new Core_Exception_Logic(__('战斗记录器初始化失败'));
        }

        $this->_self  = $self;
        $this->_enemy = $enemy;
    }

    /**
     * 增加一条开炮攻击记录
     *
     * @param int $round
     * @param array $log
     * @return bool
     */
    public function add($round, $log)
    {
        $this->_fireLogs['self'][]  = $log['self'];
        $this->_fireLogs['enemy'][] = $log['enemy'];

        // 统计每回合开炮次数
        if (!isset($this->_roundCount[$round])) {
            $this->_roundCount[$round] = 0;
        }
        $this->_roundCount[$round]++;

        return $this;
    }

    /**
     * 本次战斗打了几个回合
     *
     * @return int
     */
    public function roundCount()
    {
        return count($this->_roundCount);
    }

    /**
     * 本次战斗双方共开炮几次
     *
     * @return int
     */
    public function fireCount()
    {
        return array_sum($this->_roundCount);
    }

    /**
     * 保存战斗记录
     * 同时保存两份（双方各一份）
     *
     * @return bool
     */
    public function save()
    {
        if (!$this->_fireLogs) {
            return false;
        }

        // 先记录到我自己的库中
        $setArr = array(
            'uid'           => $this->_self['uid'],
            'attacker_uid'  => $this->_self['uid'],
            'defender_uid'  => $this->_enemy['uid'],
            'self_ships'    => serialize($this->_ships['self']),
            'enemy_ships'   => serialize($this->_ships['enemy']),
            'result'        => $this->_fightResult,
            'result_msg'    => $this->_fightResultMsg['self'],
            'fire_logs'     => serialize($this->_fireLogs['self']),
            'create_time'   => $GLOBALS['_TIME'],
        );
        $logId = Dao('User_Log_Fight')->loadDs($this->_self['uid'])
                            ->hashTable($this->_self['uid'])
                            ->insert($setArr);

        // 再记录到对方的库中
        $setArr = array(
            'uid'           => $this->_enemy['uid'],
            'attacker_uid'  => $this->_self['uid'],
            'defender_uid'  => $this->_enemy['uid'],
            'self_ships'    => serialize($this->_ships['enemy']),
            'enemy_ships'   => serialize($this->_ships['self']),
            'result'        => $this->_getOppositeResult($this->_fightResult), // 相反的战果
            'result_msg'    => $this->_fightResultMsg['enemy'],
            'fire_logs'     => serialize($this->_fireLogs['enemy']),
            'create_time'   => $GLOBALS['_TIME'],
        );
        Dao('User_Log_Fight')->loadDs($this->_enemy['uid'])
                            ->hashTable($this->_enemy['uid'])
                            ->insert($setArr);

        return $logId;
    }

    /**
     * 获取相反的战果
     *
     * @return const
     */
    private function _getOppositeResult($fightResult)
    {
        if ($fightResult == Model_Fight::WIN) {
            return Model_Fight::LOSE;
        } elseif ($fightResult == Model_Fight::LOSE) {
            return Model_Fight::WIN;
        }

        return $fightResult;
    }

    public function setShips($selfShips, $enemyShips)
    {
        $this->_ships['self']  = $selfShips;
        $this->_ships['enemy'] = $enemyShips;

        return $this;
    }

    public function setFightResult($fightResult)
    {
        $this->_fightResult = $fightResult;

        return $this;
    }

    public function setFightResultMsg($fightResultMsg)
    {
        $this->_fightResultMsg = $fightResultMsg;

        return $this;
    }
}