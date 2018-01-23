<?php
namespace AZE\core\request;

class Parameter
{
    private $name = null;

    private $value = null;

    private $default = null;

    /**
     *
     * Constructor
     */
    private function __construct($name, $type = "GET")
    {
        $this->name = $name;

        $this->type = $type;
        if ($type == 'HEADER') {
            $array = getallheaders();
        } else {
            global ${'_' . $this->type};
            $array =& ${'_' . $this->type};
        }

        if (isset($array[$this->name])) {
            $this->value = $array[$this->name];
        }

        return $this;
    }

    public static function get($name, $default = null)
    {
        return (new self($name, 'GET'))->setDefault($default);
    }

    public static function post($name, $default = null)
    {
        return (new self($name, 'POST'))->setDefault($default);
    }

    public static function header($name, $default = null)
    {
        return (new self($name, 'HEADER'))->setDefault($default);
    }

    public function setDefault($default)
    {
        $this->default = $default;
        $this->value = !is_null($this->value) ? $this->value : $this->default;
        return $this;
    }

    /**
     * Return the current value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     *
     * @param int $filterName Name of the filter to use
     * list of filter
     *
     * int or 257
     * boolean or 258
     * float or 259
     * validate_regexp or 272
     * validate_url or 273
     * validate_email or 274
     * validate_ip or 275
     * string or 513
     * stripped or 513
     * encoded or 514
     * special_chars or 515
     * unsafe_raw or 516
     * email or 517
     * url or 518
     * number_int or 519
     * number_float or 520
     * magic_quotes or 521
     * callback or 1024
     */
    public function validate($filter, $options = null)
    {
        $valid = false;
        if (is_int($filter) || $filter instanceof Validate) {
            $valid = filter_var($this->value, $filter, $options);
        } elseif (is_string($filter)) {
            // StaticInit if it's a Regex
            if (preg_match("/^\/.+\/[a-z]*$/i", $filter)) {
                $valid = filter_var($filter, $this->value);
            } elseif (filter_id($filter)) {
                $valid = filter_var($this->value, filter_id($filter));
            }
        } elseif (is_callable($filter)) {
            $valid = call_user_func($filter, $this->value);
        }

        return $valid;
    }

    public function sanitize($sanitizer)
    {
        $this->value = filter_var($sanitizer, $this->value);
        return $this;
    }
}
