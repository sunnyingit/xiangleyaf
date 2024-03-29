<?php

/**
 * 写文件日志
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Log.php 5 2012-11-15 07:57:07Z jiangjian $
 */

class Com_Log
{
    /**
     * 写日志
     *
     * @param string $fileName
     * @param string $content
     * @param bool $withDatetime 行首增加时间前缀
     */
    public static function write($fileName, $content, $withDatetime = true)
    {
        $fileDir = LOG_PATH . date('Y-m-d') . DIRECTORY_SEPARATOR;
        if (! is_dir($fileDir)) {
            @mkdir($fileDir, 0755, true);
        }

        // 行首增加时间前缀
        if ($withDatetime) {
            $content = '[' . date('Y-m-d H:i:s') . '] ' . $content;
        }

        @file_put_contents($fileDir . $fileName . '.log', $content . PHP_EOL, FILE_APPEND);
    }
}