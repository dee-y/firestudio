<?php
namespace Fire\Studio;

class Router {

    private $_routes;
    private $_currentRoute;
    private $_matchedRoute;
    private $_module;
    private $_controller;
    private $_action;
    private $_routeVars;

    public function __construct()
    {
        $this->_routes = [];
        $this->_matchedRoute = false;
        $this->_module = false;
        $this->_controller = false;
        $this->_action = false;
        $this->_id = false;
        $this->_routeVars = [];
    }

    public function when($path, $module, $controller, $action, $id = false)
    {
        $this->_setRoute($path, $module, $controller, $action, $id);
        return $this;
    }

    public function otherwise($module, $controller, $action, $id = false)
    {
        $this->_setRoute('*', $module, $controller, $action, $id);
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

    public function getModule()
    {
        return $this->_module;
    }

    public function getController()
    {
        return $this->_controller;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function getId()
    {
        return $this->_id;
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

    private function _setRoute($path, $module, $controller, $action, $id)
    {
        $this->_routes[$path] = (object) [
            'id' => $id,
            'module' => ($module) ? $module : false,
            'controller' => $controller,
            'action' => $action
        ];
    }

    private function _resolve()
    {
        $routeConfig = $this->_routes;
        $currentRoute = $_SERVER['REQUEST_URI'];
        if (array_key_exists($currentRoute, $routeConfig)) {
            $this->_matchedRoute = $currentRoute;
            $this->_module = $routeConfig[$currentRoute]->module;
            $this->_controller = $routeConfig[$currentRoute]->controller;
            $this->_action = $routeConfig[$currentRoute]->action;
            $this->_id = $routeConfig[$currentRoute]->id;
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
                        $this->_module = $route->module;
                        $this->_controller = $route->controller;
                        $this->_action = $route->action;
                        $this->_id = $route->id;
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
                $this->_module = $routeConfig['*']->module;
                $this->_controller = $routeConfig['*']->controller;
                $this->_action = $routeConfig['*']->action;
                $this->_id = $routeConfig['*']->id;
                return true;
            } else {
                return false;
            }
        }
    }
}
