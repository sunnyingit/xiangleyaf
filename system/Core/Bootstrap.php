<?php

/**
 * 框架初始化
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Bootstrap.php 306 2012-11-12 05:30:45Z jiangjian $
 */

class Core_Bootstrap
{
    public static function init()
    {
        $self = new self();

        // 依次执行本类所有方法
        foreach (get_class_methods($self) as $method) {
            if ($method != __FUNCTION__) {  // 不是 init 方法本身
                $self->$method();
            }
        }
    }

    public function initGlobal()
    {
        // 定义路径常量
        if (! defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        define('TPL_PATH',      APP_PATH  . 'views'    . DS);
        define('DATA_PATH',     APP_PATH  . 'data'     . DS);
        define('LOG_PATH',      DATA_PATH . 'logs'     . DS);
        define('CACHE_PATH',    DATA_PATH . 'cache'    . DS);
        define('LOCALE_PATH',   DATA_PATH . 'locale'   . DS);
        define('RESOURCE_PATH', DATA_PATH . 'resource' . DS);
        define('THRIFT_PATH',   DATA_PATH . 'thrift'   . DS . 'gen-php' . DS);

        // 应用常量定义
        Yaf_Loader::import(APP_PATH . 'conf/constant.php');

        // 设置编码
        header('Content-type: text/html; charset=UTF-8');

        // 设置时区
        date_default_timezone_set(CUR_TIMEZONE);

        // 性能测试 - 程序开始执行时间、消耗内存
        $GLOBALS['_START_TIME'] = microtime(true);
        $GLOBALS['_START_MEM']  = memory_get_usage();
        $GLOBALS['_TIME']       = $_SERVER['REQUEST_TIME'];
        $GLOBALS['_DATE']       = date('Y-m-d H:i:s');
        $GLOBALS['_SQLs']       = array();

        // 全局常量、函数等
        Yaf_Loader::import(SYS_PATH . 'Function/Core.php');
        Yaf_Loader::getInstance()->registerLocalNamespace(array('Dao', 'MyHelper'));

        // 把配置保存起来
        Yaf_Registry::set('config', Yaf_Application::app()->getConfig());

        // 开启输出缓冲
        ob_start();
    }

    public function initLocale()
    {
        // 多语言版本（国际化设置）
        if (defined('CUR_LANG') && defined('CUR_TEXT_DOMAIN')) {

            // 设置环境变量
            putenv('LANG=' . CUR_LANG);
            putenv('LC_ALL=' . CUR_LANG);

            // 设置场景信息
            setlocale(LC_ALL, CUR_LANG);

            // 设置要绑定的语言包的目录
            bindtextdomain(CUR_TEXT_DOMAIN, LOCALE_PATH);
            bind_textdomain_codeset(CUR_TEXT_DOMAIN, 'UTF-8');

            // 设置默认的包
            textdomain(CUR_TEXT_DOMAIN);
        }
    }

    public function initDebugMode()
    {
        // 调试、错误信息开关
        if (isDebug()) {
            ini_set('display_errors', 'On');
            error_reporting(E_ALL | E_STRICT);
            if (! isset($_COOKIE['__debug'])) {
                setcookie('__debug', DEBUG_XKEY, null, '/');
            }
            // 打印调试信息
            if (PHP_SAPI !== 'cli') {   // 非命令行模式才输出调试信息
                Yaf_Loader::import(SYS_PATH . 'Core/Debug.php');
                Yaf_Loader::import(SYS_PATH . 'Third/FirePHPCore/fb.php');
                register_shutdown_function(array('Core_Debug', 'firePHP'));
            }
        } else {
            ini_set('display_errors', 'Off');
            error_reporting(0);
        }

        // 全局错误处理 TODO
        // set_error_handler(array('Core_Error', 'handler'));
    }

    public function initRequestUri()
    {
        // 将 XianglePHP 风格的 URI 转为 Yaf 风格
        // 例如：/index-test/hello-world => /indextest/helloworld
        $request    = Yaf_Dispatcher::getInstance()->getRequest();
        $requestUri = $request->getRequestUri();

        if (strpos($requestUri, '-') !== false) {
            $request->setRequestUri(str_replace('-', '', $requestUri));
        }
    }
}