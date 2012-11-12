<?php

/**
 * 战斗播放、回放器模型
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Recorder.php 279 2012-11-01 10:14:26Z jiangjian $
 */

class Model_Fight_Player extends Core_Model_Abstract
{
    public function get($uid, $logId)
    {
        if ($logId < 1) {
            throw new Core_Exception_Logic(__('Invalid FightLogId'));
        }

        $logRow = Dao('User_Log_Fight')->loadDs($uid)->get($logId);
        if (!$logRow) {
            throw new Core_Exception_Logic(__('Invalid FightLogRow'));
        }

        if ($logRow['uid'] != $uid) {
            // throw new Core_Exception_Logic(__('Invalid Request'));
        }

        return $this->_decode($logRow);
    }

    public function listByUid($uid, $start = 0, $pageSize = 30)
    {
        $fields = 'id, attacker_uid, defender_uid, result, result_msg, create_time';
        $logList = Dao('User_Log_Fight')->loadDs($uid)->findByPage(array('uid' => $uid), $start, $pageSize, 'id DESC', $fields);

/*  Query Builder
        $logList = Dao('User_Log_Fight')->loadDs($uid)
                                       ->field('attacker_uid', 'defender_uid')
                                       ->where(array('uid' => $uid))
                                       ->limit($start, $pageSize)
                                       ->sort('id DESC')
                                       ->fetchAll();

        $sql = "SELECT {$fields} FROM `user_fight_log` WHERE `uid` = '{$uid}' ORDER BY `id` DESC";
        $list = Dao('User_Log_Fight')->loadDs($uid)->query('limitQuery', $sql, $start, $pageSize);
*/

        return $logList;
    }

    private function _decode($log)
    {
        if (isset($log['self_ships'])) {
            $log['self_ships']  = unserialize($log['self_ships']);
        }

        if (isset($log['enemy_ships'])) {
            $log['enemy_ships'] = unserialize($log['enemy_ships']);
        }

        if (isset($log['fire_logs'])) {
            $log['fire_logs']   = unserialize($log['fire_logs']);
        }

        return $log;
    }
}