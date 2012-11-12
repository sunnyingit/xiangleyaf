<?php

class Model_User_Trait_Base extends Model_User_Trait_Abstract
{
    /**
     * 升级
     *
     * @return bool
     */
    public function levelUp()
    {
        // 已经是最大等级（没有下一级了）
        if (!$this->_user['next_level']) {
            return -1;
        }

        // 经验值还未到下一级别的升级线
        if (!$this->_user['exp'] < $this->_user['next_level']['exp']) {
            return -2;
        }

        $curLevel = Dao('Static_Level')->getLevelByExp($this->_user['exp']);
        if ($curLevel['level_id'] == $this->_user['level_id'])  {
            return -3;
        }

        if (!$this->_user->update('level_id', $curLevel['level_id'])) {
            return -4;
        }

        // 加满生命值、移动力、精力
        $this->_user->restore->full();

        return 200;
    }

    /**
     * 首次登陆、每日登陆奖励
     *
     * @return bool
     */
    public function loginReward()
    {

    }

    /**
     * 消耗金块
     *
     * @param int $num
     * @return bool
     */
    public function consumeGold($num)
    {

    }

    /**
     * 消耗银币
     *
     * @param int $num
     * @return bool
     */
    public function consumeSilver($num)
    {

    }

}