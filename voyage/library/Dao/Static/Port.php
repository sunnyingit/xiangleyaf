<?php

class Dao_Static_Port extends Dao_Static_Abstract
{
    const DEFAULT_DIST_TIME = 60;

    public function getDistance($portFrom, $portTo)
    {
        $distanceTime = $this->_db()->fetchOne("SELECT `distance_time` FROM `port_distance` WHERE `port_from` = '{$portFrom}' AND `port_to` = '{$portTo}'");
        return $distanceTime ?: self::DEFAULT_DIST_TIME;
    }
}