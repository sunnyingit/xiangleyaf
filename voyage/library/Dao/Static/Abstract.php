<?php

class Dao_Static_Abstract extends Com_Dao
{
    public function __construct()
    {
        $this->_dbName    = 'voyage_static_' . strtolower(CUR_LANG);
        $this->_tableName = strtolower(str_replace('Dao_Static_', '', get_called_class()));
    }
}