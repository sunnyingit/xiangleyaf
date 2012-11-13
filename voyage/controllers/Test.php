<?php

class Controller_Test extends Yaf_Controller_Abstract
{
    public function indexAction()
    {
        echo 'hello text';

        return false;
    }
}