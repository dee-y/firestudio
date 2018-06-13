<?php

namespace Fire\Bug\Panel;

use \Fire\Bug\Panel;

/**
 * This class represents the panel for config to be displayed
 * in the FireBug Panel.
 */
class Render extends Panel
{
    use \Fire\Studio\Injector;

    private $_templateId;
    private $_templateModel;

    /**
     * Constants
     */
    const ID = 'render';
    const NAME = 'Render';
    const TEMPLATE = '/render.phtml';

    /**
     * The constructor
     */
    public function __construct()
    {
        parent::__construct(self::ID, self::NAME, __DIR__ . self::TEMPLATE);
        $this->_templateId = false;
        $this->_templateModel = false;
    }

    public function setTemplateId($templateId)
    {
        $this->_templateId = $templateId;
    }

    public function getTemplateId()
    {
        return $this->_template;
    }

    public function setModel($model)
    {
        $this->_templateModel = $model;
    }

    public function getModel()
    {
        return $this->_templateModel;
    }
}
