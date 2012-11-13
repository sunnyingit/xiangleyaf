<?php

class Controller_Queue extends Core_Controller_Web
{
    public $yafAutoRender = false;

    public function redisQAction()
    {
        $q = Com_Queue::getInstance('queue1');
        $q->push('a');
        $q->push('b');
        $q->push('c');
        $q->push('d');
    }

    public function redisQPopAction()
    {
        $q = Com_Queue::getInstance('queue1');
        var_dump($q->pop());
    }

    public function redisQCountAction()
    {
        $q = Com_Queue::getInstance('queue1');
        var_dump($q->view());
    }

    public function memcacheQAction()
    {
        $q = Com_Queue::getInstance('queue2');
        $q->push('a');
        $q->push('b');
        $q->push('c');
        $q->push('d');
    }

    public function memcacheQPopAction()
    {
        $q = Com_Queue::getInstance('queue2');
        var_dump($q->pop());
    }
}