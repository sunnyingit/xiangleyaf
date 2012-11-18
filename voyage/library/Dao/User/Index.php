<?php

class Dao_User_Index extends Dao_User_Abstract
{
    protected $_dbName    = 'voyage_share';
    protected $_nameField = 'user_name';

    public function getDbSuffix($uid)
    {
        $dbSuffix = $this->oneByPk('db_suffix', $uid);

        if (! $dbSuffix) {
            throw new Core_Exception_Logic(__('用户 (UID: {uid}) 不存在或已被禁用，请联系管理员', array('uid' => $uid)));
        }

        return $dbSuffix;
    }

    public function getUidByToken($userToken)
    {
        return $this->one('id', array('user_token' => $userToken));
    }

    public function getUidByCode($userCode)
    {
        return $this->one('id', array('user_code' => $userCode));
    }

    public function getUidByAccount($userAccount)
    {
        return $this->one('id', array('user_account' => $userAccount));
    }

    public function getUserByAccount($userAccount)
    {
        return $this->row(array('user_account' => $userAccount));
    }

    public function updateUserToken($uid, $userToken)
    {
        return $this->updateByPk(array('user_token' => $userToken), $uid);
    }
}