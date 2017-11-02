<?php
namespace AZE\core;

class Translator
{
    private static $instance;
    private $langage = 'fr';
    private $multi_dictionnary = array();

    /**
     *
     * Constructor
     */
    private function __construct()
    {
        if (!isset($lang) || empty($lang)) {
            $lang = 'fr';
        }
        $this->multi_dictionnary = array();
        $this->langage = $lang + "";
        $this->multi_dictionnary[$this->langage] = array();
    }

    /**
     *
     * Get current instance
     */
    private static function instance()
    {
        if(is_null(self::$instance)) {
            self::$instance = new Translator();
        }

        return self::$instance;
    }

    public static function getLangage()
    {
        return self::instance()->langage;
    }

    public static function setLangage($lang = 'fr')
    {
        self::instance()->langage = $lang;

        if(!array_key_exists(self::instance()->langage, self::instance()->multi_dictionnary)){
            self::instance()->multi_dictionnary[self::instance()->langage] = array();
        }
    }

    public static function getEntries()
    {
        return (array)self::instance()->multi_dictionnary[self::instance()->langage];
    }

    public static function addEntries(array $entries = array())
    {
        self::instance()->multi_dictionnary[self::instance()->langage] = array_merge(
            self::instance()->multi_dictionnary[self::instance()->langage],
            $entries
        );
    }

    public static function translate($mixed = array(), $lang = 'fr')
    {
        $single_world = !is_array($mixed);
        self::instance()->setLangage($lang);
        $mixed = array_map(function($element){
            $dictionnary = Translator::getEntries();
            if(array_key_exists($element, $dictionnary)){
                $element = $dictionnary[$element];
            }
            return $element;
        }, (array)$mixed);

        if($single_world){
            $mixed = array_filter($mixed);
            $mixed = reset($mixed);
        }

        return $mixed;
    }
}