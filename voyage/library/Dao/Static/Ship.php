<?php

class Dao_Static_Ship extends Dao_Static_Abstract
{
    public function getUnlockList($level)
    {
        return $this->find("`unlock_level` <= {$level}", '`ship_id` DESC');
    }
}