<?php

class Model_User_Trait_Tavern extends Model_User_Trait_Abstract
{
    const
        SHOW_CAPTAIN_NUMS = 3,       // 每次刷新显示几位船长
        REFRESH_INTERVAL  = 3600;    // 刷新间隔（单位：秒）

    /**
     * 我的酒馆信息
     *
     * @return array
     */
    public function get()
    {
        return Dao('User_Tavern')->loadDs($this->_user['uid'])->get($this->_user['uid']);
    }

    /**
     * 我本次在酒馆看到的船长列表
     *
     * @param array &$myTavernInfo
     * @return bool
     */
    public function current(&$myTavernInfo)
    {
        $captainIds = $this->refresh($myTavernInfo);

        if (!$captainIds) {
            return array();
        }

        // 根据 ids 获取详细船长信息
        return Dao('Static_Captain')->find(array('captain_id' => array('IN', $captainIds)));
    }

    /**
     * 刷新船长列表
     *
     * @param array &$myTavernInfo
     * @param array $forceRefresh 是否强制刷新
     * @return bool
     */
    public function refresh(&$myTavernInfo, $forceRefresh = false)
    {
        // 我的酒馆信息
        if ($myTavernInfo === null) {
            $myTavernInfo = $this->get();
        }

        // 还没有到刷新时间
        if (!$forceRefresh && $myTavernInfo && $GLOBALS['_TIME'] < $myTavernInfo['next_update_time']) {
            return $myTavernInfo['captain_ids'];
        }

        // 排除的ids
        $excludeIds = $this->_getExcludeCaptainIds($myTavernInfo['captain_ids']);

        // 随机获取新一轮的船长列表
        $captainIds = $this->_getNewCaptainIds($excludeIds);

        // 更新入库
        if ($captainIds) {
            $myTavernInfo = array(
                'uid'              => $this->_user['uid'],
                'next_update_time' => $GLOBALS['_TIME'] + self::REFRESH_INTERVAL,
                'captain_ids'      => implode(',', $captainIds),
            );
            Dao('User_Tavern')->loadDs($this->_user['uid'])->replace($myTavernInfo);
        }

        return $captainIds;
    }

    private function _getExcludeCaptainIds($lastIds)
    {
        // 上次我在酒馆看到的船长
        $lastIds = array_filter(explode(',', $lastIds));

        // 我已有的船长
        $myCaptainIds = $this->_user->captain->getIds();

        // 合并返回
        return array_merge($myCaptainIds, $lastIds);
    }

    private function _getNewCaptainIds($excludeIds = array())
    {
        // 所有对我解锁的船长
        $allCaptainIds = Dao('Static_Captain')->getUnlockIds($this->_user['level_id']);

        // 排除我已有的船长等
        $allCaptainIds = array_diff($allCaptainIds, $excludeIds);

        // 随机取N个返回
        return Helper_Array::rand($allCaptainIds, self::SHOW_CAPTAIN_NUMS);
    }
}