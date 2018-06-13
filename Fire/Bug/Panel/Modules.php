<?php

namespace Fire\Bug\Panel;

use \Fire\Bug\Panel;

/**
 * This class represents the panel for config to be displayed
 * in the FireBug Panel.
 */
class Modules extends Panel
{
    use \Fire\Studio\Injector;

    /**
     * Constants
     */
    const ID = 'modules';
    const NAME = 'Modules';
    const TEMPLATE = '/modules.phtml';

    private $_modules;

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->_modules = [];
        parent::__construct(self::ID, self::NAME, __DIR__ . self::TEMPLATE);
    }

    public function addModule($module, $trace)
    {
        $this->_modules[] = (object) [
            'module' => $module,
            'trace' => $trace
        ];
    }

    public function getLoadedModules()
    {
        return $this->_modules;
    }
}
