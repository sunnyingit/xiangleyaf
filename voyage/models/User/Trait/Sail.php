<?php

class Model_User_Trait_Sail extends Model_User_Trait_Abstract
{
    /**
     * 增加航行记录
     *
     * @param int $portTo
     * @param bool $isInstant 是否瞬间移动
     * @return bool
     */
    public function log($portTo, $isInstant = 0)
    {
        $setArr = array(
            'uid'         => $this->_user['uid'],
            'port_from'   => $this->_user['port_from'],
            'port_to'     => $portTo,
            'depart_time' => $GLOBALS['_TIME'],
            'arrive_time' => $GLOBALS['_TIME'],
            'is_instant'  => $isInstant ? 1 : 0,
        );

        return Dao('User_Log_Sail')->loadDs($this->_user['uid'])->insert($setArr);
    }

    /**
     * 扬帆起航
     *
     * @param int $portTo
     * @return bool
     */
    public function depart($portTo)
    {
        // 两个港口间需要航行多久(单位:秒)
        $distanceTime = Dao('Static_Port')->getDistance($this->_user['port_from'], $portTo);

        // 更新玩家目的港口、出港时间、到达时间
        $setArr = array(
            'port_to'     => $portTo,
            'depart_time' => $GLOBALS['_TIME'],
            'arrive_time' => $GLOBALS['_TIME'] + $distanceTime,
            'status'      => 1,  // 更新状态：航行中
        );

        return $this->_user->update($setArr);
    }

    /**
     * 到达目的港口
     *
     * @return bool
     */
    public function arrive()
    {
        $setArr = array(
            'port_from'   => $this->_user['port_to'],
            'port_to'     => 0,
            'depart_time' => 0,
            'arrive_time' => 0,
            'status'      => 0,  // 更新状态：港口中
        );

        return $this->_user->update($setArr);
    }
}