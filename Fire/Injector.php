<?php
namespace Fire;

use Fire\StudioException;

class Injector {

    static public $injector;

    private $_objects;

    private function __construct() {
        $this->_objects = [];
    }

    public function set($name, $object) {
        $this->_objects[$name] = $object;
    }

    public function get($name) {
        if (!isset($this->_objects[$name])) {
            throw new StudioException('Could not find object "' . $name . '" in the injector.');
        }
        return $this->_objects[$name];
    }

    public function has($name) {
        return isset($this->_objects[$name]);
    }

    public function debug() {
        return $this->_objects;
    }

    static public function instance() {
        if (!self::$injector) {
            self::$injector = new self();
        }
        return self::$injector;
    }

}
