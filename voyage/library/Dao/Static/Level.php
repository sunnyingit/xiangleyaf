<?php

class Dao_Static_Level extends Dao_Static_Abstract
{
    /**
     * 根据经验值获取对应等级
     *
     * @param  int $exp
     * @return int
     */
    public function getLevelByExp($exp)
    {
        return $this->one('id', "`exp` <= {$exp}", '`id` DESC');
    }
}