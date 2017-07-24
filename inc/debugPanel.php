<?php

namespace firestudio;

use firestudio\view;

class debugPanel extends view {

    protected $_panels;

    public function __construct() {
        parent::__construct();
        $this->_panels = [];
        $this->debuggers = [];
    }

    public function addPanel($panel) {
        $this->_panels[] = $panel;
    }

    public function renderPanel() {
        $this->injectorDebug = $this->injector->debug();
        $this->render(__DIR__ . '/templates/debug/debug-panel.phtml', false);
    }

}
