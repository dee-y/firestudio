<?php

namespace Fire\Bug\Panel;

use \Fire\Bug\Panel;
use \Fire\Studio;

/**
 * This class represents the panel for config to be displayed
 * in the FireBug Panel.
 */
class View extends Panel
{
    use \Fire\Studio\Injector;

    /**
     * Constants
     */
    const ID = 'view';
    const NAME = 'Templates';
    const TEMPLATE = '/view.phtml';

    private $_view;

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->_fireInjector();
        $this->_view = $this->injector->get(Studio::INJECTOR_VIEW);
        parent::__construct(self::ID, self::NAME, __DIR__ . self::TEMPLATE);
    }

    public function getTemplates()
    {
        return $this->_view->getTemplates();
    }
}
