<?php

class Model_User_Trait_Captain extends Model_User_Trait_Abstract
{
    public function getIds()
    {
        return Dao('User_Captain')->loadDs($this->_user['uid'])->col('captain_id', array('uid' => $this->_user['uid']));
    }

    public function getList()
    {
        $captainList = Dao('User_Captain')->loadDs($this->_user['uid'])->find(array('uid' => $this->_user['uid']));

        foreach ($captainList as $key => &$captain) {
            if ($captainInfo = Dao('Static_Captain')->get($captain['captain_id'])) {
                $captain += $captainInfo;
            } else {
                unset($captainList[$key]);
            }
        }

        return $captainList;
    }

    public function hire($captainId)
    {
        // 是否已经雇用过
        $hired = Dao('User_Captain')->loadDs($this->_user['uid'])->row(array(
            'uid'         => $this->_user['uid'],
            'captain_id'  => $captainId,
        ));

        if ($hired) {
            throw new Core_Exception_Logic(__('你已经雇用过这名船长，请勿重复操作'));
        }

        $setArr = array(
            'uid'         => $this->_user['uid'],
            'captain_id'  => $captainId,
            'create_time' => $GLOBALS['_TIME'],
        );

        return Dao('User_Captain')->loadDs($this->_user['uid'])->replace($setArr);
    }

    public function dismiss($captainId)
    {
        $whereArr = array(
            'uid' => $this->_user['uid'],
            'captain_id' => $captainId,
        );

        return Dao('User_Captain')->loadDs($this->_user['uid'])->delete($whereArr);
    }
}