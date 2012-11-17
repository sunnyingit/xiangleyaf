<?php

class Dao_User_Abstract extends Com_Dao
{
    protected static $_dbSuffix = array();

    public function loadDs($uid)
    {
        if (! isset(self::$_dbSuffix[$uid])) {
            self::$_dbSuffix[$uid] = Dao('User_Index')->getDbSuffix($uid);
        }

        return $this->setDs(self::$_dbSuffix[$uid]);
    }

    public function setDs($dbSuffix)
    {
        $this->_dbName = 'voyage_' . $dbSuffix;

        return $this;
    }
}