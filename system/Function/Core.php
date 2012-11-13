<?php

/**
 * 核心函数库（修改请谨慎）
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Core.php 306 2012-11-12 05:30:45Z jiangjian $
 */

/**
 * 单例加载
 *
 * @param string $className
 * @return object
 */
function S($className)
{
    return Core_Loader::getSingleton($className);
}

/**
 * 加载模型
 *
 * @param string $name
 * @return object
 */
function Model($name)
{
    return S('Model_' . $name);
}

/**
 * 加载 Dao
 *
 * @param string $name
 * @return object
 */
function Dao($name)
{
    return S('Dao_' . $name);
}

/**
 * 国际化文本显示
 *
 * @param string $string
 * @param array $vars
 * @return string
 */
function __($string, $vars = null)
{
    if (!$vars) {
       return gettext($string);
    }

    $searchs = $replaces = array();

    foreach ((array) $vars as $key => $var) {
        $searchs[] = '{' . $key . '}';
        $replaces[] = $var;
    }

    return str_replace($searchs, $replaces, gettext($string));
}

/**
 * 包含模板
 *
 * @param string $tpl
 * @return string
 */
function template($tpl)
{
    return rtrim(Core_View::getInstance()->getScriptPath(), DS) . DS . $tpl . TPL_EXT;
}

/**
 * 404
 *
 * @return void
 */
function header404()
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    exit('404 Not Found');
}

/**
 * 500
 *
 * @return void
 */
function header500()
{
    header("HTTP/1.0 500 Internal Server Error");
    header("Status: 500 Internal Server Error");
    exit('500 Internal Server Error');
}

/**
 * 遍历 addslashes
 *
 * @param mixed $data
 * @return mixed
 */
function saddslashes($data)
{
    return is_array($data) ? array_map(__FUNCTION__, $data) : addslashes($data);
}

/**
 * 整形化
 *
 * @param bigint $num
 * @return bigint
 */
function xintval($num)
{
    return preg_match('/^\-?[0-9]+$/', $num) ? $num : 0;
}

/**
 * 浮点数
 *
 * @param int/float $val
 * @param int $precision
 * @return float
 */
function decimal($val, $precision = 0)
{
    if ((float) $val) {
        $val = round((float) $val, (int) $precision);
        list($a, $b) = explode('.', $val);
        if (strlen($b) < $precision) {
            $b = str_pad($b, $precision, '0', STR_PAD_RIGHT);
        }
        return $precision ? "$a.$b" : $a;
    }

    return $val;
}

/**
 * 逗号连接
 *
 * @param array $array
 * @return string
 */
function ximplode($array)
{
    return empty($array) ? 0 : "'" . implode("','", is_array($array) ? $array : array($array)) . "'";
}

/**
 * 是否调试模式
 *
 * @return bool
 */
function isDebug()
{
    !defined('DEBUG_XKEY') && define('DEBUG_XKEY', 'xianglephp');
    return ((defined('DEBUG_MODE') && DEBUG_MODE) || (isset($_REQUEST['debug']) && $_REQUEST['debug'] == DEBUG_XKEY));
}

/**
 * 格式化时间戳(秒)
 *
 * @param int $timestamp
 * @param string $format
 * @return string
 */
function datex($timestamp, $format = 'Y-m-d H:i')
{
    return date($format, $timestamp);
}

/**
 * 数组转为对象
 *
 * @param array $e
 * @return object
 */
function arrayToObject($e)
{
    if (!is_array($e)) {
        return $e;
    }

    return (object) array_map(__FUNCTION__, $e);
}

/**
 * 对象转为数组
 *
 * @param object $e
 * @return array
 */
function objectToArray($e)
{
    if (is_object($e)) {
        $e = (array) $e;
    }

    if (!is_array($e)) {
        return $e;
    }

    return (array) array_map(__FUNCTION__, $e);
}

function vd($s, $exit = 1)
{
    echo '<pre>';
    var_dump($s);
    echo '</pre>';
    $exit && exit();
}

function pr($s, $exit = 1)
{
    echo '<pre>';
    print_r($s);
    echo '</pre>';
    $exit && exit();
}