<?php
namespace Fire\Studio;

class Router {

    private $_routes;
    private $_currentRoute;
    private $_matchedRoute;
    private $_controller;
    private $_method;
    private $_routeVars;

    public function __construct()
    {
        $this->_routes = [];
        $this->_matchedRoute = false;
        $this->_controller = false;
        $this->_method = false;
        $this->_routeVars = [];
    }

    public function when($path, $controller, $method)
    {
        $this->_setRoute($path, $controller, $method);
        return $this;
    }

    public function otherwise($controller, $method)
    {
        $this->_setRoute('*', $controller, $method);
        return $this;
    }

    public function resolve()
    {
        $this->_resolve();
    }

    public function getRoutes()
    {
        return $this->_routes;
    }

    public function getRoute()
    {
        return $this->_matchedRoute;
    }

    public function getController()
    {
        return $this->_controller;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function getVariables($routeParam = null)
    {
        if ($routeParam) {
            if (isset($this->_routeVars[$routeParam])) {
                return $this->_routeVars[$routeParam];
            }
        } else {
            return $this->_routeVars;
        }
        return false;
    }

    private function _setRoute($path, $controller, $method)
    {
        $this->_routes[$path] = (object) [
            'controller' => $controller,
            'method' => $method
        ];
    }

    private function _resolve()
    {
        $routeConfig = $this->_routes;
        $currentRoute = $_SERVER['REQUEST_URI'];
        if (array_key_exists($currentRoute, $routeConfig)) {
            $this->_matchedRoute = $currentRoute;
            $this->_controller = $routeConfig[$currentRoute]->controller;
            $this->_method = $routeConfig[$currentRoute]->method;
            return true;
        } else {
            //remove url query params and parse route into its parts
            $routeQuery = explode('?', $currentRoute);
            $routeParts = explode('/', substr($routeQuery[0], 1));
            foreach ($routeConfig as $path => $route) {
                if (strpos($path, ':') !== false) {
                    $routeMatch = true;
                    $pathParts = explode('/', substr($path, 1));
                    $i = 0;
                    foreach ($pathParts as $part) {
                        if ($routeMatch) {
                            $routeMatch = false;
                            if (isset($routeParts[$i]) && $routeParts[$i] != '') {
                                if (strpos($part, ':') !== false) {
                                    $routeMatch = true;
                                } elseif ($part == $routeParts[$i]) {
                                    $routeMatch = true;
                                }
                            }
                            $i++;
                        }
                    }
                    if (isset($routeParts[$i])) {
                        $routeMatch = false;
                    }
                    if ($routeMatch) {
                        $this->_matchedRoute = $path;
                        $this->_controller = $route->controller;
                        $this->_method = $route->method;
                        $matchedRoute = explode('/', substr($this->_matchedRoute, 1));
                        $i = 0;
                        foreach ($matchedRoute as $matchedRoutePart) {
                            if (strpos($matchedRoutePart, ':') !== false) {
                                $this->_routeVars[substr($matchedRoutePart, 1)] = $routeParts[$i];
                            }
                            $i++;
                        }
                        return true;
                    }
                }
            }
            if (array_key_exists('*', $routeConfig)) {
                $this->_matchedRoute = '*';
                $this->_controller = $routeConfig['*']->controller;
                $this->_method = $routeConfig['*']->method;
                return true;
            } else {
                return false;
            }
        }
    }
}
