<?php

class Dao_User_Index extends Dao_User_Abstract
{
    protected $_dbName          = 'voyage_share';
    protected $_tableName       = 'user_index';
    protected $_primaryKey      = 'uid';
    protected $_primaryKeyType  = 'bigint';
    protected $_nameField       = 'user_name';

    public function getDbSuffix($uid)
    {
        $dbSuffix = $this->oneByPk('db_suffix', $uid);
        if ($dbSuffix === false) {
            throw new Core_Exception_Logic(__('用户 (UID: {uid}) 不存在或已被禁用，请联系管理员', array('uid' => $uid)));
        }

        return $dbSuffix;
    }

    public function generateUid()
    {
        if (!$this->_db()->query('INSERT INTO `user_uid_generator` (uid) VALUES (null)')) {
            throw new Core_Exception_SQL('uid_generator failed');
        }

        $uid = $this->_db()->lastInsertId();
        if (!$uid) {
            throw new Core_Exception_SQL('uid_generator failed');
        }

        // 为了不规则，后缀加4位随机数
        $uid .= str_pad(rand(1, 9999), 4, 0, STR_PAD_LEFT);

        return $uid;
    }
}