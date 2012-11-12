<?php

/**
 * 队列接口、方法定义
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Interface.php 305 2012-11-12 01:17:12Z jiangjian $
 */

interface Com_Queue_Interface
{
    public function push($value);

    public function pop();

    public function count();

    public function clear();

    public function view();
}