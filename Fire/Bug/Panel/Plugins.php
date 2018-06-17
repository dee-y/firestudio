<?php

namespace Fire\Bug\Panel;

use \Fire\Bug\Panel;

/**
 * This class represents the panel for config to be displayed
 * in the FireBug Panel.
 */
class Plugins extends Panel
{
    use \Fire\Studio\Injector;

    /**
     * Constants
     */
    const ID = 'plugins';
    const NAME = 'Plugins';
    const TEMPLATE = '/plugins.phtml';

    private $_plugins;

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->_plugins = [];
        parent::__construct(self::ID, self::NAME, __DIR__ . self::TEMPLATE);
    }

    public function addPlugin($plugin, $trace)
    {
        $this->_plugins[] = (object) [
            'module' => $plugin,
            'trace' => $trace
        ];
    }

    public function getLoadedPlugins()
    {
        return $this->_plugins;
    }
}
