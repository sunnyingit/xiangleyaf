<?php

/**
 * 主界面控制器
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Index.php 320 2012-11-13 03:03:03Z jiangjian $
 */

class Controller_Index extends Controller_Abstract
{
    /**
     * 顶部用户信息
     */
    public function userAction()
    {
        // 直接渲染模板
    }

    /**
     * 主界面
     */
    public function indexAction()
    {
        if ($this->_user['status'] == 0) {

            $this->_inPort();   // 港口中
            $tpl = 'index/in_port';

        } elseif ($this->_user['status'] == 1) {

            $this->_sailing();  // 航行中
            $tpl = 'index/sailing';
        }

        $this->assign('tpl', $tpl);
    }

    /**
     * 主界面：港口中
     */
    private function _inPort()
    {
        // 当前港口信息
        $portInfo = Model('Static')->Port->get($this->_user['port_from']);

        $this->assign('portInfo', $portInfo);
    }

    /**
     * 主界面：航行中
     */
    private function _sailing()
    {
        // 倒计时：还要多久到达目的地
        $arriveTimeCountdown = $this->_user['arrive_time'] - $GLOBALS['_TIME'];

        // 已经驶达，跳转到目的港口
        if ($arriveTimeCountdown < 1) {
            $this->_user->sail->arrive();
            $this->redirect('/');
        }

        // 全程需航行多久
        $arriveTimeTotal = $this->_user['arrive_time'] - $this->_user['depart_time'];

        // 出发港口、目的港口信息
        $portFromInfo = Model('Static')->Port->get($this->_user['port_from']);
        $portToInfo   = Model('Static')->Port->get($this->_user['port_to']);

        $this->assign('portFromInfo',        $portFromInfo);
        $this->assign('portToInfo',          $portToInfo);
        $this->assign('arriveTimeCountdown', $arriveTimeCountdown);
        $this->assign('arriveTimeTotal',     $arriveTimeTotal);
    }

    /**
     * Ajax 自动更新用户属性
     */
    public function ajaxAttrAction()
    {
        $attr = $this->getx('attr');
        if (!in_array($attr, array('hp', 'move', 'energy'))) {
            exit('Access Denied');
        }

        // 重新读取一遍用户信息
        $this->_user = $this->_user->refresh();

        $this->json(array(
            $attr                   => $this->_user[$attr],
            $attr . '_max'          => $this->_user[$attr . '_max'],
            $attr . '_in_countdown' => $this->_user[$attr . '_in_next_time'] - $GLOBALS['_TIME'],
        ));
    }

    /**
     * Web 界面模式 For Dev/QA
     */
    public function appAction()
    {
        // 主窗体 URL
        $bodyUrl = $this->getx('url') ?: '/';
        $this->assign('bodyUrl', $bodyUrl);

        // 无布局
        $this->_view->setLayout(null);
    }
}