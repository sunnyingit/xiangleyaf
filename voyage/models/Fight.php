<?php

/**
 * 战斗模型
 *
 * @author JiangJian <silverd@sohu.com>
 * $Id: Fight.php 317 2012-11-12 07:46:46Z jiangjian $
 */

class Model_Fight extends Core_Model_Abstract
{
    const
        MAX_ROUNDS      = 20,   // 最多几个回合
        WIN             = 1,    // 胜利
        LOSE            = -1,   // 失败
        DRAW            = 0,    // 平手
        USER_MIN_HP     = 25,
        USER_MIN_ENERGY = 1;

    private $_self;
    private $_enemy;
    private $_selfShips   = array();
    private $_enemyShips  = array();
    private $_fightResult = 0;

    /**
     * 初始化一场战斗
     *
     * @param Model_User $self
     * @param Model_User $enemy
     */
    public function __construct(Model_User $self, Model_User $enemy)
    {
        if (!$self || !$enemy) {
            throw new Core_Exception_Logic(__('战斗初始化失败：用户信息不存在'));
        }

        $this->_self  = $self;
        $this->_enemy = $enemy;

        // 前提检测
        $this->_initCheck();

        // 战斗记录器
        $this->_recorder = new Model_Fight_Recorder();
        $this->_recorder->init($this->_self, $this->_enemy);

        // 船只初始化
        $this->_initShips();
    }

    private function _initCheck()
    {
        $this->_self->assertSailing(__('你尚未出海，无法参与战斗'));
        // $this->_enemy->assertSailing(__('对方尚未出海，无法参与战斗'));

        if ($this->_self['hp'] < self::USER_MIN_HP) {
            throw new Core_Exception_Logic(__('你的生命值太低，无法参与战斗'));
        }

        if ($this->_enemy['hp'] < self::USER_MIN_HP) {
            // throw new Core_Exception_Logic(__('对方生命值太低，无法参与战斗'));
        }

        if ($this->_self['energy'] < self::USER_MIN_ENERGY) {
            throw new Core_Exception_Logic(__('你的精力不足，无法参与战斗'));
        }

        // TODO 判断对方是否在自己的战斗区间内
    }

    /**
     * 初始化、计算双方的船只属性值（含Buff）
     */
    private function _initShips()
    {
        // 我方舰船信息
        $this->_selfShips = $this->_self->ship->getList();
        if (!$this->_selfShips) {
            throw new Core_Exception_Logic(__('你没有任何舰船，无法战斗'));
        }

        // 敌方舰船信息
        $this->_enemyShips = $this->_enemy->ship->getList();
        if (!$this->_enemyShips) {
            throw new Core_Exception_Logic(__('对方没有任何舰船，无法战斗'));
        }

        // TODO buff
        foreach ($this->_selfShips as &$ship) {
        }

        // TODO buff
        foreach ($this->_enemyShips as &$ship) {
        }

        // 随机布阵
        // 注：打散后key会从0开始重排
        shuffle($this->_selfShips);
        shuffle($this->_enemyShips);

        // 记录战前双方阵列
        $this->_recorder->setShips($this->_selfShips, $this->_enemyShips);
    }

    /**
     * 战斗进行（外观模式）
     *
     * @return const
     */
    public function process()
    {
        $this->_fightResult = $this->_fight();

        $this->_recorder->setFightResult($this->_fightResult);

        return $this->_fightResult;
    }

