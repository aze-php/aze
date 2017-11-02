<?php
namespace AZE\twig;


use AZE\core\db\Db;
use AZE\core\Debug;

use AZE\core\Initializer;

class TwigExtended
{
    private $twig = null;

    private $datas = array();

    private $jsArray = array();
    private $cssArray = array();
    private $scriptArray = array();

    const DEFAULT_TEMPLATE = 'index.html';

    public function __construct(TwigConfig $twigConfig = null)
    {
        if (!is_null($twigConfig)) {
            $loader = new \Twig_Loader_Filesystem($twigConfig->getTemplateDir());

            $loader->addPath(__DIR__ . "/../controller/twig/template");

            $this->twig = new \Twig_Environment(
                $loader,
                $twigConfig->toArray()
            );

            $this->twig->addExtension(new TwigExtension());
        }

        return $this;
    }

    public function set($var = null, $value = null)
    {
        if (!is_null($var)) {
            $this->datas[$var] = $value;
        }
    }

    public function get($var = null)
    {
        $return = null;

        if (isset($this->datas[$var])) {
            $return = $this->datas[$var];
        }

        return $return;
    }

    /**
     *
     * Add js tag && url to template
     * @param string $js
     */
    public function addJs($js)
    {
        $this->jsArray[] = $js;
    }

    /**
     *
     * Add css tag && url to template
     * @param unknown_type $css
     */
    public function addCss($css)
    {
        $this->cssArray[] = $css;
    }

    /**
     *
     * Add css tag && url to template
     * @param unknown_type $css
     */
    public function addScript($script)
    {
        $this->scriptArray[] = $script;
    }

    private function defineRendering($template = self::DEFAULT_TEMPLATE, array $datas = array(), $combine = false)
    {
        $timer = round(microtime(true) - Initializer::$timer, 2);

        $this->set('loadedTime', $timer);

        $this->set('jsArray', $this->jsArray);
        $this->set('cssArray', $this->cssArray);
        $this->set('scriptArray', $this->scriptArray);

        $this->set('startTime', Initializer::$timer);
        $this->set('parameter', array("get"=>$_GET, "post"=>$_POST, "cookies"=>$_COOKIE));

        $this->set('classList', get_declared_classes());
        $this->set('queryList', Db::instance()->queryList);
        $this->set('dumpList', Debug::getDumps());

        $this->datas = $combine ? array_merge_recursive($this->datas, $datas) : array_merge($this->datas, $datas);

        return $this->twig->load($template);
    }

    public function fetch($template = self::DEFAULT_TEMPLATE, array $datas = array(), $combine = false)
    {
        return $this->defineRendering($template, $datas, $combine)->render($this->datas);
    }

    public function render($template = self::DEFAULT_TEMPLATE, array $datas = array(), $combine = false)
    {
        echo $this->fetch($template, $datas, $combine);
    }
}