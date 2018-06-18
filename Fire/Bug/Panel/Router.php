<?php

namespace Fire\Bug\Panel;

use \Fire\Bug\Panel;
use \Fire\Studio;

/**
 * This class represents the panel for config to be displayed
 * in the FireBug Panel.
 */
class Router extends Panel
{
    use \Fire\Studio\Injector;

    /**
     * Constants
     */
    const ID = 'router';
    const NAME = 'Router';
    const TEMPLATE = '/router.phtml';

    /**
     * The constructor
     */
    public function __construct()
    {

        parent::__construct(self::ID, self::NAME, __DIR__ . self::TEMPLATE);
    }

    public function getRouterDebug()
    {
        $router = $this->injector()->get(Studio::INJECTOR_ROUTER);
        return (object) [
            'routes' => $router->getRoutes(),
            'route' => $router->getRoute(),
            'method' => $router->getRequestMethod(),
            'module' => $router->getModule(),
            'controller' => $router->getController(),
            'action' => $router->getAction(),
            'variables' => $router->getVariables(),
            'id' => $router->getId()
        ];
    }
}
