<?php

class Dao_Static_Ship extends Dao_Static_Abstract
{
    protected $_tableName  = 'ship';
    protected $_primaryKey = 'ship_id';
    protected $_nameField  = 'ship_name';

    public function getUnlockList($level)
    {
        return $this->find("`unlock_level` <= {$level}", '`ship_id` DESC');
    }
}