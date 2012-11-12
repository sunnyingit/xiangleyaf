<?php

class Dao_Static_Level extends Dao_Static_Abstract
{
    protected $_tableName  = 'level';
    protected $_primaryKey = 'level_id';
    protected $_nameField  = 'level_name';

    /**
     * 根据经验值获取对应等级
     *
     * @param  int $exp
     * @return int
     */
    public function getLevelByExp($exp)
    {
        return $this->row("`exp` <= {$exp}", 'level_id', '`level_id` DESC');
    }
}