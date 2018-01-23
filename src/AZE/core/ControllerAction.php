<?php
namespace AZE\core;

class ControllerAction
{
    private $namespace = null;
    private $name = null;
    private $action = null;

    /**
     *
     * Constructor
     */
    public function __construct($name = null, $action = null)
    {
        $this->name = $name;
        $this->action = $action;

        return $this;
    }

    /**
     * @param null $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function isValid()
    {
        return !is_null($this->name);
    }

    public function call()
    {
        $return = false;
        if ($this->isValid()) {
            $class = $this . '';
            if (class_exists($class)) {
                $return = true;
                $class = new $class($this->action);
            }
        }

        return $return;
    }

    public function __toString()
    {
        return $this->namespace . '\\' . $this->name;
    }
}