    /**
     * 战斗进行
     *
     * @return const Model_Fight::WIN/LOSE/DRAW
     */
    private function _fight()
    {
        $selfAimTarget  = 0; // 我方瞄准攻击的目标（默认是敌方的第1艘船）
        $enemyAimTarget = 0; // 敌方瞄准攻击的目标（默认是我方的第1艘船）

        $shipCount = max(count($this->_selfShips), count($this->_enemyShips));

        // 循环N个回合
        for ($round = 1; $round <= self::MAX_ROUNDS; $round++) {

            // 每艘船交错轮流开火
            for ($shipNo = 0; $shipNo < $shipCount; $shipNo++) {

                // 我船攻击敌船
                if (isset($this->_selfShips[$shipNo])) {

                    // 敌船被击沉，我方攻击目标转移到下一艘
                    if (!isset($this->_enemyShips[$selfAimTarget])) {
                        // 我方胜利：没有攻击目标了（敌船全被击沉）
                        if (++$selfAimTarget >= $shipCount) {
                            return self::WIN;
                        }
                    }

                    // 我船开火攻击
                    $this->_fire($this->_selfShips[$shipNo], $this->_enemyShips[$selfAimTarget], 'self', $round);
                }

                // 敌船攻击我船
                if (isset($this->_enemyShips[$shipNo])) {

                    // 我船被击沉，敌方攻击目标转移到下一艘
                    if (!isset($this->_selfShips[$enemyAimTarget])) {
                        // 敌方胜利：没有攻击目标了（我船全被击沉）
                        if (++$enemyAimTarget >= $shipCount) {
                            return self::LOSE;   // 敌方的胜利，即我方的失败
                        }
                    }

                    // 敌船开火攻击
                    $this->_fire($this->_enemyShips[$shipNo], $this->_selfShips[$enemyAimTarget], 'enemy', $round);
                }
            }

            // 我方胜利：敌方船全被击沉
            if (count($this->_enemyShips) < 1) {
                return self::WIN;
            }

            // 我方失败：我方船全被击沉
            if (count($this->_selfShips) < 1) {
                return self::LOSE;
            }
        }

        // 双方战平：N回合内仍未见胜负
        return self::DRAW;
    }

    /**
     * 单次开炮攻击
     *
     * @param array &$attackerShip
     * @param array &$defenderShip
     * @param string $attackerSide 发起进攻者 self|enemy 用于区分战斗记录文案、标明攻击方向（我方在左，对方在右）
     * @param int $round 第几回合
     * @return void
     */
    private function _fire(&$attackerShip, &$defenderShip, $attackerSide, $round)
    {
        // 对方被伤害（至少扣1滴血）
        $defenderShipHpBefore = $defenderShip['hp'];
        $damage               = max(1, $attackerShip['attack'] - $defenderShip['defense']);
        $defenderShip['hp']   = max(0, $defenderShipHpBefore - $damage);

        // 防守者是谁
        $defenderSide = $attackerSide == 'self' ? 'enemy' : 'self';

        // 增加一条开炮攻击记录
        $this->_recorder->add($round, array(

            // 我看到的
            'self' => array(

                'round'          => $round,         // 第几回合
                'damage'         => $damage,        // 守船受伤害点数
                'attacker_side'  => $attackerSide,  // 攻击者是谁：self|enemy
                'defender_side'  => $defenderSide,  // 防守者是谁：self|enemy

                // 详细文字
                'message' => $this->_getSideFireText($attackerSide, $attackerShip, $defenderShip, $damage),

                // 攻船信息
                'att_ship'       => array(
                    'ship_id'    => $attackerShip['ship_id'],
                    'captain_id' => $attackerShip['captain_id'],
                    'hp'         => $attackerShip['hp'],
                ),

                // 守船信息
                'def_ship'       => array(
                    'ship_id'    => $defenderShip['ship_id'],
                    'captain_id' => $attackerShip['captain_id'],
                    'hp'         => $defenderShip['hp'],
                    'hp_before'  => $defenderShipHpBefore,
                ),
            ),

            // 对方看到的
            'enemy' => array(

                'round'          => $round,         // 第几回合
                'damage'         => $damage,        // 守船受伤害点数
                'attacker_side'  => $defenderSide,  // 攻击者是谁：self|enemy
                'defender_side'  => $attackerSide,  // 防守者是谁：self|enemy

                // 详细文字
                'message' => $this->_getSideFireText($defenderSide, $attackerShip, $defenderShip, $damage),

                // 攻船信息
                'att_ship'       => array(
                    'ship_id'    => $attackerShip['ship_id'],
                    'captain_id' => $attackerShip['captain_id'],
                    'hp'         => $attackerShip['hp'],
                ),

                // 守船信息
                'def_ship'       => array(
                    'ship_id'    => $defenderShip['ship_id'],
                    'captain_id' => $attackerShip['captain_id'],
                    'hp'         => $defenderShip['hp'],
                    'hp_before'  => $defenderShipHpBefore,
                ),
            ),
        ));

        // 守船被击沉
        if ($defenderShip['hp'] < 1) {
            $defenderShip = null;   // 这里不能用 unset，因为需要在数组中保留该下标
        }
    }

