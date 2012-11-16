<?php

class Dao_Static_Captain extends Dao_Static_Abstract
{
    public function getUnlockList($level)
    {
        return $this->find("`unlock_level` <= {$level}", '`captain_id` DESC');
    }

    public function getUnlockIds($level)
    {
        return $this->col($this->_primaryKey, "`unlock_level` <= {$level}");
    }
}