<?php

namespace Fire\Studio;

use Fire\Studio;
use Fire\FireStudioException;

class View
{

    use \Fire\Studio\Injector;

    public function __construct()
    {
        $this->_initInjector();
    }

    public function assign(array $model)
    {
        foreach ($model as $property => $value) {
            $this->{$property} = $value;
        }
    }

    public function render($template, $return = false)
    {
        if (!file_exists($template)) {
            $errors = $this->injector->get(fire::INJECTOR_ERRORS);
            $msg = $errors['view.templateNotFound'];
            throw new error(sprintf($msg, $template));
        }
        ob_start();
        include $template;
        ob_end_flush();
    }

    public function head()
    {

    }

    public function footer()
    {
        $debugPanel = $this->injector->get(fire::INJECTOR_DEBUG_PANEL);
        $config = $this->injector->get(fire::INJECTOR_CONFIG);

        if (!empty($config['debug'])) {
            $debugPanel->renderPanel();
        }
    }

}
