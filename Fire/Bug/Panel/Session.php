<?php

namespace Fire\Bug\Panel;

use \Fire\Bug\Panel;

/**
 * This class represents the panel for config to be displayed
 * in the FireBug Panel.
 */
class Session extends Panel
{
    use \Fire\Studio\Injector;

    /**
     * Constants
     */
    const ID = 'session';
    const NAME = 'Session';
    const TEMPLATE = '/session.phtml';

    private $_view;

    /**
     * The constructor
     */
    public function __construct()
    {
        parent::__construct(self::ID, self::NAME, __DIR__ . self::TEMPLATE);
    }

    public function getSessionData()
    {
        return (object) $_SESSION;
    }
}
