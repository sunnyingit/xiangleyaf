<?php

class Dao_Static_Abstract extends Com_Dao
{
    protected $_dbName;

    public function __construct()
    {
        $this->_dbName = 'voyage_static_' . CUR_LANG;
    }
}