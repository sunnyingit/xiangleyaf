<?php

class Dao_User_Abstract extends Com_Dao
{
    protected static $_userDbs = array();

    public function loadDs($uid)
    {
        if (!isset(self::$_userDbs[$uid])){
            $dbSuffix = Dao('User_Index')->getDbSuffix($uid);
            self::$_userDbs[$uid] = 'voyage_' . $dbSuffix;
        }

        $this->_dbName = self::$_userDbs[$uid];
        return $this;
    }

    public function setDs($dbSuffix)
    {
        $this->_dbName = 'voyage_' . $dbSuffix;
    }
}