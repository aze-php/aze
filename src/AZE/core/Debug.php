<?php
namespace AZE\core;

use AZE\core\export\Export;

class Debug
{
    private static $activated = false;

    public static function isActivated()
    {
        return self::$activated || false;
    }

    public static function activated($bool)
    {
        self::$activated = $bool ? $bool != "false" : false;
    }
}