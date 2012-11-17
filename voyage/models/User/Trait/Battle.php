<?php

class Model_User_Trait_Battle extends Model_User_Trait_Abstract
{
    /**
     * 海战对手列表
       在同一海域内
       度过新手教程，等级高于20级（暂定）
       等级差不超过+5级
       不处于同一国家
       可以直接攻击在同一海域内的悬赏榜上的异国玩家，无等级限制
       拥有至少一场战斗的精力消耗数量
       足够的体力，具体数值待定
     *
     * @return array
     */
    public function getOpponent()
    {
        // 等级范围
        $levelStart = max(1, $this->_user['level_id'] - 5);
        $levelEnd   = $this->_user['level_id'] + 5;

        // demo
        $whereSql = '1';
        $whereSql .= " AND level_id >= '{$levelStart}' AND level_id <= '{$levelEnd}'";
        $whereSql .= " AND uid != '{$this->_user['uid']}'";
        // $whereSql .= " AND nation_id != '{$this->_user['nation_id']}'";
        // $whereSql .= " AND status = 1";
        // $whereSql .= " AND hp >= '". Model_Battle::USER_MIN_HP . "'";

        $list = Dao('Battle_Block')->findByPage($whereSql, 0, 10);
        if (! $list) {
            return array();
        }

        // TODO 计算战斗力、公会信息
        foreach ($list as &$value) {
            $value['flagship_id'] = rand(1, 5); // demo
            $value['user_name'] = Dao('User_Index')->name($value['uid']);
        }

        return $list;
    }

    public function getLogRow($logId)
    {
        if ($logId < 1) {
            throw new Core_Exception_Logic(__('战斗记录不存在'));
        }

        $logRow = Dao('User_Log_Battle')->loadDs($this->_user['uid'])->get($logId);
        if (! $logRow) {
            throw new Core_Exception_Logic(__('战斗记录不存在'));
        }

        if ($this->_user['uid'] != $logRow['uid']) {
            throw new Core_Exception_Logic(__('你只能看自己的战斗记录'));
        }

        return $this->_decodeLog($logRow);
    }

    public function getLogList($start = 0, $pageSize = 20)
    {
        $fields = 'id, attacker_uid, defender_uid, result, result_msg, create_time';
        $logList = Dao('User_Log_Battle')->loadDs($this->_user['uid'])->findByPage(
            array('uid' => $this->_user['uid']), $start, $pageSize, 'id DESC', $fields
        );

        return $logList;

/*  Query Builder
        $logList = Dao('User_Log_Battle')->loadDs($uid)
                                       ->field('attacker_uid', 'defender_uid')
                                       ->where(array('uid' => $uid))
                                       ->limit($start, $pageSize)
                                       ->sort('id DESC')
                                       ->fetchAll();

        $sql = "SELECT {$fields} FROM `user_battle_log` WHERE `uid` = '{$uid}' ORDER BY `id` DESC";
        $logList = Dao('User_Log_Battle')->loadDs($uid)->query('limitQuery', $sql, $start, $pageSize);
*/
    }

    private function _decodeLog($log)
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