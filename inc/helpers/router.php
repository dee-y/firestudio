<?php

namespace firestudio\helpers;

class router {
    private $_routes;
    private $_currentRoute;
    private $_matchedRoute;
    private $_routeVars;
    public function __construct() {
        $this->_routes = [];
        $this->_currentRoute = $_SERVER['REQUEST_URI'];
        $this->_matchedRoute = false;
        $this->_routeVars = [];
    }

    public function when($path, $controller, $method) {
        $this->_setRoute($path, $controller, $method);
        return $this;
    }

    public function otherwise($controller, $method) {
        $this->_setRoute('*', $controller, $method);
        return $this;
    }

    public function redirect($path, $responseCode = 302) {
        if (substr($path, 0, 1) != '/') {
            $path = '/' . $path;
        }
        http_response_code($responseCode);
        header('location:' . $path);
    }

    public function resolve() {
        $routeConfig = $this->_routes;
        $currentRoute = $this->getCurrentRoute();
        if (array_key_exists($currentRoute, $routeConfig)) {
            $this->_matchedRoute = $currentRoute;
            return $routeConfig[$currentRoute];
        } else {
            //remove url query params and parse route into its parts
            $routeQuery = explode('?', $currentRoute);
            $routeParts = explode('/', substr($routeQuery[0], 1));
            foreach ($routeConfig as $path => $controller) {
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
                        $matchedRoute = explode('/', substr($this->_matchedRoute, 1));
                        $i = 0;
                        foreach ($matchedRoute as $matchedRoutePart) {
                            if (strpos($matchedRoutePart, ':') !== false) {
                                $this->_routeVars[substr($matchedRoutePart, 1)] = $routeParts[$i];
                            }
                            $i++;
                        }
                        return $controller;
                    }
                }
            }
            if (array_key_exists('*', $routeConfig)) {
                $this->_matchedRoute = '*';
                return $routeConfig['*'];
            } else {
                return false;
            }
        }
    }

    public function getRouteVars($routeParam = null) {
        if ($routeParam) {
            if (isset($this->_routeVars[$routeParam])) {
                return $this->_routeVars[$routeParam];
            }
        } else {
            return $this->_routeVars;
        }
        return false;
    }

    public function getCurrentRoute() {
        return $this->_currentRoute;
    }

    public function getMatchedRoute() {
        return $this->_matchedRoute;
    }

    public function getRouteConfig() {
        return $this->_routes;
    }

    private function _setRoute($path, $controller, $method) {
        $this->_routes[$path] = (object) [
            'controller' => $controller,
            'method' => $method
        ];
    }
}
