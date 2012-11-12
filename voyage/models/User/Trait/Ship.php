<?php

class Model_User_Trait_Ship extends Model_User_Trait_Abstract
{
    /**
     * 加载我的舰船信息
     *
     * @return array
     */
    public function getList()
    {
        $shipList = Dao('User_Ship')->loadDs($this->_user['uid'])->find(array('uid' => $this->_user['uid']));

        // demo
        $shipList = array(
            array('ship_id' => 1, 'captain_id' => 11),
            array('ship_id' => 2, 'captain_id' => 22),
            array('ship_id' => 3, 'captain_id' => 33),
            array('ship_id' => 4, 'captain_id' => 44),
            array('ship_id' => 5, 'captain_id' => 55),
        );

        foreach ($shipList as $key => &$ship) {
            if ($shipInfo = Dao('Static_Ship')->get($ship['ship_id'])) {
                $ship += $shipInfo;
            } else {
                unset($shipList[$key]);
            }
        }

        return $shipList;
    }
}