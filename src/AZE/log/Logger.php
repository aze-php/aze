<?php
namespace AZE\log;

use AZE\core\Debug;
use AZE\core\export\Export;
use Monolog\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;

class Logger
{
    private static $instances = array();

    private $logger;

    private function __construct($name)
    {
        $this->logger = new \Monolog\Logger($name);

        $handler = new ErrorHandler($this->logger);
        $handler->registerErrorHandler([], false);
        $handler->registerExceptionHandler();
        $handler->registerFatalHandler();

        $this->logger->pushProcessor(new \Monolog\Processor\WebProcessor);


        $streamHandler = new \Monolog\Handler\StreamHandler('php://stderr', \Monolog\Logger::ERROR);
        $this->logger->pushHandler($streamHandler);

        if (Debug::isActivated()) {
            $browserHanlder = new \Monolog\Handler\BrowserConsoleHandler(\Monolog\Logger::DEBUG);
            $this->logger->pushHandler($browserHanlder);
        }

        $htmlHandler = new AZEHandler();
        $htmlHandler->setFormatter(new LineFormatter());
        $this->logger->pushHandler($htmlHandler);
    }

    public static function get($name = 'AZE')
    {
        if (!isset(self::$instances[$name]) || is_null(self::$instances[$name])) {
            self::$instances[$name] = new self($name);
        }

        return self::$instances[$name]->logger;
    }

    public static function addHandler(HandlerInterface $handler)
    {
        self::get()->logger->pushHandler($handler);
    }

    public static function __callStatic($name, $arguments)
    {
        $return = false;
        if (isset(\Monolog\Logger::getLevels()[strtoupper($name)])) {
            $return = call_user_func_array(array(Logger::get(), strtolower($name)), $arguments);
        }

        return $return;
    }

    public static function var_dump(){ return call_user_func_array(array(Export::get("dump"), "dump"), func_get_args());}
    public static function dump(){ return call_user_func_array(array(self, "var_dump"), func_get_args()); }
    public static function varDump(){ return call_user_func_array(array(self, "var_dump"), func_get_args()); }

    public static function debug(){ return call_user_func_array(array(Logger::get(), __FUNCTION__), func_get_args()); }
    public static function info(){ return call_user_func_array(array(Logger::get(), __FUNCTION__), func_get_args()); }
    public static function notice(){ return call_user_func_array(array(Logger::get(), __FUNCTION__), func_get_args()); }
    public static function warning(){ return call_user_func_array(array(Logger::get(), __FUNCTION__), func_get_args()); }
    public static function error(){ return call_user_func_array(array(Logger::get(), __FUNCTION__), func_get_args()); }
    public static function critical(){ return call_user_func_array(array(Logger::get(), __FUNCTION__), func_get_args()); }
    public static function alert(){ return call_user_func_array(array(Logger::get(), __FUNCTION__), func_get_args()); }
    public static function emergency(){ return call_user_func_array(array(Logger::get(), __FUNCTION__), func_get_args()); }
}