<?php
namespace AZE\core\export;

class Export
{
    private static $exporter = array();

    public static function get($name = "dump")
    {
        if (!isset(self::$exporter[$name]) || is_null(self::$exporter[$name])) {
            self::$exporter[$name] = new Exporter();
        }

        return self::$exporter[$name];
    }

    public static function dump()
    {
        return call_user_func_array(array(self::init(), "dump"), func_get_args());
    }

    public static function getDumps()
    {
        return call_user_func_array(array(self::init(), "getDumps"), func_get_args());
    }

    public static function isActivated()
    {
        return call_user_func_array(array(self::init(), "isActivated"), func_get_args());
    }

    public static function activated($bool)
    {
        return call_user_func(array(self::init(), "activated"), $bool);
    }
}
