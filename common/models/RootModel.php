<?php

namespace common\models;

use DateTime;
use yii\db\ActiveRecord;

/** @noinspection UndetectableTableInspection */

/**
 * @property array $permissions
 */
class RootModel extends ActiveRecord
{
    public static function getSetTimeZoneHandler()
    {
        $setTimeZone = function ($event) {
            $date = new DateTime();
            $offset = $date->getOffset();
            $sign = $offset < 0 ? '-' : '+';
            $offset = abs($offset);
            $hour = intval($offset / (60 * 60));
            $min = abs(abs($offset) - abs($hour) * (60 * 60)) / 60;
            $tzFinal = $sign . $hour . ':' . $min;
            $event->sender->createCommand("SET time_zone='" . $tzFinal . "';")->execute();
        };

        return $setTimeZone;
    }
}