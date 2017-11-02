<?php
namespace AZE\core\configuration;

use AZE\exception\ConfigException;

class Config implements \IteratorAggregate
{

    /**
     * @var Config instance de classe
     */
    private static $instances = array();

    /**
     *
     * @var array[ConfigElement] configuration elements
     */
    private $config;

    public function __construct()
    {
        $this->config = new ConfigElement();
    }

    /**
     * Get instance $name or new instance if $name doesn't exist
     *
     * @param string $name instance name
     *
     * @return Config
     */
    public static function get($name = 'default')
    {
        if (!isset(self::$instances[$name]) || is_null(self::$instances)) {
            self::$instances[$name] = new Config();
        }

        return self::$instances[$name];
    }

    /**
     * Define path to configuration file
     */
    public function loadXml($configFilePath)
    {
        if (file_exists($configFilePath)) {
            libxml_disable_entity_loader(false);
            $xml = simplexml_load_file($configFilePath);
            $element = new ConfigElement($xml, ConfigElement::XML);
            $this->config->merge($element);
        } else {
            throw new ConfigException('Configuration file "' . $configFilePath . '" not found');
        }

        return $this;
    }

    /**
     * Define path to configuration file
     */
    public function loadJson($configFilePath)
    {
        if (file_exists($configFilePath)) {
            $json = json_decode(file_get_contents($configFilePath), true);
            $this->config->merge(new ConfigElement($json, ConfigElement::JSON));
        } else {
            throw new ConfigException('Configuration file "' . $configFilePath . '" not found');
        }

        return $this->config;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->config->children);
    }

    /**
     * Get current configuration as SimpleXmlObject
     */
    public function __get($attr)
    {
        return $this->config->$attr;
    }

    /**
     *
     * @return Boolean
     */
    public function save()
    {
        return self::instance()->config->asXml(CONFIG_PATH);
    }
}