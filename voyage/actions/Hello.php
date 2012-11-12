<?php

class Action_Hello extends Yaf_Action_Abstract
{
    public function execute()
    {
        var_dump($this->getController()->_good);
        return false;
    }
}