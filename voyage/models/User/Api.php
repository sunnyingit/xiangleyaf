<?php

class Model_User_Api extends Core_Model_Abstract
{
    public function register($userToken)
    {
        // 联盟号
        $userCode = $this->_getUserCode();

        // demo
        $userName = Helper_Array::rand(array(
            '阿诺·施瓦辛格',
            '阿伦·艾弗森',
            '妮可·罗宾',
            '丹尼斯·罗德曼',
            '迈克·杰克逊',
            '索隆·罗密欧',
            '康桑·思密达'
        )) . rand(1,1000);

        // demo
        $nationId = rand(1, 4);

        // 用户索引表
        $setArr = array(
            'user_token'   => $userToken,
            'user_code'    => $userCode,
            'user_name'    => $userName,
            'user_account' => '',
            'db_suffix'    => 0,
            'level_id'     => 1,
            'position_id'  => 1,
            'nation_id'    => $nationId,
        );
        $uid = Dao('User_Index')->insert($setArr);

        if ($uid < 1) {
            throw new Core_Exception_SQL(__('注册用户失败，请联系管理员'));
        }

        // 用户库后缀
        Dao('User_Index')->updateByPk(array('db_suffix' => $this->_getDbSuffixForNewUser($uid)), $uid);

        // 用户基本信息
        $setArr = array(
            'uid'         => $uid,
            'user_code'   => $userCode,
            'user_name'   => $userName,
            'create_time' => $GLOBALS['_DATE'],
            'silver'      => 10000,
            'gold'        => 600,
            'hp'          => 100,
            'hp_max'      => 100,
            'move'        => 200,
            'move_max'    => 200,
            'energy'      => 3,
            'energy_max'  => 3,
            'exp'         => 0,
            'level_id'    => 1,
            'position_id' => 0,
            'nation_id'   => $nationId,
            'avatar_id'   => 1,
            'status'      => 0,
            'port_from'   => 1, // 默认港口
        );
        Dao('User')->loadDs($uid)->insert($setArr);

        // 战斗区间索引
        $setArr = array(
            'uid'              => $uid,
            'hp'               => 100,
            'level_id'         => 1,
            'nation_id'        => $nationId,
            'last_active_time' => $GLOBALS['_TIME'],
        );
        Dao('Battle_Block')->insert($setArr);

        return $uid;
    }

    /**
     * 新用户使用的分库
     *
     * @return int
     */
    private function _getDbSuffixForNewUser($uid)
    {
        $suffixes = explode(',', DB_SUFFIX_NEW_USER);
        return Helper_Array::rand($suffixes, 1);
    }

    /**
     * 生成新的联盟号
     *
     * @return string
     */
    private function _getUserCode()
    {
        do {
            $sourceStr = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
            $userCode = Helper_String::random(6, false, $sourceStr);
            if (! Dao('User_Index')->getUidByCode($userCode)) {
                return $userCode;
            }
        } while (true);
    }

    /**
     * 处理手机端参数
     *
     * @param array $data
                version 客户端版本
                udid    手机Imei码
                hashCode    mac经加密后形成的字符串
                sn  手机系统
                model   手机型号
                sv  手机系统版本
                pay  支付方式
                width   手机像素宽
                height  手机像素高
                timestamp   时间戳
                deviceToken 通知令牌
                source  下载渠道
     * @return array
     */
    public function initMobileParams($data)
    {
        if (! $data) {
            return false;
        }

        $mobileParams = array();

        foreach(array(
            'version', 'udid', 'hashCode', 'sn', 'model', 'sv',
            'pay', 'width', 'height', 'timestamp', 'deviceToken', 'source'
        ) as $key) {
            $mobileParams[$key] = isset($data[$key]) ? $data[$key] : '';
        }

        // 自适应缩放比例
        if (isset($mobileParams['width']) && $mobileParams['width']) {
            $scales = array(
                540 => '1.12',
                720 => '1.13',
                600 => '1.25',
                800 => '1.25',
            );
            $this->_cookie['m_scale'] = isset($scales[$mobileParams['width']]) ? $scales[$mobileParams['width']] : 1;
            $this->_cookie['m_width'] = $mobileParams['width'];
        } elseif (! isset($this->_cookie['m_scale'])) {
            $this->_cookie['m_scale'] = 1;
        }

        // 手机类型 iPhone/WindowsPhone/Others
        if ($mobileParams['sn']) {
            $this->_cookie['m_type'] = $mobileParams['sn'];
        }

        // 手机系统版本
        if ($mobileParams['sv']) {
            $this->_cookie['m_sv'] = $mobileParams['sv'];
        }

        return $mobileParams;
    }
}