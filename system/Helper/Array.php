<?php

/**
 * 数组处理函数
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Array.php 293 2012-11-06 01:41:03Z jiangjian $
 */

class Helper_Array
{
    /**
     * 将一维数组变成奇偶键值关联数组
     * Example: array(a, b, c, d) => array(a => b, c => d)
     *
     * @param array $array
     * @param array $array
     *
     * @return bool
     */
    public static function assoc(array $array)
    {
        $return = array();

        if ($array) {
            $count  = count($array);
            for ($i = 0; $i < $count; $i+=2) {
                $return[$array[$i]] = ($i + 1 < $count) ? $array[$i + 1] : null;
            }
        }

        return $return;
    }

    /**
     * 深度遍历删除空元素
     *
     * @param array $data
     *
     * @return bool
     */
    public static function filter($data)
    {
        foreach ($data as $key => $value) {
            if (! $value) {
                unset($data[$key]);
            } elseif (is_array($value)) {
                $data[$key] = self::filter($value);
            }
        }

        return $data;
    }

    /**
     * 在多维数组中搜索值是否存在
     *
     * @param mixed $find
     * @param array $multiArray
     *
     * @return bool
     */
    public static function inMultiArray($find, $multiArray)
    {
        $isFound = false;
        if (is_array($multiArray)) {
            foreach ($multiArray as $key => $val) {
                if (is_array($val)) {
                    $isFound = self::inMultiArray($find, $val);
                } else {
                    if ($find == $val) {
                        $isFound = true;
                    }
                }
                if ($isFound) {
                    break;
                }
            }
            return $isFound;
        }
        return false;
    }

    /**
     * 取出数组某列
     *
     * @param array $array
     * @param string $key
     * @return array
     */
    public static function fetchCol($array, $field)
    {
        $result = array();

        foreach ($array as $value) {
            $result[] = $value[$field];
        }

        return $result;
    }

    /**
     * 随机数组中的元素
     *
     * @param array $array
     * @param int $rndNum 取几个
     * @return mixed
     */
    public static function rand($array, $rndNum = 1)
    {
        if (! $array || ! is_array($array)) {
            return false;
        }

        if (count($array) < $rndNum) {
            return $array;
        }

        // 随机一个
        if ($rndNum == 1) {
            $randKey = array_rand($array);
            return $array[$randKey];
        }

        // 随机多个
        $result = array();
        $randKeys = array_rand($array, $rndNum);
        foreach ($randKeys as $randKey) {
            $result[] = $array[$randKey];
        }

        return $result;
    }

    /**
     * 将数组 value 中的某一个字段的值，赋给该数组的 key
     *
     * @param array $array
     * @param string $field
     * @return array
     */
    public static function indexField($array, $field)
    {
        $result = array();

        foreach ($array as $key => $value) {
            $result[$value[$field]] = $value;
        }

        return $result;
    }

    /**
     * 二维数组排序（可按多字段排序）
     *
     * @param array &$array
     * @param array $sortFields
     *                  $field1 => SORT_DESC,
     *                  $field2 => SORT_ASC,
     * @return array
     */
    public static function multiSort(&$array, $sortFields)
    {
        // 准备索引
        foreach ($array as $key => $value) {
            foreach ($sortFields as $sortField => $order) {
                ${$sortField}[$key] = $value[$sortField];
            }
        }

        // 组合参数
        $args = array();
        foreach ($sortFields as $sortField => $order) {
            $args[] = ${$sortField};
            $args[] = $order;
        }

        // 把 $array 作为最后一个参数，以通用键排序
        $args[] = &$array;

        call_user_func_array('array_multisort', $args);
        return $array;
    }
}