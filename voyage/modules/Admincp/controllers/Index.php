<?php

class Controller_Index extends Yaf_Controller_Abstract
{
    public function indexAction()
    {
        echo 'you are in Modules/Admincp/Controller_Index::indexAction()';

        return false;
    }

    public function forwardAction()
    {
        $this->forward('Index', 'index', 'good');
        echo 'goodboy';

        return false;
    }
}