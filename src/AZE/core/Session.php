<?php
namespace AZE\core;

class Session
{
    /**
     * @var Session $instance Current session
     */
    private static $instance;

    /**
     * @var array $session_vars Array of session datas
     */
    public $sessions_vars;

    /**
     * Constructor
     */
    private function __construct()
    {
        if (defined('SESSION_PATH')) {
            if (!file_exists(SESSION_PATH)) {
                mkdir(SESSION_PATH, 0777, false);
            }
            session_save_path(SESSION_PATH);
        }

        session_start();
        $this->sessions_vars = $_SESSION;
    }

    /**
     * Get current instance
     * @return \AZE\core\Session
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Session();
        }

        return self::$instance;
    }

    /**
     * Return session ID
     * @return string
     */
    public static function id()
    {
        self::instance();

        return session_id();
    }

    /**
     * Return session data for a given key
     * @param string $key datakey
     * @return Ambigous <NULL, multitype:>
     */
    public static function get($key = '')
    {
        return array_key_exists($key, self::instance()->sessions_vars) ? self::instance()->sessions_vars[$key] : null;
    }

    /**
     * Set data for a given key
     * @param string $key datakey
     * @param string $value Value
     */
    public static function set($key = '', $value = '')
    {
        self::instance()->sessions_vars[$key] = $value;
        $_SESSION[$key] = $value;
    }

    /**
     * Unset a data
     * @param string $key Datakey
     */
    public static function unsetValue($key = '')
    {
        unset(self::instance()->sessions_vars[$key]);
        unset($_SESSION[$key]);
    }

    /**
     * Destroy current session
     */
    public static function destroy()
    {
        self::instance()->sessions_vars = array();
        session_destroy();
    }

    /**
     * Magic method using to access to data
     * @param string $attr Datakey
     * @return Ambigous <NULL, multitype:>
     */
    public function __get($attr)
    {
        return array_key_exists($attr, self::instance()->sessions_vars) ? self::instance()->sessions_vars[$attr] : null;
    }

    /**
     * Magic method using to set data for a given key
     * @param string $attr Datakey
     * @param mixed $value Value
     */
    public function __set($attr, $value)
    {
        self::instance()->sessions_vars[$attr] = $value;
        $_SESSION[$attr] = $value;
    }
}
