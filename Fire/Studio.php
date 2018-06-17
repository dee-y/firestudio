<?php

namespace Fire;

use \PDO;
use \Fire\Bug;
use \Fire\Bug\Panel\Config as FireBugPanelConfig;
use \Fire\Bug\Panel\Injector as FireBugPanelInjector;
use \Fire\Bug\Panel\Modules as FireBugPanelModules;
use \Fire\Bug\Panel\Render as FireBugPanelRender;
use \Fire\Bug\Panel\Router as FireBugPanelRouter;
use \Fire\Bug\Panel\View as FireBugPanelView;
use \Fire\Studio\Module;
use \Fire\Studio\Service\Config;
use \Fire\Studio\Service\Model as ViewModel;
use \Fire\Studio\Service\Router;
use \Fire\Studio\Service\View;
use \Fire\StudioException;
use \Fire\Sql;

/**
 * This class is responsible for bootstrapping together a FireStudio Application.
 *
 * Process:
 * SETUP
 * 1. Initialize Injector and add Debug, Config, Router, Model, View, and DB objects.
 * 2. Initialize FireBug Debug Panel and add FireStudio specific panels.
 * 3. Adds configs to Config object.
 * 4. Inializes a database connection based on the PDO config.
 * RUN
 * 1. Adds modules from the Config object. NOTE: when a module is added,
 *    its module::config() method will be invoked.
 * 2. Registers routes from the Config object.
 * 3. Resolves the route and adds the module registered with the route.
 *    NOTE: when a module is added, its module::config() method will be invoked.
 * 4. Load all modules by invoking the module::load() method.
 * 5. Invokes module::run() based on the module registered with the resolved route
 *    then invokes the controller and action based on the resolved route.
 */
class Studio
{

    use \Fire\Studio\Injector;

    const INJECTOR_DEBUG_PANEL = 'fire.studio.debug';
    const INJECTOR_CONFIG = 'fire.studio.config';
    const INJECTOR_ROUTER = 'fire.studio.router';
    const INJECTOR_MODEL = 'fire.studio.model';
    const INJECTOR_VIEW = 'fire.studio.view';
    const INJECTOR_DATABASE = 'fire.studio.db';

    private $_config;
    private $_debug;
    private $_router;
    private $_model;
    private $_view;
    private $_db;
    private $_modules;
    private $_plugins;

    public function __construct($appJsonConfig)
    {
        $this->_initInjector();
        $this->_initDebug();
        $this->_initConfig($appJsonConfig);
        $this->_initDb();
        $this->_modules = [];
        $this->_plugins = [];
    }

    public function run()
    {
        $this->_addModulesFromConfig();
        $this->_setupRoutesFromConfig();
        $this->_resolveRouteAndAddModule();
        $this->_loadAllModules();
        $this->_invokeModuleRunControllerAction();
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

    /**
     * ================================================================================
     * Startup Processes
     * ================================================================================
     */

    private function _initInjector()
    {
        $this->injector()->set(self::INJECTOR_DEBUG_PANEL, Bug::get());
        $this->_debug = $this->injector()->get(self::INJECTOR_DEBUG_PANEL);
        $this->injector()->set(self::INJECTOR_CONFIG, new Config());
        $this->_config = $this->injector()->get(self::INJECTOR_CONFIG);
        $this->injector()->set(self::INJECTOR_ROUTER, new Router());
        $this->_router = $this->injector()->get(self::INJECTOR_ROUTER);
        $this->injector()->set(self::INJECTOR_MODEL, new ViewModel());
        $this->_model = $this->injector()->get(self::INJECTOR_MODEL);
        $this->injector()->set(self::INJECTOR_VIEW, new View());
        $this->_view = $this->injector()->get(self::INJECTOR_VIEW);
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
            $this->injector()->set(self::INJECTOR_DATABASE, new Sql($pdo));
            $this->_db = $this->injector()->get(self::INJECTOR_DATABASE);
        }
    }

    /**
     * ================================================================================
     * Run Processes
     * ================================================================================
     */

    private function _addModulesFromConfig($addedModules = [])
    {
        $config = $this->_config->getConfig();
        $modules = (isset($config->modules)) ? $config->modules : [];
        foreach ($modules as $module) {
            $this->addModule($module);
        }
        if ($modules !== $addedModules) {
            $this->_addModulesFromConfig($modules);
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

    private function _resolveRouteAndAddModule()
    {
        $this->_router->resolve();
        $moduleClass = $this->_router->getModule();
        if ($moduleClass) {
            $this->addModule($moduleClass);
        }
    }

    private function _loadAllModules()
    {
        foreach ($this->_modules as $module) {
            $module->load();
        }
    }

    private function _invokeModuleRunControllerAction()
    {
        $moduleClass = $this->_router->getModule();
        $this->getModule($moduleClass)->run();
        $controllerClass = $this->_router->getController();
        $action = $this->_router->getAction();
        if ($controllerClass && $action) {
            $controller = new $controllerClass();
            $controller->init();
            $controller->{$action}();
        }
    }

}
