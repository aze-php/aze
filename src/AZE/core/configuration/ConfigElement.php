<?php
namespace AZE\core\configuration;

class ConfigElement implements \IteratorAggregate
{
    const XML = 1;
    const JSON = 2;

    private $value = null;
    private $children = null;

    public function __construct($datas = array(), $type = null)
    {
        switch ($type) {
            case self::XML:
                $this->loadXml($datas);
                break;
            case self::JSON:
                $this->loadJson($datas);
                break;
            default:
                return;
        }

        return $this;
    }

    private function loadXml(\SimpleXMLElement $xml)
    {
        if ($xml->count()) {
            $this->value = array();
            foreach ($xml->children() as $child) {
                $configElement = new self($child, self::XML);
                $this->value[$child->getName()] = $configElement->getValue();
            }
        } else {
            $this->value = $xml . '';
        }

        return $this;
    }

    private function loadJson($json)
    {
        if (is_array($json) && count($json)) {
            $this->children = array();
            foreach ($json as $key=>$node) {
                $configElement = new self($node, self::JSON);
                $this->children[$key] = $configElement->getValue();
            }
        } else {
            $this->value = $json;
        }

        return $this;
    }

    public function merge(ConfigElement $newConfig)
    {
        foreach ($newConfig as $name=>$child) {
            $this->children[$name] = $child;
            $this->value = null;
        }

        return $this;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->children);
    }


    public function getValue()
    {
        return !is_null($this->value) ? $this->value  : $this;
    }


    public function setValue($value)
    {
        return $this->value = $value;
    }

    /**
     * @return array children
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param null $children
     */
    public function setChildren(array $children)
    {
        $this->children = $children;
    }

    public function __get($attr)
    {
        return !is_null($this->children) && isset($this->children[$attr]) ? $this->children[$attr] : null;
    }

    public function __toString()
    {
        $string = $this->getValue();

        if (!is_null($this->children)) {
            $string = "ConfigElement:{";
            if (is_array($this->children)) {
                $sep = '';
                foreach ($this->children as $attr=>$child) {
                    $string .= $attr . ':' . $child;
                    $sep = ',';
                }
            }
            $string .= "}";
        }

        return $string;
    }
}