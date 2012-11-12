<?php

/**
 * Cli 控制器抽象父类
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Cli.php 229 2012-10-25 08:02:25Z jiangjian $
 */

abstract class Core_Controller_Cli extends Core_Controller_Abstract
{
    /**
     * 不加载视图（请勿修改）
     *
     * @var bool
     */
    public $yafAutoRender = false;
}