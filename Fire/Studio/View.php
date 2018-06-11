<?php

namespace Fire\Studio;

use \Mustache_Engine;
use \Fire\Studio;

class View
{

    use \Fire\Studio\Injector;

    private $_mustache;

    private $_templates = [];

    public function __construct()
    {
        $this->_fireInjector();
        $this->_debug = $this->injector->get(Studio::INJECTOR_DEBUG_PANEL);
        $this->_mustache = new Mustache_Engine();
        $this->_templates = [];
    }

    public function loadTemplate($id, $pathToTemplate)
    {
        $template = file_get_contents($pathToTemplate);
        $this->_templates[$id] = $template;
    }

    public function getTemplate($id)
    {
        return (isset($this->_templates[$id])) ? $this->_templates[$id] : '';
    }

    public function getTemplates()
    {
        return $this->_templates;
    }

    public function render($template, $model = [])
    {
        $model['debugPanel'] = $this->_debug->render(false);
        $mustacheTemplate = $this->_templates[$template];
        return $this->_mustache->render($mustacheTemplate, $model);
    }

}
