<?php

/**
 * 战斗控制器
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Battle.php 4 2012-11-15 05:28:51Z jiangjian $
 */

class Controller_Battle extends Controller_Abstract
{
    /**
     * 选择对手
     */
    public function opponentAction()
    {
        // 必须为出海状态
        $this->_user->assertSailing();

        $opponentList = $this->_user->battle->getOpponent();
        $this->assign('opponentList', $this->encryptIds($opponentList, 'uid'));
    }

    /**
     * 进入战斗
     */
    public function battleAction()
    {
        // 必须为出海状态
        $this->_user->assertSailing();

        $enemyUid = $this->decrypt($this->getx('enemy_uid'));
        if ($enemyUid < 1 || $enemyUid == $this->_user['uid']) {
            exit('Invalid EnemyUid');
        }

        // 敌方用户信息
        $enemy = new Model_User($enemyUid);

        // 初始化一场战斗
        $battle = new Model_Battle($this->_user, $enemy);

        // 记录进入战斗时间、进入战斗前航行到达时间（用于战后重置计算）
        $this->_session['arrive_time_info'] = array(
            'arrive_time_before_battle' => $this->_user['arrive_time'],
            'battle_start_time'         => $GLOBALS['_TIME'],
        );

        // 正式开打!
        $battle->process();

        // 战后相关事宜处理（加减属性等）
        $logId = $battle->after();

        // 直接跳到回放器：播放战斗过程
        $this->forward('Battle', 'replay', array('id' => $this->encrypt($logId), 'in_battle' => true));

        return false;
    }

    /**
     * 战斗后重置航行到达时间
     */
    public function resetArriveTimeAction()
    {
        $arriveTimeInfo = $this->_session['arrive_time_info'];
        if (! $arriveTimeInfo) {
            exit('-1');
        }

        // 战斗耗时（在真实世界里玩家在战斗界面停留了几秒，最少3秒）
        $battleElapse = max(3, $GLOBALS['_TIME'] - $arriveTimeInfo['battle_start_time']);

        // 重置航行到达时间
        if (! $this->_user->update('arrive_time', ($arriveTimeInfo['arrive_time_before_battle'] + $battleElapse))) {
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
        $logId = $this->decrypt($this->getx('id'));
        if ($logId < 1) {
            exit('Invalid BattleLogId');
        }

        $logRow = $this->_user->battle->getLogRow($logId);

        // 0:战斗中，1:回放中
        $isReplay = $this->getInt('in_battle') ? 0 : 1;

        // 传出模板变量
        $this->assign(array(
            'selfShips'  => $logRow['self_ships'],
            'enemyShips' => $logRow['enemy_ships'],
            'result'     => $logRow['result'],
            'resultMsg'  => $logRow['result_msg'],
            'fireLogs'   => $logRow['fire_logs'],
            'isReplay'   => $isReplay,
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

        $logList  = $this->_user->battle->getLogList($start, $pageSize);

        // 加密id字段
        if ($logList) {
            foreach ($logList as &$log) {
                $log['id']        = $this->encrypt($log['id']);
                $log['enemy_uid'] = $this->encrypt($log['attacker_uid'] == $this->_user['uid'] ? $log['defender_uid'] : $log['attacker_uid']);
            }
        }

        $this->assign('logList', $logList);
    }
}