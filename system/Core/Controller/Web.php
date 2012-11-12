<?php

/**
 * 网页控制器抽象父类
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Web.php 303 2012-11-08 03:30:25Z jiangjian $
 */

abstract class Core_Controller_Web extends Core_Controller_Abstract
{
    /**
     * 默认的布局
     *
     * @var string
     */
    public $layout = 'default';

    /**
     * 自动加载视图
     *
     * @var bool
     */
    public $yafAutoRender = true;

    /**
     * 自动加载视图
     *
     * @var bool
     */
    public $autoRender = true;

    public function assign($key, $value = null, $isGlobal = false)
    {
        // 全局变量
        if ($isGlobal) {
            $GLOBALS[$key] = $value;
        }

        return $this->_view->assign($key, $value);
    }

    public function alert($msg, $res = 'success', $url = '', $extra = '')
    {
        if (is_array($msg)) {
            $msg = implode('\n', $msg);
        }

        // ajax
        if ($this->isAjax()) {
            $this->jsonx($msg, $res, '', true);
        }

        // 跳转链接
        if ($url == 'halt') {
            $jumpStr = '';
        } else {
            $url = $url ? $url : $this->refer();
            $url = $url ? $url : '/';
            $jumpStr = $url ? "top.location.href = '{$url}';" : '';
        }

        $this->js("top.alert('{$msg}'); {$extra} {$jumpStr}");
    }

    public function js($script, $exit = true)
    {
        echo('<script type="text/javascript">' . $script . '</script>');
        $exit && exit();
    }

    public function json($output)
    {
        header('Content-type: text/json');
        header('Content-type: application/json; charset=UTF-8');
        exit(json_encode($output));
    }

    /**
     * 用于 AJAX 响应输出 JSON
     *
     * @param string $msg
     * @param string $resultType success|error|warnings|tips
     * @param string $extra
     * @param bool $obClean 是否先清除之前的缓冲区
     */
    public function jsonx($msg, $resultType = 'success', $extra = '', $obClean = true)
    {
        // 清除之前的缓冲区，防止多余输出
        $obClean && ob_clean();

        $this->json(array('msg' => $msg, 'res' => $resultType, 'extra' => $extra));
    }

    public function jump($url = '')
    {
        $url = $url ? $url : $_SERVER['HTTP_REFERER'];
        $this->js('top.location.href = \'' . $url . '\';');
    }

    public function redirect($url = '')
    {
        $url = $url ? $url : $_SERVER['HTTP_REFERER'];
        header('Location: ' . $url);
        exit();
    }

    public function refer()
    {
        $refer = $this->getx('refer');
        return $refer ? $refer : $_SERVER['HTTP_REFERER'];
    }
}