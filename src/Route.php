<?php namespace Jacko;

use Jacko\DependencyInjection\Injector;

/**
 * Class Route
 * @package Jacko
 */
class Route
{

    /**
     * The HTTP method
     * @var string
     */
    protected $methods = array();

    /**
     * The action to call after a match
     * @var string
     */
    protected $action;

    /**
     * The pattern to match
     * @var string
     */
    protected $pattern;

    /**
     * The URI pattern the route responds to.
     * @var string
     */
    protected $regex;

    /**
     * The URI pattern the route responds to.
     * @var string
     */
    protected $parameter_values = array();

    /**
     * The parameters from the uri
     * @var string
     */
    protected $parameters = [];

    /**
     * @param $methods
     * @param $pattern
     * @param $action
     * @return mixed
     */
    public function buildRoute($methods, $pattern, $action)
    {
        $this->methods = $methods;
        $this->pattern = $pattern;
        $this->action = $action;

        $url = rtrim(strtok($_SERVER["REQUEST_URI"], '?'), '/');
        $method = $_SERVER['REQUEST_METHOD'];

        if ($this->matches($url, $method)) {
            $this->routes[] = $method;
            return $this->dispatch();
        }
    }

    /**
     * @param $url
     * @param $method
     * @return bool
     */
    public function matches($url, $method)
    {
        if (!in_array($method, $this->methods))
        {
            return false;
        }

        $url = rtrim($url, '/');
        $regex 	= preg_replace_callback('/{([\w]+)}/', array($this, 'getRegex'), $this->pattern);
        $regex 	= '#^' . $regex . '?$#';

        if (!preg_match($regex, $url, $matches))
        {
            return false;
        }

        foreach ($this->parameters as $name => &$value)
        {
            if (isset($matches[$name]))
            {
                $value = $matches[$name];
            }
        }

        return true;
    }

    /**
     * @param $matches
     * @return string
     */
    public function getRegex($matches)
    {
        $match = $matches[1];
        $this->parameters[$match] = null;

        return '(?<'.$match.'>[^/]+)';
    }

    /**
     * dispatch the route
     * @return mixed
     */
    public function dispatch()
    {
        if(is_object($this->action))
            return call_user_func_array($this->action, $this->parameters);

        list($class, $method) = explode('@', $this->action);

        if ($method) {
            (new Injector())->register($class, $method, $this->parameters);
        }
    }
}