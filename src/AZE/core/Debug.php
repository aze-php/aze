<?php
namespace AZE\core;

class Debug
{
    private static $activated = false;

    public static function isActivated()
    {
        return self::$activated || false;
    }

    public static function activated($bool)
    {
        self::$activated = $bool ? $bool !== "false" : false;
    }
}
