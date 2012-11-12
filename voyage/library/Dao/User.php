<?php

class Dao_User extends Dao_User_Abstract
{
    protected $_tableName       = 'user';
    protected $_primaryKey      = 'uid';
    protected $_primaryKeyType  = 'bigint';
    protected $_nameField       = 'user_name';
}