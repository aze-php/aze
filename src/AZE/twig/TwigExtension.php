<?php
namespace AZE\twig;

use AZE\core\Debug;
use AZE\core\export\Export;
use AZE\core\Profiler;
use Monolog\Logger;

class TwigExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'AZE_Extension';
    }

    public function getFilters()
    {
        $safe = array('is_safe' => array('html'));
        return array(
            'ellapsed'=>new \Twig_Filter('ellapsed', array($this, 'ellapsed'), $safe),
            'dumper'=>new \Twig_Filter('dumper', array($this, 'dumper'), $safe),
            'getExports'=>new \Twig_Filter('getExports', array($this, 'getExports'), $safe),
            'FormatBytes'=>new \Twig_Filter('FormatBytes', array($this, 'formatBytes'), $safe)
        );
    }

    public function getFunctions()
    {
        $safe = array('is_safe' => array('html'));
        return array(
            new \Twig_SimpleFunction('GetLogLevels', array($this, 'getLogLevels'), $safe),
            new \Twig_SimpleFunction('DebugIsActivated', array($this, 'debugIsActivated'), $safe),
            new \Twig_SimpleFunction('DumperCss', array($this, 'getDumperCss'), $safe),
            new \Twig_SimpleFunction('DumperJs', array($this, 'getDumperJs'), $safe),
            new \Twig_SimpleFunction('MemoryUsage', array($this, 'getMemoryUsage')),
            new \Twig_SimpleFunction('ProfilerDatas', array($this, 'getProfilerDatas'))
        );
    }

    public function ellapsed($timestamp)
    {
        $return = 0;
        if (is_numeric($timestamp)) {
            $return = round(microtime(true) - $timestamp, 2);
        }

        return $return;
    }

    public function dumper($var)
    {
        return \Dumper\Dumper::dump($var) . '';
    }

    public function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function getExports($name)
    {
        return Export::get($name)->getDumps();
    }

    public function getLogLevels()
    {
        return Logger::getLevels();
    }

    public function debugIsActivated()
    {
        return Debug::isActivated();
    }

    public function getDumperCss()
    {
        return \Dumper\Dumper::getCss();
    }

    public function getDumperJs()
    {
        return \Dumper\Dumper::getJs();
    }

    public function getMemoryUsage()
    {
        return memory_get_peak_usage();
    }

    public function getProfilerDatas()
    {
        return Profiler::instance()->getDatas();
    }
}
