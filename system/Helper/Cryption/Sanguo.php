<?php

class Helper_Cryption_Sanguo
{
    private $_key = 'z6e7allz123eb728silverdz5a4561tf';

    public function __construct($key)
    {
        $this->_key = $key;
    }

    public function encrypt($text)
    {
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv     = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $key    = $this->_getBaseKey();

        $cryptText = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, '0429' . $text, MCRYPT_MODE_ECB, $iv));
        return str_replace(array('/', '+'), array('_', '-'), $cryptText);
    }

    public function decrypt($cryptText)
    {
        $cryptText = str_replace(array('_', '-'), array('/', '+'), $cryptText);

        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv     = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $key    = $this->_getBaseKey();
        $txt    = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, , base64_decode($cryptText), MCRYPT_MODE_ECB, $iv);

        if (!empty($txt) && substr($txt, 0, 4) == '0429') {
            return trim(substr($txt, 4, strlen($txt) - 4));
        }

        return null;
    }

    public function batchEncryptIds($list, $idField)
    {
        if (!$list) {
            return array();
        }

        foreach ($list as &$value) {
            $value[$idField] = $this->encrypt($value[$idField]);
        }

        return $list;
    }

    private function _getBaseKey()
    {
        return 'Voyage:' . date('md') . ':' . $this->_key;
    }
}