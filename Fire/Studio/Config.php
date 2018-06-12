<?php

namespace Fire\Studio;

use Fire\StudioException;

class Config
{

    private $_config;

    public function __construct()
    {
        $this->_config = (object) [];
    }

    public function addJsonConfig($config)
    {
        $addConfig = json_decode($config);
        $isJsonConfig = (json_last_error() === JSON_ERROR_NONE) ? true : false;
        if (!$isJsonConfig) {
            throw new StudioException('Config must be a JSON string.');
        }

        $this->_addConfig($addConfig);
    }

    public function getConfig()
    {
        return $this->_config;
    }

    private function _addConfig($addConfig)
    {
        $currentConfig = $this->_config;
        $addConfig = $addConfig;
        $this->_config = $this->_mergeRecursively($currentConfig, $addConfig);
        //$this->_config = (object) $this->_mergeArrayRecursively($currentConfig, $addConfig);
    }

    private function _mergeRecursively($obj1, $obj2) {
        if (is_object($obj2)) {
            $keys = array_keys(get_object_vars($obj2));
            foreach ($keys as $key) {
                if (
                    isset($obj1->{$key})
                    && is_object($obj1->{$key})
                    && is_object($obj2->{$key})
                ) {
                    $this->_mergeRecursively($obj1->{$key}, $obj2->{$key});
                } else {
                    $obj1->{$key} = $obj2->{$key};
                }
            }
        } elseif (is_array($obj2)) {
            $keys = array_keys($obj2);
            foreach ($keys as $key) {
                if (
                    isset($obj1[$key])
                    && is_object($obj1[$key])
                    && is_object($obj2[$key])
                ) {
                    $this->_mergeRecursively($obj1[$key], $obj2[$key]);
                } else {
                    $obj1[$key] = $obj2[$key];
                }
            }
        }

        return $obj1;
    }

    private function _mergeArrayRecursively($arr1, $arr2)
    {
        $keys = array_keys($arr2);
        foreach($keys as $key) {
            if(
                isset($arr1[$key])
                && is_array($arr1[$key])
                && is_array($arr2[$key])
            ) {
                $arr1[$key] = $this->_mergeArrayRecursively($arr1[$key], $arr2[$key]);
            } else {
                $arr1[$key] = $arr2[$key];
            }
        }
        return $arr1;
    }

}
