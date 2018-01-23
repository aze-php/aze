<?php
namespace AZE\core;

class Request
{
    private $get = null;
    private $post = null;
    private $header = null;

    private static $instance = null;

    /**
     *
     * Constructor
     *
     */
    public function __construct()
    {
        $this->get = array();
        foreach ($_GET as $key => $value) {
            $this->get[$key] = request\Parameter::get($key, null);
        }

        $this->post = array();
        foreach ($_POST as $key => $value) {
            $this->post[$key] = request\Parameter::post($key, null);
        }

        $this->header = array();
        if (function_exists('getallheaders') && is_callable('getallheaders')) {
            foreach (getallheaders() as $key => $value) {
                $this->header[$key] = request\Parameter::header($key, null);
            }
        }

        return $this;
    }

    public static function instance()
    {
        return self::$instance ?: new Request();
    }

    public function get($name)
    {
        return isset($this->get[$name]) ? $this->get[$name]->getValue() : null;
    }

    public function post($name)
    {
        return isset($this->post[$name]) ? $this->post[$name]->getValue() : null;
    }

    public function header($name)
    {
        return isset($this->header[$name]) ? $this->header[$name]->getValue() : null;
    }
}
