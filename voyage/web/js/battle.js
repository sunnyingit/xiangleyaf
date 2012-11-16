var battleRecorder = function(options)
{
    var self = this;

    this.isReplay   = options.isReplay;
    this.logs       = options.logs;

    this.logScroll  = null;
    this.lastRound  = 0;
    this.playSt     = null;

    // 动画的播放时长（单位：ms）
    this.durations = {
        "shoot" : 1200, // 开火动画
        "hit"   : 1200, // 受击动画
        "wreck" : 2200  // 沉船动画
    };

    // 开打倒计时 3-2-1
    this.countdown = function(sec)
    {
        $('#countdown_war')[0].className = 'count_war count_' + sec;

        setTimeout(function() {
            if (sec > 0) {
                self.countdown(sec - 1);
            } else {
                self.startup();    // 正式开打！
                $('#countdown_war').remove();
            }
        }, 900);
    };

    // 正式开打！
    this.startup = function()
    {
        // 战斗记录滚动区域
        self.logScroll = new iScroll('record_box', {hScroll: false, hScrollbar: false, vScrollbar: false});

        // 开始播报
        self.play();
        self.playSt = setInterval(self.play, 2420);

        // 查看战果按钮（点击略过战斗过程直接显示战果）
        $('#showResultBtn').bind('click', function() {
            // 显示战果弹窗
            self.showResult();
        });
    };

    // 循环播报
    this.play = function()
    {
        // 弹出最新一条记录
        var log = self.getLog();
        if (!log) {
            return false;
        }

        // 顶部两个当前攻防船信息更新
        self.updateTopCurrent(log);

        // 播放一次攻防动画
        self.showAnimation(log);
    };

    // 弹出最新一条记录
    this.getLog = function()
    {
        // 从数组中弹出
        var log = self.logs.shift();

        // 全部播放完了
        if (log == undefined) {
            // 显示战果弹窗
            self.showResult();
            return false;
        }

        return log;
    };

    // 播放一次攻防动画
    this.showAnimation = function(log)
    {
        var attackerShip = $('#' + log.attacker_side +'_ship_' + log.att_ship.ship_id);
        var defenderShip = $('#' + log.defender_side +'_ship_' + log.def_ship.ship_id);

        // 攻船开炮动画开始
        self.updateShipEffect(attackerShip, 'shoot');

        setTimeout(function() {

            // 底部文字记录更新
            self.appendLog(log);

            // 攻船开炮动画结束
            self.updateShipEffect(attackerShip, null);

            // 守船受击动画开始
            self.updateShipStatus(defenderShip, 'hit');     // 状态：船体受击
            self.updateShipEffect(defenderShip, 'explode'); // 特效：船体爆炸

            // 守船扣血、血条变化
            var shipWrecked = self.updateShipHp(log);

            // 守船受击动画结束
            setTimeout(function() {

                // 守船被击沉
                if (shipWrecked) {

                    // 切换到沉船状态（播放沉船动画）
                    self.updateShipStatus(defenderShip, 'wreck');

                    // 从画面中移除该船
                    setTimeout(function() {
                        defenderShip.css('visibility', 'hidden');
                    }, self.durations.wreck); // 沉船动画END (2200ms)

                } else {

                    // 还原到待机状态
                    self.updateShipStatus(defenderShip, 'standby');
                }

                // 移除爆炸特效
                self.updateShipEffect(defenderShip, null);

            }, self.durations.hit);   // 受击动画END (1200ms)

        }, self.durations.shoot);   // 开火动画END (1200ms)
    };

    // 底部文字记录播放
    this.appendLog = function(log)
    {
        var html = '<p>' + log.message + '</p>';

        if (log.round != self.lastRound) {
            html = '<p class="yellow">' + __('round_no', {"round": log.round}) + '</p>' + html;
            self.lastRound = log.round;
        }

        // 追加记录
        $('#record_box_inner').append(html);

        // 文字播报区实时滚动到最底部
        self.updateScroll();
    };

    // 播放区滚动
    this.updateScroll = function()
    {
        // 刷新滚动区域
        self.logScroll.refresh();

        // 播放区域始终保持滚动到底，使之显示底部最新一条
        if ($('#record_box_inner p').length > 4) {
            self.logScroll.scrollToElement('p:last-child', 100);
        }
    };

    // 守船扣血、血条变化
    this.updateShipHp = function(log)
    {
        var defenderShip = $('#' + log.defender_side +'_ship_' + log.def_ship.ship_id);

        // 更新血条宽度
        var hpPercent = log.def_ship.hp / defenderShip.attr('init_hp');
        defenderShip.find('.ship_blood span').animate({
            "width": Math.ceil(60 * hpPercent) + 'px'
        });

        // 血条变色
        if (hpPercent <= 0.35) {
            defenderShip.children('.ship_blood').addClass('blood_3');
        } else if (hpPercent <= 0.70) {
            defenderShip.children('.ship_blood').addClass('blood_2');
        }

        // 扣血数字浮现
        self.showDamageNumber(defenderShip, log.damage);

        // 返回是否被击沉
        return (log.def_ship.hp < 1) ? true : false;
    };

    // 扣血数字浮现
    this.showDamageNumber = function(defenderShip, hpSub)
    {
        var subHpPop = $('<div id="warship_sub_hp" style="font-size:10px;z-index:100">' + (-hpSub) + '</div>');
        subHpPop.appendTo("body");

        var subHpOffset = defenderShip.offset();
        subHpOffset.top += 30;
        subHpOffset.left += 50;

        subHpPop.offset(subHpOffset).show().animate({
            "font-size": "35px",
            "opacity": "show",
            "left": "-=32px"
        }, 300, "linear", function() {
            setTimeout(function() {
                subHpPop.remove();
            }, 600);
        });
    };

    // 顶部两个当前攻防船信息更新
    this.updateTopCurrent = function(log)
    {
        if (log.attacker_side == 'self') {
            var selfShip       = $('#self_ship_' + log.att_ship.ship_id);
            var enemyShip      = $('#enemy_ship_' + log.def_ship.ship_id);
            var selfShipCurHp  = log.att_ship.hp;
            var enemyShipCurHp = log.def_ship.hp_before;
        } else {
            var selfShip       = $('#self_ship_' + log.def_ship.ship_id);
            var enemyShip      = $('#enemy_ship_' + log.att_ship.ship_id);
            var selfShipCurHp  = log.def_ship.hp_before;
            var enemyShipCurHp = log.att_ship.hp;
        }

        $('#self_cur_ship_info').show();
        $('#self_cur_ship_info .ship_name').html(selfShip.attr('ship_name'));
        $('#self_cur_ship_info .ship_hp').html(selfShipCurHp + '/' + selfShip.attr('init_hp'));

        $('#enemy_cur_ship_info').show();
        $('#enemy_cur_ship_info .ship_name').html(enemyShip.attr('ship_name'));
        $('#enemy_cur_ship_info .ship_hp').html(enemyShipCurHp + '/' + enemyShip.attr('init_hp'));
    };

    // 显示最终战果弹窗
    this.showResult = function()
    {
        $('#pop_up_result_msg').show();
        clearInterval(self.playSt);

        // 禁用查看战果按钮（防止重复点击）
        $('#showResultBtn').unbind('click');

        // 非回放时
        if (!self.isReplay) {

            // 刷新顶部用户信息
            voyage.reloadTop();

            // 重置航行时间
            $.get('/battle/reset-arrive-time/?t=' + Math.random());
        }
    };

    // 更新船的状态（待机、受击、沉没）
    this.updateShipStatus = function(ship, status)
    {
        if (status != null) {
            ship.children('.ship_img').children('img').attr('src', ship.attr('img_' + status));
        } else {
            ship.children('.ship_img').empty();
        }
    };

    // 更新船的特效（开炮、被击中爆炸）
    this.updateShipEffect = function(ship, effect)
    {
        ship.children('.ship_effect').empty();
        if (effect != null) {
            ship.children('.ship_effect').html('<img src="/img/warship/war_way/' + effect + '.gif" />');
        }
    };
};