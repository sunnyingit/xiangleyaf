<?php

class Model_User_Trait_Fight extends Model_User_Trait_Abstract
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
        // $whereSql .= " AND country_id != '{$this->_user['country_id']}'";
        // $whereSql .= " AND status = 1";
        // $whereSql .= " AND hp >= '". Model_Fight::USER_MIN_HP . "'";

        $list = Dao('User_Index')->findByPage($whereSql, 0, 10);
        if (!$list) {
            return array();
        }

        // TODO 计算战斗力、公会信息
        foreach ($list as &$value) {
            $value['flagship_id'] = rand(1, 5); // demo
        }

        return $list;
    }
}