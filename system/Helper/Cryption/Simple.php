<?php

class Helper_Cryption_Simple
{
    private static $_key = 'z6e7allz123';

    private static function _init(&$key, &$ivSize, &$iv)
    {
        $key    = $key ?: self::$_key;
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv     = mcrypt_create_iv($ivSize, MCRYPT_RAND);
    }

    public static function encrypt($orgText, $key = null)
    {
        self::_init($key, $ivSize, $iv);

        $result = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $orgText, MCRYPT_MODE_ECB, $iv));
        $result = str_replace(array('/', '+'), array('_', '-'), $result);

        return $result;
    }

    public static function decrypt($cryptText, $key = null)
    {
        self::_init($key, $ivSize, $iv);

        $result = str_replace(array('_', '-'), array('/', '+'), $cryptText);
        $result = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($result), MCRYPT_MODE_ECB, $iv);

        return trim($result);
    }
}