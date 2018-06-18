<?php

namespace Fire\Studio\Service;

use Fire\StudioException;
use Fire\Studio\DataMapper;

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
        $this->_config = DataMapper::mergeObjRecursively($this->_config, $addConfig);
    }
}
