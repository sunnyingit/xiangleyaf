<?php

/**
 * 交易所
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Exchange.php 282 2012-11-05 01:23:56Z jiangjian $
 */

class Controller_Exchange extends Controller_Abstract
{
    public function init()
    {
        parent::init();

        // 必须为港口状态
        $this->_user->assertInPort();
    }

    public function indexAction()
    {

    }
}
