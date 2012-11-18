<?php

/**
 * 敏感词过滤
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Cache.php 276 2012-11-01 06:03:20Z jiangjian $
 */

class Model_Censor extends Core_Model_Abstract
{
    private $_censor;

    public function __construct()
    {
        // TODO read from dao
        $keywords = array('毛泽东', '周恩来', '朱德');

        $this->_censor = new Com_Censor($keywords);
    }

    public function filter($word)
    {
        return $this->_censor->filter($word);
    }
}