    /**
     * 单次开炮攻击 - 获取具体文案
     *
     * @param string $attackerSide self|enemy
     * @param string $attackerShip
     * @param string $defenderShip
     * @param string $damage
     * @return string
     */
    private function _getSideFireText($attackerSide, $attackerShip, $defenderShip, $damage)
    {
        // 我方是攻击方
        if ($attackerSide == 'self') {

            // 基本文案
            $message = __('{attacker_ship}攻击{defender_ship}，造成{damage}点伤害。', array(
                'attacker_ship' => '<span class="green">' . __('我方') . $attackerShip['ship_name'] . '</span>',
                'defender_ship' => '<span class="blue">'  . __('敌方') . $defenderShip['ship_name'] . '</span>',
                'damage'        => '<span class="red">'   . $damage    . '</span>',
            ));

            // 附加文案 - 守船被击沉
            if ($defenderShip['hp'] < 1) {
                $message .= __('并击沉了{defender_ship}。', array(
                    'defender_ship' => '<span class="blue">' . __('敌方') . $defenderShip['ship_name'] . '</span>',
                ));
            }

        // 我方是防守方
        } else {

            // 基本文案
            $message = __('{attacker_ship}攻击{defender_ship}，造成{damage}点伤害。', array(
                'attacker_ship' => '<span class="blue">'  . __('敌方') . $attackerShip['ship_name'] . '</span>',
                'defender_ship' => '<span class="green">' . __('我方') . $defenderShip['ship_name'] . '</span>',
                'damage'        => '<span class="red">'   . $damage    . '</span>',
            ));

            // 附加文案 - 守船被击沉
            if ($defenderShip['hp'] < 1) {
                $message .= __('并击沉了{defender_ship}。', array(
                    'defender_ship' => '<span class="green">' . __('我方') . $defenderShip['ship_name'] . '</span>',
                ));
            }
        }

        return $message;
    }

