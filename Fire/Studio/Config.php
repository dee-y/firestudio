<?php

namespace Fire\Studio;

use Fire\StudioException;

class Config
{

    private $_config;

    private $_loadedConfigFiles;

    public function __construct()
    {
        $this->_config = (object) [];
        $this->_loadedConfigFiles = [];
    }

    public function addConfigFile($pathToJsonFile)
    {
        $jsonConfig = file_get_contents($pathToJsonFile);
        $this->_loadedConfigFiles[] = (object) [
            'config' => $pathToJsonFile,
            'trace' => debug_backtrace(),
            'fileContent' => $jsonConfig
        ];

        $this->addJsonConfig($jsonConfig);
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

    public function addObjectConfig($config)
    {
        $this->_addConfig($config);
    }

    public function getLoadedConfigFiles()
    {
        return $this->_loadedConfigFiles;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    private function _addConfig($addConfig)
    {
        $this->_config = $this->_mergeRecursively($this->_config, $addConfig);
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
                    $obj1->{$key} = $this->_mergeRecursively($obj1->{$key}, $obj2->{$key});
                } elseif (isset($obj1->{$key})
                && is_array($obj1->{$key})
                && is_array($obj2->{$key})) {
                    $obj1->{$key} = $this->_mergeRecursively($obj1->{$key}, $obj2->{$key});
                } else {
                    $obj1->{$key} = $obj2->{$key};
                }
            }
        } elseif (is_array($obj2)) {
            if (
                is_array($obj1)
                && is_array($obj2)
            ) {
                $obj1 = array_merge_recursive($obj1, $obj2);
            } else {
                $obj1 = $obj2;
            }
        }

        return $obj1;
    }
}
