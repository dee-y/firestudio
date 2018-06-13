<?php

namespace Fire\Studio;

use \Mustache_Engine;
use \Fire\Studio;
use \Fire\Bug\Panel\Render as FireBugPanelRender;

class View
{

    use \Fire\Studio\Injector;

    private $_templates;

    private $_partials;

    private $_inlineStyles;

    private $_inlineScripts;

    public function __construct()
    {
        $this->_fireInjector();
        $this->_debug = $this->injector->get(Studio::INJECTOR_DEBUG_PANEL);
        $this->_templates = [];
        $this->_partials = [];
        $this->_inlineStyles = [];
        $this->_inlineScrypts = [];
    }

    public function loadTemplate($id, $pathToTemplate, $loadAsPartial = false)
    {
        $template = file_get_contents($pathToTemplate);
        if (!$loadAsPartial) {
            $this->_templates[$id] = (object) [
                'file' => $pathToTemplate,
                'trace' => debug_backtrace(),
                'template' => $template
            ];
        } else {
            $this->_partials[$id] = (object) [
                'file' => $pathToTemplate,
                'trace' => debug_backtrace(),
                'partial' => $template
            ];
        }
    }

    public function loadInlineStyle($pathToInlineStyle)
    {
        $this->_inlineStyles[] = $pathToInlineStyle;
    }

    public function loadInlineScript($pathToInlineScript)
    {
        $this->_inlineScripts[] = $pathToInlineScript;
    }

    public function getTemplate($id)
    {
        return (isset($this->_templates[$id]->template)) ? $this->_templates[$id]->template : '';
    }

    public function getPartial($id)
    {
        return (isset($this->_partials[$id])) ? $this->_partials[$id] : '';
    }

    public function getTemplates()
    {
        return $this->_templates;
    }

    public function getPartials()
    {
        return $this->_partials;
    }

    public function getInlineStyles()
    {
        return $this->_inlineStyles;
    }

    public function getInlineScripts()
    {
        return $this->_inlineScripts;
    }

    public function render($templateId, $model)
    {
        $partials = [];
        foreach ($this->_partials as $id => $partial) {
            $partials[$id] = $partial->partial;
        }
        $mustache = new Mustache_Engine([
            'partials' => $partials
        ]);
        $model->debugPanel = $this->_debug->render(false);
        $mustacheTemplate = $this->getTemplate($templateId);
        $renderDebug = $this->_debug->getPanel(FireBugPanelRender::ID);
        $renderDebug->setTemplateId($templateId);
        $renderDebug->setModel($model);
        return $mustache->render($mustacheTemplate, $model);
    }

}
