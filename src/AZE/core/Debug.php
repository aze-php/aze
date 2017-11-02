<?php
namespace AZE\core;

class Debug
{
    private static $dump = array();

    public static function getDumps()
    {
        return self::$dump;
    }

    public static function dump()
    {
        $countArgs = func_num_args();

        if ($countArgs > 0) {

            $params = func_get_args();

            foreach ($params as $param) {
                self::$dump[] = $param;
            }
        }
    }
}