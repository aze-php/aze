<?php
namespace AZE\core;

abstract class Controller
{
    /**
     * @var string      Action requested by user/url
     */
    public $action = null;

    /**
     * @var \AZE\core\request\Request Current request (post/get/headers)
     */
    protected $request = null;

    /**
     * @var string      Action requested by user/url
     */
    private $callBefore = array();

    /**
     * @var string      Action requested by user/url
     */
    private $callAfter = array();

    /**
     * @param null $action Action requested by user/url
     */
    public function __construct($action = null)
    {
        $this->action = $action;

        $this->request = new Request();

        // CallBefore call
        if (count($this->callBefore) > 0) {
            foreach ($this->callBefore as $callback) {
                call_user_func($callback, $this);
            }
        }

        if (empty($this->action)) {
            $this->main();
        } elseif (method_exists($this, $this->action)) {
            $reflection = new \ReflectionMethod($this, $this->action);
            if (!$reflection->isPublic()) {
                $this->action();
            } else {
                call_user_func_array(array($this, $this->action), array());
            }
        } else {
            $this->action();
        }

        // CallAfter call
        if (count($this->callAfter) > 0) {
            foreach ($this->callAfter as $callback) {
                call_user_func($callback, $this);
            }
        }
        return $this;
    }

    /**
     * @abstract
     * Default action, it's executed if no one is defined
     */
    public function main()
    {
    }

    /**
     * @abstract
     * Function used to manage actions requested
     */
    public function action()
    {
    }


    /**
     * callback executed before action
     * This callback will be use before the internal call to "main()" or "action()"
     */
    protected function addCallbefore($function)
    {
        $return = -1;
        if (isset($function) && !empty($function)) {
            $this->callBefore[] = $function;
            $return = count($this->callBefore);
        }
        return $return;
    }

    /**
     * callback executed after action
     * This callback will be use after the internal call to "main()" or "action()"
     */
    protected function addCallAfter($function)
    {
        $return = -1;
        if (isset($function) && !empty($function)) {
            $this->callAfter[] = $function;
            $return = count($this->callAfter);
        }
        return $return;
    }
}
