<?php
namespace AZE\core\routing;

use AZE\core\ControllerAction;
use AZE\core\Translator;
use AZE\exception\RouterException;

class Router
{
    /**
     * A dynamic routing will call the controller corresponding to the current url
     */
    const DYNAMIC = 1;
    /**
     * A strict routing will only allow usage of routing file
     */
    const STRICT = 2;

    private static $routes = array();

    private static $defaultController = null;

    private static $namespace = null;

    private static $routingType = self::DYNAMIC;

    public function __construct()
    {
        $uri = $_SERVER["REQUEST_URI"];

        $this->controllerAction = new ControllerAction(self::$defaultController);
        $this->controllerAction->setNamespace(self::$namespace);

        $validRoute = self::$routingType === self::STRICT && $this->hasRoute($uri);
        $validRoute |= self::$routingType === self::DYNAMIC && ($this->hasRoute($uri) || $this->parseUri($uri));

        if ($validRoute) {
            $this->controllerAction->call();
        }
    }

    /**
     * Get routes
     * @return array
     */
    public static function getRoutes()
    {
        return self::$routes;
    }

    /**
     * Define routing file to use
     *
     * @param $path
     * @throws RouterException
     */
    public static function setRoutes($path)
    {
        if (file_exists($path)) {
            $use_errors = libxml_use_internal_errors(true);

            $xml = new \DOMDocument();
            $xml->load($path);

            if (!$xml->schemaValidate(self::getXsdPath())) {
                $error = libxml_get_last_error();
                throw new RouterException($error->message);
            } else {
                self::$routes = simplexml_load_file($path);
            }

            libxml_clear_errors();
            libxml_use_internal_errors($use_errors);
        } else {
            throw new RouterException('File ' . $path . ' not found');
        }
    }

    /**
     * Get path to xsd validator for routing file
     * @return string
     */
    public static function getXsdPath()
    {
        return __DIR__ . '/routing.xsd';
    }

    /**
     * Get nanemspace to use for controller
     * @return string
     */
    public static function getNamespace()
    {
        return self::$namespace;
    }

    /**
     * Set nanemspace to use for controller
     * @param string $namespace
     */
    public static function setNamespace($namespace)
    {
        self::$namespace = $namespace;
    }

    /**
     * Get default controller to use
     * @return null
     */
    public static function getDefaultController()
    {
        return self::$defaultController;
    }

    /**
     * Define default controller to use
     * @param null $defaultController
     */
    public static function setDefaultController($defaultController)
    {
        self::$defaultController = $defaultController;
    }

    /**
     * @param $type
     */
    public static function setRoutingType($type)
    {
        self::$routingType = $type == self::DYNAMIC || $type === self::STRICT ? $type : self::DYNAMIC;
    }

    /**
     * @param $uri
     * @return bool|void
     */
    public function hasRoute(&$uri)
    {
        $find = false;
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        // Pour chaque route du fichier de routing
        foreach (self::$routes as $type => $route) {
            if ($type === "any" || $type === $method) {
                // On génère la requête regex de validation
                $regex = '/^' . preg_replace(
                    array('/{(\w+)}/', '/\//'),
                    array('(.+?)', '\/'),
                    $route->path
                ) . '(\?.*){0,1}$/';

                // Si elle correspond à l'URI actuelle
                if (preg_match_all($regex, $uri, $matches)) {
                    $this->defineGetParameters($route->path, $matches);

                    $subFind = $this->computeRoute($route, $matches, $uri, $find);

                    if ($subFind) {
                        break;
                    }

                    $find |= $subFind;
                }
            }
        }

        return $find;
    }

    public function parseUri($uri)
    {
        $find = true;
        // Split URI by slash
        $directories = explode('/', $uri);

        // Shift first empty index
        array_shift($directories);

        // Get action and arguments
        if (end($directories) != "") {
            $actionAndArgs = explode('?', end($directories));
            $this->controllerAction->setAction(Translator::translate($actionAndArgs[0]));
        }
        array_pop($directories);

        // Get controller directory and name
        if (count($directories) > 0) {
            $this->controllerAction->setName(ucfirst(Translator::translate(end($directories))));

            array_pop($directories);
            if (count($directories) > 0) {
                $this->controllerAction->addSubNamespace(
                    implode(
                        '\\',
                        array_filter(Translator::translate($directories))
                    )
                );
            }
        }

        return $find;
    }

    /**
     * @param $path
     * @param array $matches
     */
    private function defineGetParameters($path, $matches = array())
    {
        // On traite l'ensemble des paramètres mis en forme dans l'URI
        preg_match_all('/({)(\w+)(})/', $path, $keys);
        $keys = array_flip($keys[2]);
        $i = 1;
        foreach ($keys as $key => $val) {
            $_GET[$key] = $matches[$i][0];
            $i++;
        }
    }

    /**
     * @param $route
     * @param $matches
     * @param $uri
     * @param $find
     */
    private function computeRoute($route, $matches, &$uri, &$find)
    {
        if ($route->alias) {
            $alias = str_replace(array_map(function ($e) {
                return '{' . $e . '}';
            }, array_keys($_GET)), $_GET, $route->alias);
            $uri = $alias;
        } else {
            if ($route->redirect) {
                header('location:' . $route->redirect . $matches[count($matches) - 1][0]);
            } else {
                $find = true;
                // On définit le controller et l'action correspondant à la route
                $this->controllerAction
                    ->setNamespace(self::$namespace)
                    ->setName($route->controller->name)
                    ->setAction($route->controller->action . '');

                if (isset($route->controller->dir)) {
                    $this->controllerAction
                        ->addSubNamespace($route->controller->dir);
                }
            }
        }
    }
}