    /**
     * 战后相关事宜处理
     * 例如：减生命值、加金钱、扣属性等
     *
     * @return string 最终战果影响文字
     */
    public function after()
    {
        // 战果提示文字
        $fightResustMsg = array(
            'self'  => '',
            'enemy' => '',
        );

        // 准备更新数组
        $setArrSelf = $setArrEnemy = array();

        // 扣精力
        $setArrSelf['energy'] = array('-', 1, 0);

        // 加经验
        $setArrSelf['exp'] = array('+', rand(1, 5));

        // 本次战斗打了几个回合
        $roundCount = $this->_recorder->roundCount();

        switch ($this->_fightResult) {

            // 我方胜利
            case self::WIN:

                // 胜利者从失败者身上抢银币
                $silverDeduct = floor($this->_enemy['silver'] * rand(7, 12) / 100);
                $setArrSelf['silver']  = array('+', $silverDeduct);
                $setArrEnemy['silver'] = array('-', $silverDeduct, 0);

                // 双方扣血值
                $setArrSelf['hp']  = array('-', rand(1, 5), 0);
                $setArrEnemy['hp'] = array('-', rand(16, 20), 0);

                // 最终战果影响文字
                $fightResustMsg = array(
                    // 我看到的
                    'self' => __('我方气势如虹，经过{round}个回合，获得战斗胜利，从{enemy_name}那抢到{silver_coins}，获得{exp}，消耗{energy}，损失{hp}，{enemy_name}损失{enemy_hp}。', array(
                        'round'        => $roundCount,
                        'enemy_name'   => '<span>' . $this->_enemy['user_name'] . '</span>',
                        'silver_coins' => '<span class="war_coin"></span>' . $silverDeduct,
                        'exp'          => '<span class="war_exp"></span>' . $setArrSelf['exp'][1],
                        'energy'       => '<span class="war_energy"></span>' . $setArrSelf['energy'][1],
                        'hp'           => '<span class="war_hp"></span>' . $setArrSelf['hp'][1],
                        'enemy_hp'     => '<span class="war_hp"></span>' . $setArrEnemy['hp'][1],
                    )),
                    // 对方看到的
                    'enemy' => __('对方实力强大，经过{round}个回合，我方不幸战斗失败，被抢走{silver_coins}，损失{hp}，{enemy_name}损失{enemy_hp}。', array(
                        'round'        => $roundCount,
                        'silver_coins' => '<span class="war_coin"></span>' . $silverDeduct,
                        'hp'           => '<span class="war_hp"></span>' . $setArrEnemy['hp'][1],
                        'enemy_name'   => '<span>' . $this->_self['user_name'] . '</span>',
                        'enemy_hp'     => '<span class="war_hp"></span>' . $setArrSelf['hp'][1],
                    )),
                );

                break;

            // 我方失败
            case self::LOSE:

                // 双方扣血值
                $setArrSelf['hp']  = array('-', rand(16, 20), 0);
                $setArrEnemy['hp'] = array('-', rand(1, 5), 0);

                // 最终战果影响文字
                $fightResustMsg = array(
                    // 我看到的
                    'self' => __('对方实力强大，经过{round}个回合，我方不幸战斗失败，消耗{energy}，损失{hp}，{enemy_name}损失{enemy_hp}。', array(
                        'round'        => $roundCount,
                        'energy'       => '<span class="war_energy"></span>' . $setArrSelf['energy'][1],
                        'hp'           => '<span class="war_hp"></span>' . $setArrSelf['hp'][1],
                        'enemy_name'   => '<span>' . $this->_enemy['user_name'] . '</span>',
                        'enemy_hp'     => '<span class="war_hp"></span>' . $setArrEnemy['hp'][1],
                    )),
                    // 对方看到的
                    'enemy' => __('我方实力强大，经过{round}个回合，获得了战斗胜利，损失{hp}，{enemy_name}损失{enemy_hp}。', array(
                        'round'        => $roundCount,
                        'hp'           => '<span class="war_hp"></span>' . $setArrEnemy['hp'][1],
                        'enemy_name'   => '<span>' . $this->_self['user_name'] . '</span>',
                        'enemy_hp'     => '<span class="war_hp"></span>' . $setArrSelf['hp'][1],
                    )),
                );

                break;

            // 双方战平
            case self::DRAW:

                // 双方扣血值
                $setArrSelf['hp']  = array('-', rand(8, 12), 0);
                $setArrEnemy['hp'] = array('-', rand(8, 12), 0);

                // 最终战果影响文字
                $fightResustMsg = array(
                    // 我看到的
                    'self' => __('双方实力相当，经过{round}个回合，战斗打成平手，消耗{energy}，损失{hp}，{enemy_name}损失{enemy_hp}。', array(
                        'round'        => $roundCount,
                        'energy'       => '<span class="war_energy"></span>' . $setArrSelf['energy'][1],
                        'hp'           => '<span class="war_hp"></span>' . $setArrSelf['hp'][1],
                        'enemy_name'   => '<span>' . $this->_enemy['user_name'] . '</span>',
                        'enemy_hp'     => '<span class="war_hp"></span>' . $setArrEnemy['hp'][1],
                    )),
                    // 对方看到的
                    'enemy' => __('双方实力相当，经过{round}个回合，战斗打成平手，损失{hp}，{enemy_name}损失{enemy_hp}。', array(
                        'round'        => $roundCount,
                        'hp'           => '<span class="war_hp"></span>' . $setArrEnemy['hp'][1],
                        'enemy_name'   => '<span>' . $this->_self['user_name'] . '</span>',
                        'enemy_hp'     => '<span class="war_hp"></span>' . $setArrSelf['hp'][1],
                    )),
                );

                break;

            default:

                throw new Core_Exception_Logic(__('战斗结果异常，善后事宜无法处理'));
        }

        // 缺省的航行到达时间（因为战斗时航行时间是停止的）
        // 防止玩家没有点击“战斗结果”按钮重置战斗耗时，这种情况下，使用这个缺省时间（例如：直接退出应用等）
        $setArrSelf['arrive_time'] = array('+', $this->_getElapse());

        // 统一更新我方属性
        $this->_self->update($setArrSelf);

        // 统一更新敌方属性
        if ($setArrEnemy) {
            $this->_enemy->update($setArrEnemy);
        }

        // 保存战斗记录
        $logId = $this->_recorder->setFightResultMsg($fightResustMsg)->save();

        return $logId;
    }

    /**
     * 缺省的本次战斗耗时
     *
     * @return int
     */
    private function _getElapse()
    {
        // 计算方法：开始3秒倒计时 + 攻击次数 * 每次攻击时间(1秒)
        // 加10秒误差
        return 10 + ceil(3 + ($this->_recorder->fireCount() * 2.4));
    }
}