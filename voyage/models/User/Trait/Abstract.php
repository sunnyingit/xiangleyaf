<?php

class Model_User_Trait_Abstract
{
    protected $_user;

    public function __construct(Model_User $user)
    {
        $this->_user = $user;
    }
}