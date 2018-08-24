<?php
namespace AZE\core;

class Profiler
{
    private static $instance;

    private $datas = array();

    private function __construct()
    {
        declare(ticks=1000);
        register_tick_function(array(&$this, 'tick'));
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function tick()
    {
        $this->datas[] = memory_get_usage();
    }

    public function getDatas()
    {
        unregister_tick_function(array(&$this, 'tick'));
        return $this->datas;
    }

    public static function active()
    {
        self::$instance = new self();
    }

    public static function getData()
    {
        return self::instance()->getDatas();
    }
}
