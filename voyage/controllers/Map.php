<?php

class Controller_Map extends Controller_Abstract
{
    /**
     * 海域列表
     */
    public function indexAction()
    {
        // demo
        $this->redirect('/map/depart/?port_to=random');
    }

    /**
     * 检测目的港口合法性
     */
    private function _checkPortTarget(&$portTo)
    {
        if ($this->_user['status'] != 0) {
            $this->redirect('/');
        }

        $portTo = $this->getInt('port_to');

        // demo 随机找个目的港口
        if ($portTo == 'random') {
            $portTo = Model('Static')->Port->one('id', "id != '{$this->_user['port_from']}'", 'RAND()');
        }

        if (! $portTo) {
            exit('Invalid portTargetId');
        }

        if ($portTo == $this->_user['port_from']) {
            exit('目的港口不能和你当前所在港口相同');
        }

        $portRow = Model('Static')->Port->get($portTo);
        if (! $portRow) {
            exit('Invalid PortRow');
        }
    }

    /**
     * 扬帆起航
     */
    public function departAction()
    {
        // 检测目的港口合法性
        $this->_checkPortTarget($portTo);

        // 扬帆起航
        $this->_user->sail->depart($portTo);

        // 增加航行记录
        $this->_user->sail->log($portTo);

        $this->redirect('/');

        return false;
    }

    /**
     * 瞬间移动到目的港口（消费金块）
     */
    public function instantMoveAction()
    {
        // 检测目的港口合法性
        $this->_checkPortTarget($portTo);

        // 需消耗多少金块
        $needGold = 5;

        if ($this->_user['gold'] < $needGold) {
            exit('您的金块不足');
        }

        // 扣除金块
        $this->_user->consumeGold($needGold);

        // 扬帆起航
        $this->_user->sail->depart($portTo);

        // 瞬间到达
        $this->_user->sail->arrive();

        // 增加航行记录
        $this->_user->sail->log($portTo, 1);

        $this->redirect('/');

        return false;
    }
}