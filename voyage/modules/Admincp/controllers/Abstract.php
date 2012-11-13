<?php

class Controller_Abstract extends Core_Controller_Web
{
    public function init()
    {
        parent::init();
        echo 'init!!';
    }
}