<?php

namespace Fire;

use \Fire\StudioException;
use \Fire\Studio\Module;
use \Fire\Bug;
use \Fire\Bug\Panel\Injector as FireBugPanelInjector;
use \Fire\Studio\Config;
use \Fire\Bug\Panel\Config as FireBugPanelConfig;
use \Fire\Sql;
use \Fire\Studio\Router;
use \Fire\Bug\Panel\Router as FireBugPanelRouter;
use \Fire\Studio\View\Model as ViewModel;
use \Fire\Studio\View;
use \Fire\Bug\Panel\View as FireBugPanelView;
use \Fire\Bug\Panel\Modules as FireBugPanelModules;
use \Fire\Bug\Panel\Render as FireBugPanelRender;
use \PDO;

class Studio
{

    use \Fire\Studio\Injector;

    const INJECTOR_DEBUG_PANEL = 'fire.studio.debug';
    const INJECTOR_CONFIG = 'fire.studio.config';
    const INJECTOR_DATABASE = 'fire.studio.db';
    const INJECTOR_ROUTER = 'fire.studio.router';
    const INJECTOR_VIEW = 'fire.studio.view';
    const INJECTOR_MODEL = 'fire.studio.model';

    private $_config;
    private $_debug;
    private $_router;
    private $_model;
    private $_view;
    private $_db;
    private $_modules;

    public function __construct($appJsonConfig)
    {
        $this->_fireInjector();
        $this->_initInjector();
        $this->_initDebug();
        $this->_initConfig($appJsonConfig);
        $this->_initDb();
        $this->_modules = [];
    }

    public function loadConfig($pathToJsonConfig)
    {
        $this->_config->addConfigFile($pathToJsonConfig);
    }

    public function addModule($moduleClass)
    {
        if (!isset($this->_modules[$moduleClass])) {
            $module = new $moduleClass();
            $module->config();
            $this->_modules[$moduleClass] = $module;
            $this->_debug->getPanel(FireBugPanelModules::ID)->addModule($moduleClass, debug_backtrace());
        }
    }

    public function getModule($moduleClass)
    {
        return $this->_modules[$moduleClass];
    }

    public function run()
    {
        $this->_initModulesFromConfig();
        $this->_setupRoutesFromConfig();
        $this->_resolveRouteInitModule();
        $this->_invokeControllerAction();
    }

    /**
     * ================================================================================
     * Startup Processes
     * ================================================================================
     */

    private function _initInjector()
    {
        $this->injector->set(self::INJECTOR_DEBUG_PANEL, Bug::get());
        $this->_debug = $this->injector->get(self::INJECTOR_DEBUG_PANEL);
        $this->injector->set(self::INJECTOR_CONFIG, new Config());
        $this->_config = $this->injector->get(self::INJECTOR_CONFIG);
        $this->injector->set(self::INJECTOR_ROUTER, new Router());
        $this->_router = $this->injector->get(self::INJECTOR_ROUTER);
        $this->injector->set(self::INJECTOR_MODEL, new ViewModel());
        $this->_model = $this->injector->get(self::INJECTOR_MODEL);
        $this->injector->set(self::INJECTOR_VIEW, new View());
        $this->_view = $this->injector->get(self::INJECTOR_VIEW);
    }

    private function _initDebug()
    {
         //enable debugging
         $this->_debug->enable();

         //add all debug panels
         $this->_debug->addPanel(new FireBugPanelInjector());
         $this->_debug->addPanel(new FireBugPanelConfig());
         $this->_debug->addPanel(new FireBugPanelRouter());
         $this->_debug->addPanel(new FireBugPanelModules());
         $this->_debug->addPanel(new FireBugPanelView());
         $this->_debug->addPanel(new FireBugPanelRender());
    }

    private function _initConfig($appConfig)
    {
        //setup application configurations
        $defaultAppConfig = __DIR__ . '/Studio/Application/Config/application.json';
        $this->loadConfig($defaultAppConfig);
        $this->loadConfig($appConfig);
    }

    private function _initDb()
    {
        /**
         * Uses PDO Application Configuration
         * sqlite:
         * "pdo": {
         *     "adapter": "sqlite",
         *     "location": "path/to/db/location"
         * }
         *
         * mysql:
         * "pdo": {
         *     "adpater": "mysql",
         *     "host": "localhost",
         *     "port": "3306",
         *     "dbname": "firestudio",
         *     "username": "username",
         *     "password": "******"
         * }
         */
        $config = $this->_config->getConfig();
        if (isset($config->pdo)) {
            $adapter = $config->pdo->adapter;
            switch($adapter) {
                case 'sqlite':
                    if (isset($config->pdo->location)) {
                        $dns = $adapter . ':' . $config->pdo->location;
                    }
                break;
                case 'mysql':
                    if (
                        isset($config->pdo->host)
                        && isset($config->pdo->dbname)
                        && isset($config->pdo->usersname)
                        && isset($config->pdo->password)
                    ) {
                        $dns = $adapter . ':'
                            . 'host=' . $config->pdo->host . ';'
                            . 'port=' . (isset($config->pdo->port)) ? $config->pdo->port : '3306' . ';'
                            . 'dbname=' . $config->pdo->dbname;
                        $username = $config->username;
                        $password = $config->password;
                    }
                break;
            }

            if (
                isset($dns)
                && isset($username)
                && isset($password)
            ) {
                $pdo = new PDO($dns, $username, $password);
            } else {
                $pdo = new PDO($dns);
            }
            $this->injector->set(self::INJECTOR_DATABASE, new Sql($pdo));
            $this->_db = $this->injector->get(self::INJECTOR_DATABASE);
        }
    }

    /**
     * ================================================================================
     * Run Processes
     * ================================================================================
     */

    private function _initModulesFromConfig()
    {
        $config = $this->_config->getConfig();
        $modules = (isset($config->modules)) ? $config->modules : [];
        foreach ($modules as $module) {
            $this->addModule($module);
        }
    }

    private function _setupRoutesFromConfig()
    {
        $config = $this->_config->getConfig();
        $routes = (isset($config->routes)) ? $config->routes : [];
        foreach ($routes as $id => $route) {
            $this->_router->when($route->path, $route->module, $route->controller, $route->action, $id);
        }
    }

    private function _resolveRouteInitModule()
    {
        $this->_router->resolve();

        $moduleClass = $this->_router->getModule();
        if ($moduleClass) {
            $this->addModule($moduleClass);
        }
        $this->getModule($moduleClass)->init();
    }

    private function _runModules()
    {
        foreach ($this->_modules as $module) {
            $module->run();
        }
    }

    private function _invokeControllerAction()
    {
        $controllerClass = $this->_router->getController();
        $action = $this->_router->getAction();
        if ($controllerClass && $action) {
            $controller = new $controllerClass();
            $controller->init();
            $controller->{$action}();
        }
    }

}
