<?php

/**
 * 酒馆
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Tavern.php 309 2012-11-12 06:01:52Z jiangjian $
 */

class Controller_Tavern extends Controller_Abstract
{
    public function init()
    {
        parent::init();

        // 必须为港口状态
        $this->_user->assertInPort();
    }

    public function indexAction()
    {
        // 我的酒馆信息
        $myTavernInfo = $this->_user->tavern->get();

        // 本次刷出的船长列表（其中会引用修改 $myTavernInfo）
        $captainList = $this->_user->tavern->current($myTavernInfo);

        // 我的船长ids
        $myCaptainIds = array_flip($this->_user->captain->getIds());

        $this->assign('myTavernInfo', $myTavernInfo);
        $this->assign('captainList',  $captainList);
        $this->assign('myCaptainIds', $myCaptainIds);
    }

    public function refreshAction()
    {
        // 需消耗多少金块
        $needGold = 5;

        if ($this->_user['gold'] < $needGold) {
            throw new Core_Exception_Logic(__('您的金块不足 {need_golds}', array('need_golds' => $needGold)));
        }

        // 扣除金块
        $this->_user->base->consumeBullion($needGold);

        // 我的酒馆信息
        $myTavernInfo = $this->_user->tavern->get();

        // 执行刷新
        $this->_user->tavern->refresh($myTavernInfo, true);

        $this->redirect('/tavern');
    }

    // http://local.voyage/tavern/my-captains
    public function myCaptainsAction()
    {
        $myCaptainList = $this->_user->captain->getList();

        $sort = array(
            'intelligence' => SORT_DESC,
            'force' => SORT_ASC,
        );
        $myCaptainList = Helper_Array::multiSort($myCaptainList, $sort);

        pr($myCaptainList);
    }

    public function hireAction()
    {
        $captainId = $this->getInt('captain_id');

        $this->_user->captain->hire($captainId);

        $this->redirect('/tavern');
    }

    public function dismissAction()
    {
        $captainId = $this->getInt('captain_id');

        $this->_user->captain->dismiss($captainId);

        $this->redirect('/tavern');
    }
}
