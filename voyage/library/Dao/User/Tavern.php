<?php

class Dao_User_Tavern extends Dao_User_Abstract
{
    protected $_tableName  = 'user_tavern';
    protected $_primaryKey = 'uid';

    public function getCaptainIds($uid)
    {
        $captainIds = $this->col('captain_ids', array('uid' => $uid));
        if (!$captainIds) {
            return array();
        }

        return array_filter(explode(',', $captainIds));
    }
}