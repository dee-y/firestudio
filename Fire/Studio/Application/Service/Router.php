<?php
namespace Fire\Studio\Application\Service;

class Router {

    private $_routes;
    private $_currentRoute;
    private $_matchedRoute;
    private $_method;
    private $_isAjaxRequest;
    private $_module;
    private $_controller;
    private $_action;
    private $_routeVars;

    public function __construct()
    {
        $this->_routes = [];
        $this->_matchedRoute = false;
        $this->_isAjaxRequest = false;
        $this->_method = false;
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

    public function getUrl($id, $params = [])
    {
        $paramKeys = array_keys($params);
        $replaceKeys = [];
        foreach ($paramKeys as $key) {
            $replaceKeys[] = ':' . $key;
        }
        foreach ($this->_routes as $path => $route) {
            if ($id === $route->id) {
                return str_replace($replaceKeys, $params, $path);
            }
        }
        return false;
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

    public function isAjaxRequest()
    {
        return $this->_isAjaxRequest;
    }

    public function getRequestMethod()
    {
        return $this->_method;
    }

    public function setModule($module)
    {
        $this->_module = $module;
    }

    public function getModule()
    {
        return $this->_module;
    }

    public function setController($controller)
    {
        $this->_controller = $controller;
    }

    public function getController()
    {
        return $this->_controller;
    }

    public function setAction($action)
    {
        $this->_action = $action;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getParams($routeParam = null)
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
            'module' => $module,
            'controller' => $controller,
            'action' => $action
        ];
    }

    private function _resolve()
    {
        $this->_method = strtoupper($_SERVER['REQUEST_METHOD']);
        $routeConfig = $this->_routes;
        $requestUri = $_SERVER['REQUEST_URI'];
        //remove ? from the route matching equation
        $route = (strpos($requestUri, '?') !== false)
            ? substr($requestUri, 0, strpos($requestUri, '?')) : $requestUri;
        //remove trailing / from the route matching equation
        $currentRoute = substr($route, -1) === '/'
            ? substr($route, 0, -1) : $route;
        if (array_key_exists($currentRoute, $routeConfig)) {
            $this->_matchedRoute = $currentRoute;
            $this->_module = $routeConfig[$currentRoute]->module;
            $this->_controller = $routeConfig[$currentRoute]->controller;
            $this->_action = $this->_getAdjustedAction($routeConfig[$currentRoute]->action);
            $this->_id = $routeConfig[$currentRoute]->id;
            return true;
        } else {
            //remove url query params and parse route into its parts
            $routeParts = explode('/', substr($currentRoute, 1));
            foreach ($routeConfig as $path => $route) {
                $routeMatch = false;
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
                }
                if ($routeMatch) {
                    $this->_matchedRoute = $path;
                    $this->_module = $route->module;
                    $this->_controller = $route->controller;
                    $this->_action = $this->_getAdjustedAction($route->action);
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
            if (array_key_exists('*', $routeConfig)) {
                $this->_matchedRoute = '*';
                $this->_module = $routeConfig['*']->module;
                $this->_controller = $routeConfig['*']->controller;
                $this->_action = $this->_getAdjustedAction($routeConfig['*']->action);
                $this->_id = $routeConfig['*']->id;
                return true;
            } else {
                return false;
            }
        }
    }

    private function _getAdjustedAction($action)
    {
        $method = ($this->getRequestMethod() !== 'GET') ? $this->getRequestMethod() : '';
        $ajax = ($this->isAjaxRequest()) ? 'AJAX' : '';
        return $action . $method . $ajax;
    }
}
