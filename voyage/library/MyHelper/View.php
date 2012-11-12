<?php

/**
 * 视图中使用的相关助手方法
 *
 * @author JianJian <silverd@sohu.com>
 * $Id: View.php 113 2012-10-19 01:15:00Z jiangjian $
 */

class MyHelper_View
{
    /**
     * 玩家当前经验值，在他当前升级区间内，占多少百分比
     *
     * @return array
     */
    public static function getExpPercent($user)
    {
        // 最高等级
        if (!$user['next_level']) {
            return 0;
        }

        // 当前等级的经验值区间（最小->最大）
        $currentLevelExpRange = $user['next_level']['exp'] - $user['level']['exp'];

        if ($currentLevelExpRange <= 0) {
            return 0;
        }

        return round(($user['exp'] - $user['level']['exp']) / $currentLevelExpRange, 2);
    }

    /**
     * 获取经验值条的宽度
     *
     * @return int
     */
    public static function getExpWidth($user, $maxWidth)
    {
        return min($maxWidth, ceil(self::getExpPercent($user) * $maxWidth));
    }

    /**
     * 获取HP/精力/移动力条的宽度
     *
     * @return int
     */
    public static function getPropWidth($currentValue, $maxValue, $maxWidth)
    {
        return min($maxWidth, ceil(($currentValue / $maxValue) * $maxWidth));
    }
}