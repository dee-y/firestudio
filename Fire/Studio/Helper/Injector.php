<?php
namespace Fire\Studio\Helper;

use Fire\FireStudioException;

class Injector {

    static public $injector;

    protected $_objects;

    private function __construct() {
        $this->_objects = [];
    }

    public function set($name, $object) {
        $this->_objects[$name] = $object;
    }

    public function get($name) {
        if (!isset($this->_objects[$name])) {
            throw new FireStudioException('Could not find object "' . $name . '" in the injector.');
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
