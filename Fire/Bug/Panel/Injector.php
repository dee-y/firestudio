<?php

namespace Fire\Bug\Panel;

use \Fire\Bug\Panel;

/**
 * This class represents the panel for config to be displayed
 * in the FireBug Panel.
 */
class Injector extends Panel
{
    use \Fire\Studio\Injector;

    /**
     * Constants
     */
    const ID = 'injector';
    const NAME = 'Injector';
    const TEMPLATE = '/injector.phtml';

    /**
     * The constructor
     */
    public function __construct()
    {
        
        parent::__construct(self::ID, self::NAME, __DIR__ . self::TEMPLATE);
    }

    public function getInjector()
    {
        return $this->injector()->debug();
    }
}
