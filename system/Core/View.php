<?php

/**
 * 模板引擎
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: View.php 165 2012-11-12 16:43:37Z silverd29@gmail.com $
 */

class Core_View extends Yaf_View_Simple
{
    /**
     * 当前布局
     *
     * @var string
     */
    private $_layout = 'default';

    /**
     * 设置布局
     *
     * @param string $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * 获取布局
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * 布局渲染
     *
     * @param string $tpl
     * @param array $data
     * @param bool $return 是否仅返回（不输出到屏幕）
     * @return string
     */
    public function layout($tpl, $data = array(), $return = false)
    {
        $method = $return ? 'render' : 'display';

        // 无布局
        if (null === $this->_layout) {
            return $this->$method($tpl, $data);
        }

        // 加载布局
        $this->assign('tplFile', $tpl);
        return $this->$method('_layout/' . $this->_layout, $data);
    }
}