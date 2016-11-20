<?php namespace Jacko\DependencyInjection;

/**
 * Class Injector
 * @package Jacko\DependencyInjection
 */
class Injector
{

    private $reflected = array();
    private $instances = array();
    private $mappings = array();
    private $parameters = array();

    /**
     * @param $key
     * @param array $value
     */
    public function addParameters($key, array $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @param $key
     * @param array $value
     */
    public function addMap($key, array $value)
    {
        $this->mappings[$key] = $value;
    }

    /**
     * @param $class
     * @param null $method
     * @param null $params
     * @return mixed
     */
    public function register($class, $method=null, $params=null)
    {
        if (!isset($this->reflected[$class]))
            $this->reflected[$class] = new \ReflectionClass($class);

        $reflectionClass = $this->reflected[$class];
        if ($reflectionClass instanceof \ReflectionClass && $reflectionClass->isInstantiable()) {
            if (!isset($this->instances[$reflectionClass->name])) {
                $this->constructorInjection($reflectionClass);
                $this->setterInjection($reflectionClass, $method, $params);
            }
        }

        return $this->instances[$reflectionClass->name];
    }

    /**
     * @param \ReflectionClass $reflectionClass
     */
    private function constructorInjection(\ReflectionClass $reflectionClass)
    {
        $parameters = array();
        $reflectionConstructor = $reflectionClass->getConstructor();
        if (!is_null($reflectionConstructor))
            $parameters = $this->parseReflectionParameters($reflectionConstructor->getParameters());

        if (isset($parameters) && count($parameters) > 0) {
            $this->instances[$reflectionClass->name] = $reflectionClass->newInstanceArgs($parameters);
        } else {
            $this->instances[$reflectionClass->name] = $reflectionClass->newInstance();
        }
    }

    /**
     * @param $reflectionParameters
     * @param null $params
     * @return array
     */
    private function parseReflectionParameters($reflectionParameters, $params=null)
    {
        $parameters = array();
        $pattern = '/Parameter #\d+ \[ <(?:[A-Za-z]+)>(?: (?P<class>[a-zA-Z\\\\_\x7f-\xff][a-zA-Z0-9\\\\_\x7f-\xff]*)'
            . '(?: or [A-Za-z]+)?)? \$(?P<variable>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(?: = [A-Za-z]+)? \]/';

        foreach ($reflectionParameters as $reflectionParameter) {
            if ($reflectionParameter instanceof \ReflectionParameter) {
                $className = $reflectionParameter->getDeclaringClass()->name;
                preg_match($pattern, $reflectionParameter, $matches);
                if (isset($matches['class']) && !empty($matches['class']) && $matches['class'] != "array") {
                    if ((isset($this->mappings[$className])) && isset($this->mappings[$className][$matches['class']])) {
                        if (isset($this->instances[$matches['class']])) {
                            $parameters[] = $this->instances[$this->mappings[$className][$matches['class']]];
                        } else {
                            $parameters[] = $this->register($this->mappings[$className][$matches['class']]);
                        }
                    } else {
                        if (isset($this->instances[$matches['class']])) {
                            $parameters[] = $this->instances[$matches['class']];
                        } else {
                            $parameters[] = $this->register($matches['class']);
                        }

                    }
                } else {
                    if (isset($matches['variable'])) {
                        if (isset($this->parameters[$className][$matches['variable']])) {
                            $parameters[] = $this->parameters[$className][$matches['variable']];
                        }
                        else
                        {
                            $parameters[] = !empty($params[$matches['variable']]) ? $params[$matches['variable']] : null;
                        }
                    }

                }
            }
        }

        return $parameters;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @param $method
     * @param $params
     */
    private function setterInjection(\ReflectionClass $reflectionClass, $method, $params)
    {
        if(!empty($method))
        {
            $reflectionMethod = $reflectionClass->getMethod($method);

            if ($reflectionMethod->isPublic() && !$reflectionMethod->isConstructor() && !$reflectionMethod->isDestructor() ) {
                $parameters = $this->parseReflectionParameters($reflectionMethod->getParameters(), $params);

                //if (isset($parameters) && count($parameters) > 0)
                $reflectionMethod->invokeArgs($this->instances[$reflectionClass->name], $parameters);

            }
        }
    }

    /**
     * used to register already set up objects into the di-container
     * which is useful if those objects take care of their dependencies
     * on their own
     *
     * @param $identifier
     * @param $object
     */
    public function addInstance($identifier, $object)
    {
        $this->instances[$identifier] = $object;
    }

}
