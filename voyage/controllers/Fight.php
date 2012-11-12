<?php

/**
 * 战斗控制器
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Fight.php 301 2012-11-08 01:29:44Z jiangjian $
 */

class Controller_Fight extends Controller_Abstract
{
    /**
     * 选择对手
     */
    public function opponentAction()
    {
        // 必须为出海状态
        $this->_user->assertSailing();

        $opponentList = $this->_user->fight->getOpponent();
        $this->assign('opponentList', $opponentList);
    }

    /**
     * 进入战斗
     */
    public function fightAction()
    {
        // 必须为出海状态
        $this->_user->assertSailing();

        $enemyUid = xintval($this->getx('enemy_uid'));
        if ($enemyUid < 1 || $enemyUid == $this->_user['uid']) {
            exit('Invalid EnemyUid');
        }

        // 敌方用户信息
        $enemy = new Model_User($enemyUid);

        // 初始化一场战斗
        $fight = new Model_Fight($this->_user, $enemy);

        // 记录进入战斗时间、进入战斗前航行到达时间（用于战后重置计算）
        $this->_session['arrive_time_info'] = array(
            'arrive_time_before_fight' => $this->_user['arrive_time'],
            'fight_start_time'         => $GLOBALS['_TIME'],
        );

        // 正式开打!
        $fight->process();

        // 战后相关事宜处理（加减属性等）
        $logId = $fight->after();

        // 直接跳到回放器：播放战斗过程
        $this->forward('Fight', 'replay', array('id' => $logId, 'in_fight' => true));

        return false;
    }

    /**
     * 战斗后重置航行到达时间
     */
    public function resetArriveTimeAction()
    {
        $arriveTimeInfo = $this->_session['arrive_time_info'];
        if (!$arriveTimeInfo) {
            exit('-1');
        }

        // 战斗耗时（在真实世界里玩家在战斗界面停留了几秒，最少3秒）
        $fightElapse = max(3, $GLOBALS['_TIME'] - $arriveTimeInfo['fight_start_time']);

        // 重置航行到达时间
        if (!$this->_user->update('arrive_time', ($arriveTimeInfo['arrive_time_before_fight'] + $fightElapse))) {
            exit('-2');
        }

        // 清除无用数据
        unset($this->_session['arrive_time_info']);

        exit('200');
    }

    /**
     * 战斗回放
     */
    public function replayAction()
    {
        $logId  = $this->getInt('id');
        if ($logId < 1) {
            exit('Invalid FightLogId');
        }

        $logRow = Model('Fight_Player')->get($this->_user['uid'], $logId);

        // 0:战斗中，1:回放中
        $isReplay = $this->get('in_fight') ? 0 : 1;

        // 传出模板变量
        $this->assign(array(
            'selfShips'      => $logRow['self_ships'],
            'enemyShips'     => $logRow['enemy_ships'],
            'fightResult'    => $logRow['result'],
            'fightResultMsg' => $logRow['result_msg'],
            'fireLogs'       => $logRow['fire_logs'],
            'isReplay'       => $isReplay,
        ));
    }

    /**
     * 我的战斗历史记录列表
     */
    public function historyAction()
    {
        $pageSize = 10;
        $page     = max(1, $this->getInt('page'));
        $start    = ($page - 1) * $pageSize;

        $logList = Model('Fight_Player')->listByUid($this->_user['uid'], $start, $pageSize);

        $this->assign('logList', $logList);
    }
}