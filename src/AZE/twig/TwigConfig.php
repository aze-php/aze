<?php
namespace AZE\twig;

class TwigConfig
{
    /**
     * @var string Template directory
     */
    private $templateDir = null;

    /**
     * @var string Config directory
     */
    private $cacheDir = null;

    /**
     * @var string Default charset
     */
    private $charset = 'utf-8';

    /**
     * @var string Default charset
     */
    private $baseTemplateClass = 'Twig_Template';

    /**
     * @var boolean Debug activated
     */
    private $debugActivated = false;

    /**
     * @var boolean Debug activated
     */
    private $autoReload = false;

    /**
     * @var boolean Use strict variable
     */
    private $strictVariables = false;

    /**
     * @var string Autoescape variable
     */
    private $autoescape = null;

    /**
     * @var int A flag that indicates which optimizations to apply (default to -1 -- all optimizations are enabled; set it to 0 to disable).
     */
    private $optimizations = -1;

    /**
     * @return string
     */
    public function getTemplateDir()
    {
        return $this->templateDir;
    }

    /**
     * @param string $templateDir
     */
    public function setTemplateDir($templateDir)
    {
        $this->templateDir = $templateDir;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * @param string $cacheDir
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @return bool
     */
    public function isDebugActivated()
    {
        return $this->debugActivated;
    }

    /**
     * @param bool $debug
     */
    public function setDebugActivated($debug)
    {
        $this->debugActivated = $debug;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * @return string
     */
    public function getBaseTemplateClass()
    {
        return $this->baseTemplateClass;
    }

    /**
     * @param string $baseTemplateClass
     */
    public function setBaseTemplateClass($baseTemplateClass)
    {
        $this->baseTemplateClass = $baseTemplateClass;
    }

    /**
     * @return bool
     */
    public function isAutoReload()
    {
        return $this->autoReload;
    }

    /**
     * @param bool $autoReload
     */
    public function setAutoReload($autoReload)
    {
        $this->autoReload = $autoReload;
    }

    /**
     * @return bool
     */
    public function isStrictVariables()
    {
        return $this->strictVariables;
    }

    /**
     * @param bool $strictVariables
     */
    public function setStrictVariables($strictVariables)
    {
        $this->strictVariables = $strictVariables;
    }

    /**
     * @return string
     */
    public function getAutoescape()
    {
        return $this->autoescape;
    }

    /**
     * @param string $autoescape
     */
    public function setAutoescape($autoescape)
    {
        $this->autoescape = $autoescape;
    }

    /**
     * @return int
     */
    public function getOptimizations()
    {
        return $this->optimizations;
    }

    /**
     * @param int $optimizations
     */
    public function setOptimizations($optimizations)
    {
        $this->optimizations = $optimizations;
    }

    public function toArray()
    {
        return array(
            'debug'=>$this->isDebugActivated(),
            'charset'=>$this->getCharset(),
            'base_template_class'=>$this->getBaseTemplateClass(),
            'cache'=>$this->getCacheDir(),
            'auto_reload'=>$this->isAutoReload(),
            'strict_variables'=>$this->isStrictVariables(),
//            'autoescape'=>$this->getAutoescape(),
            'optimizations'=>$this->getOptimizations()
        );
    }
}