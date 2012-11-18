<?php

/**
 * 静态资源模型
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Cache.php 276 2012-11-01 06:03:20Z jiangjian $
 */

class Model_Static
{
    public function __get($var)
    {
        return Dao('Static_' . ucfirst($var));
    }
}