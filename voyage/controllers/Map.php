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

        $seaAreaList = Model('Static')->SeaArea->all();
        pr($seaAreaList);
    }

    /**
     * 指定海域下的港口列表
     */
    public function portListAction()
    {
        $seaAreaId = $this->getInt('sea_area_id');
        if (!$seaAreaId) {
            exit('Invalid SeaAreaId');
        }

        $seaAreaRow = Model('Static')->SeaArea->get($seaAreaId);
        if (!$seaAreaRow) {
            exit('Invalid SeaAreaRow');
        }

        if ($this->_user['level_id'] < $seaAreaRow['unlock_level']) {
            exit('等级不够解锁该海域');
        }

        // 港口列表
        $portList = Model('Static')->Port->find(array('sea_area_id' => $seaAreaId));

        $this->json($portList);

        return false;
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
            $portTo = Model('Static')->Port->one('port_id', "port_id != '{$this->_user['port_from']}'", 'RAND()');
        }

        if (!$portTo) {
            exit('Invalid portTargetId');
        }

        if ($portTo == $this->_user['port_from']) {
            exit('目的港口不能和你当前所在港口相同');
        }

        $portRow = Model('Static')->Port->getFull($portTo);
        if (!$portRow) {
            exit('Invalid PortRow');
        }

        if ($portRow['sea_area']['unlock_level'] > $this->_user['level']) {
            exit('等级不够解锁该海域');
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
        $goldNum = 5;

        if ($this->_user['gold'] < $goldNum) {
            exit('您的金块不足');
        }

        // 扣除金块
        $this->_user->consumeGold($goldNum);

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