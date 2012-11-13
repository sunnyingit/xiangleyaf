<?php

/**
 * 航海纪年、季节
 *
 * @author JianJian <silverd@sohu.com>
 * $Id: Year.php 320 2012-11-13 03:03:03Z jiangjian $
 */

class MyHelper_Year
{
    public static function get()
    {
        $days   = max(1, ceil((time() - strtotime(VOYAGE_1ST_YEAR)) / 86400));
        $year   = str_pad(1 + floor($days / 4), 4, 0, STR_PAD_LEFT);
        $season = self::getSeason($days % 4);

        return array($year, $season);
    }

    public static function getSeason($season)
    {
        switch ($season) {
            case 1:
                return __('春天');
            case 2:
                return __('夏天');
            case 3:
                return __('秋天');
            case 0:
            default:
                return __('冬天');
        }
    }
}