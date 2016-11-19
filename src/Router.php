<?php namespace Jacko;

/**
 * Class Router
 * @package Jacko
 */
class Router
{

    /**
     * Path to routes file
     * @var
     */
    public $path;

    /**
     * @var
     */
    private static $router;

    /**
     * Register a new route responding to all verbs.
     * @param $uri
     * @param $action
     */
    public function any($uri, $action)
    {
        return $this->addRoute(['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'], $uri, $action);
    }

    /**
     * Register a new GET route with the router.
     * @param $uri
     * @param $action
     */
    public function get($uri, $action)
    {
        return $this->addRoute(['GET', 'HEAD'], $uri, $action);
    }

    /**
     * Register a new POST route with the router.
     * @param $uri
     * @param $action
     */
    public function post($uri, $action)
    {
        return $this->addRoute(['POST'], $uri, $action);
    }

    /**
     * @param $methods
     * @param $uri
     * @param $action
     */
    protected function addRoute($methods, $uri, $action)
    {
        self::$router->buildRoute($methods, $uri, $action);
    }

    /**
     * Run router
     */
    public function start()
    {
        self::$router = new Route();

        if (!class_exists('\Route'))
        {
            class_alias('\Jacko\Router', '\Route');
        }

        require_once( $this->path );

        if (empty(self::$router->routes))
        {
            throw new \RuntimeException('No route');
        }

        self::$router = null;
    }

    /**
     * Magic method _call makes possible to call Router::get();
     * @param $name
     * @param $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        return call_user_func_array([self::$router, $name], $args);
    }

}