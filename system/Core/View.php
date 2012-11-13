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

    private static $_instance;
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self(rtrim(TPL_PATH, DS));
        }
        return self::$_instance;
    }

    /**
     * Assign 传参
     *
     * @param string/array $key
     * @param mixed/null $value
     * @param mixed $value
     */
    public function assign($key, $value = null)
    {
        return $value === null
            ? parent::assign($key)
            : parent::assign($key, $value);
    }

    /**
     * 渲染输出模板
     *
     * @param string $tpl
     * @param array $data
     * @return false/string
     */
    public function display($tpl, $data = array())
    {
        if (strpos($tpl, TPL_EXT) === false) {
            $tpl .= TPL_EXT;
        }

        return parent::display($tpl, $data);
    }

    /**
     * 返回输出内容（不输出到屏幕）
     *
     * @param string $tpl
     * @param array $data
     * @return string
     */
    public function render($tpl, $data = array())
    {
        if (strpos($tpl, TPL_EXT) === false) {
            $tpl .= TPL_EXT;
        }

        return parent::render($tpl, $data);
    }

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
     * @param string $bodyContent
     * @param array $data
     * @param bool $return 是否仅返回（不输出到屏幕）
     * @return string
     */
    public function layout($bodyContent, $data = array(), $return = false)
    {
        $method = $return ? 'render' : 'display';

        // 加载布局
        $this->assign('bodyContent', $bodyContent);
        return $this->$method('_layout/' . $this->_layout, $data);
    }
}