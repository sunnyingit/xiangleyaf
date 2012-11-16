<?php

class Dao_User_Index extends Dao_User_Abstract
{
    protected $_dbName          = 'voyage_share';
    protected $_tableName       = 'user_index';
    protected $_primaryKey      = 'uid';
    protected $_nameField       = 'user_name';

    public function getDbSuffix($uid)
    {
        $dbSuffix = $this->oneByPk('db_suffix', $uid);
        if ($dbSuffix === false) {
            throw new Core_Exception_Logic(__('用户 (UID: {uid}) 不存在或已被禁用，请联系管理员', array('uid' => $uid)));
        }

        return $dbSuffix;
    }
}