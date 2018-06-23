<?php

namespace Fire\Bug\Panel;

use \Fire\Bug\Panel;
use \Fire\Studio;

/**
 * This class represents the panel for config to be displayed
 * in the FireBug Panel.
 */
class Config extends Panel
{
    use \Fire\Studio\Injector;

    /**
     * Constants
     */
    const ID = 'config';
    const NAME = 'Config';
    const TEMPLATE = '/config.phtml';

    /**
     * Firestudio config service
     * @var \Fire\Studio\Service\Config
     */
    private $_config;

    /**
     * The constructor
     */
    public function __construct()
    {
        
        $this->_config = $this->injector()->get(Studio::INJECTOR_CONFIG);
        parent::__construct(self::ID, self::NAME, __DIR__ . self::TEMPLATE);
    }

    public function getConfig()
    {
        return $this->_config->getConfig();
    }

    public function getLoadedConfigFiles()
    {
        return $this->_config->getLoadedConfigFiles();
    }
}
