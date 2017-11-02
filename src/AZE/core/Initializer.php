<?php
namespace AZE\core;

use AZE\exception\InitializerException;
use AZE\core\routing\Router;

class Initializer
{
    public static $timer = 0;

    public static function initialize($initFile = null)
    {
        if (php_sapi_name() == 'cli-server') {
            $publicDir = dirname(realpath($_SERVER['SCRIPT_FILENAME']));
            $scriptPath = realpath($_SERVER['SCRIPT_NAME']);

            if (strpos($scriptPath, $publicDir) > -1 && $scriptPath !== $publicDir) {
                return false;
            }
        }

        // Initialisation of global timer
        self::$timer = microtime(true);

        Router::setNamespace('\\AZE\\controller');
        Router::setDefaultController('AZEDefaultController');

        if (!is_null($initFile)) {
            if (!file_exists($initFile)) {
                throw new InitializerException('File ' . $initFile . ' not found');
            }

            require $initFile;

            if (!class_exists('AZE\Init\Init')) {
                throw new InitializerException('Class AZE\Init\Init not found');
            }

            if (!in_array('AZE\core\InitializerInterface', class_implements('AZE\Init\Init'))) {
                throw new InitializerException('Init class doesn\'t implement AZE\core\InitializerInterface interface');
            }

            \AZE\Init\Init::initialize();
        }

        return new Router();
    }
}