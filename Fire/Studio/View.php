<?php

namespace Fire\Studio;

use \Mustache_Engine;
use \Fire\Studio;
use \Fire\Bug\Panel\Render as FireBugPanelRender;

class View
{

    use \Fire\Studio\Injector;

    private $_debug;

    private $_config;

    private $_templates;

    private $_partials;

    private $_inlineStyles;

    private $_inlineScripts;

    public $model;

    public function __construct()
    {
        $this->_fireInjector();
        $this->_debug = $this->injector->get(Studio::INJECTOR_DEBUG_PANEL);
        $this->_config = $this->injector->get(Studio::INJECTOR_CONFIG);
        $this->model = $this->injector->get(Studio::INJECTOR_MODEL);
        $this->_templates = [];
        $this->_partials = [];
        $this->_inlineStyles = [];
        $this->_inlineScripts = [];
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

    public function addInlineStyle($id, $pathToInlineStyle)
    {
        $style = file_get_contents($pathToInlineStyle);
        $this->_inlineStyles[$id] = (object) [
            'file' => $pathToInlineStyle,
            'trace' => debug_backtrace(),
            'style' => $style
        ];
    }

    public function addInlineScript($id, $pathToInlineScript)
    {
        $script = file_get_contents($pathToInlineScript);
        $this->_inlineScripts[$id] = (object) [
            'file' => $pathToInlineScript,
            'trace' => debug_backtrace(),
            'style' => $script
        ];
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

    public function render($templateId)
    {
        $config = $this->_config->getConfig();
        //initialize partials into mustache templates.
        $partials = [];
        foreach ($this->getPartials() as $id => $partial) {
            $partials[$id] = $partial->partial;
        }
        $mustache = new Mustache_Engine([
            'partials' => $partials
        ]);

        //initalize inline styles
        $this->model->inlineStyles = '<style type="text/css">' . "\n";
        foreach ($this->getInlineStyles() as $style) {
            $this->model->inlineStyles .= $style->style;
            $this->model->inlineStyles .= "\n";
        }
        $this->model->inlineStyles .= '</style>';

        //initialize inline scripts
        $this->model->inlineScripts = '<script type="text/javascript">' . "\n";
        foreach ($this->getInlineScripts() as $script) {
            $this->model->inlineScripts .= $script->style;
            $this->model->inlineScripts .= "\n";
        }
        $this->model->inlineScripts .= '</script>';

        //initialize logo and footerText
        $this->model->logo = $config->logo;
        $this->model->footerText = $config->footerText;

        //add template and model data to render debug panel.
        $renderDebugPanel = $this->_debug->getPanel(FireBugPanelRender::ID);
        $renderDebugPanel->setTemplateId($templateId);
        $renderDebugPanel->setModel($this->model);

        $this->model->debugPanel = $this->_debug->render(false);
        $mustacheTemplate = $this->getTemplate($templateId);
        return $mustache->render($mustacheTemplate, $this->model);
    }

}
