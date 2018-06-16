<?php

namespace Fire\Studio\Application\Module\AdminModule;

use \Fire\Studio;

class MenuItem
{
    use \Fire\Studio\Injector;

    public $id;
    public $title;
    public $url;
    public $active;

    public function __construct($title, $id, $params = [])
    {
        $this->_fireInjector();
        $router = $this->injector->get(Studio::INJECTOR_ROUTER);
        $this->id = $id;
        $this->title = $title;
        $this->url = $router->getUrl($id, $params);
        $this->active = $router->getId() === $id;
    }

}
