<?php

class Dao_Static_Port extends Dao_Static_Abstract
{
    protected $_tableName  = 'port';
    protected $_primaryKey = 'port_id';
    protected $_nameField  = 'port_name';

    const DEFAULT_DIST_TIME = 60;

    public function getFull($portId)
    {
        $portRow = $this->get($portId);
        if ($portRow) {
            $portRow['sea_area'] = Dao('Static_SeaArea')->get($portRow['sea_area_id']);
        }

        return $portRow;
    }

    public function getDistance($portFrom, $portTo)
    {
        $distanceTime = $this->_db()->fetchOne("SELECT `distance_time` FROM `port_distance` WHERE `port_from` = '{$portFrom}' AND `port_to` = '{$portTo}'");
        return $distanceTime ? $distanceTime : self::DEFAULT_DIST_TIME;
    }
}