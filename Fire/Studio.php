<?php

namespace Fire;

use \PDO;
use \Fire\Bug;
use \Fire\Bug\Panel\Config as FireBugPanelConfig;
use \Fire\Bug\Panel\Injector as FireBugPanelInjector;
use \Fire\Bug\Panel\Plugins as FireBugPanelPlugins;
use \Fire\Bug\Panel\Modules as FireBugPanelModules;
use \Fire\Bug\Panel\Render as FireBugPanelRender;
use \Fire\Bug\Panel\Router as FireBugPanelRouter;
use \Fire\Bug\Panel\Session as FireBugPanelSession;
use \Fire\Bug\Panel\View as FireBugPanelView;
use \Fire\Studio\Module;
use \Fire\Studio\Plugin;
use \Fire\Studio\Service\Config;
use \Fire\Studio\Service\Model as ViewModel;
use \Fire\Studio\Service\Router;
use \Fire\Studio\Service\View;
use \Fire\StudioException;
use \Fire\Sql;

class Studio
{

    use \Fire\Studio\Injector;

    const INJECTOR_DEBUG_PANEL = 'fire.studio.debug';
    const INJECTOR_CONFIG = 'fire.studio.config';
    const INJECTOR_ROUTER = 'fire.studio.router';
    const INJECTOR_MODEL = 'fire.studio.model';
    const INJECTOR_VIEW = 'fire.studio.view';
    const INJECTOR_DATABASE = 'fire.studio.db';

    const SESSION_MESSAGE_KEY = 'fsmessage';
    const SESSION_ERRORS_KEY = 'fserrors';
    const SESSION_FORM_KEY = 'fsform';

    public static $sessionMessage;
    public static $sessionErrors;
    public static $sessionForm;

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
        session_start();
        http_response_code(200);
        self::$sessionMessage = isset($_SESSION[self::SESSION_MESSAGE_KEY]) ? $_SESSION[self::SESSION_MESSAGE_KEY] : false;
        self::$sessionErrors = isset($_SESSION[self::SESSION_ERRORS_KEY]) ? $_SESSION[self::SESSION_ERRORS_KEY] : false;
        self::$sessionForm = isset($_SESSION[self::SESSION_FORM_KEY]) ? $_SESSION[self::SESSION_FORM_KEY] : false;
        unset($_SESSION[self::SESSION_MESSAGE_KEY]);
        unset($_SESSION[self::SESSION_ERRORS_KEY]);
        unset($_SESSION[self::SESSION_FORM_KEY]);
        $this->_modules = [];
        $this->_plugins = [];
        $this->_initInjector();
        $this->_initDebug();
        $this->_initConfig($appJsonConfig);
        $this->_initDb();
        $this->_initPluginsModules();
    }

    public function run()
    {
        $this->_invokeAllPluginsMethod('preRoute');
        $this->_setupRoutesFromConfigAndResolveRoute();
        $this->_invokeAllPluginsMethod('postRoute');
        $this->_invokeAllPluginsMethod('preModule');
        $this->_runResovledModule();
        $this->_invokeAllPluginsMethod('postModule');
        $this->_invokeAllPluginsMethod('preController');
        $this->_runResolvedControllerAction();
        $this->_invokeAllPluginsMethod('postController');
    }

    public function loadConfig($pathToJsonConfig)
    {
        $this->_config->addConfigFile($pathToJsonConfig);
    }

    public function addPlugin($pluginClass)
    {
        if (!isset($this->_plugins[$pluginClass])) {
            $plugin = new $pluginClass();
            if (!$plugin instanceof Plugin) {
                throw new StudioException('Plugin not instance of \Fire\Studio\Plugin.');
            }

            $plugin->config();
            $this->_plugins[$pluginClass] = $plugin;
            $this->_debug->getPanel(FireBugPanelPlugins::ID)->addPlugin($pluginClass, debug_backtrace());
        }
    }

    public function getPlugin($pluginClass)
    {
        return isset($this->_plugins[$pluginClass]) ? $this->_plugins[$pluginClass] : false;
    }

    public function addModule($moduleClass)
    {
        if (!isset($this->_modules[$moduleClass])) {
            $module = new $moduleClass();
            if (!$module instanceof Module) {
                throw new StudioException('Module not instance of \Fire\Studio\Module.');
            }

            $module->config();
            $this->_modules[$moduleClass] = $module;
            $this->_debug->getPanel(FireBugPanelModules::ID)->addModule($moduleClass, debug_backtrace());
        }
    }

    public function getModule($moduleClass)
    {
        return isset($this->_modules[$moduleClass]) ? $this->_modules[$moduleClass] : false;
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
         $this->_debug->addPanel(new FireBugPanelPlugins());
         $this->_debug->addPanel(new FireBugPanelModules());
         $this->_debug->addPanel(new FireBugPanelRouter());
         $this->_debug->addPanel(new FireBugPanelSession());
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

    private function _initPluginsModules($addedPlugins = [], $addedModules = [])
    {
        $config = $this->_config->getConfig();
        //load plugins from config
        $plugins = (isset($config->plugins)) ? $config->plugins : [];
        foreach ($plugins as $plugin) {
            if (!$this->getPlugin($plugin)) {
                $this->addPlugin($plugin);
            }
        }
        //load modules from config
        $modules = (isset($config->modules)) ? $config->modules : [];
        foreach ($modules as $module) {
            if (!$this->getModule($module)) {
                $this->addModule($module);
            }
        }
        //since a plugin or module could load in a config and change the plugins
        //or modules we want to load in we will run this method again just to
        //make sure we got all the plugins.
        if ($plugins !== $addedPlugins || $modules !== $addedModules) {
            $this->_initPluginsModules($plugins, $modules);
        }
    }

    /**
     * ================================================================================
     * Run Processes
     * ================================================================================
     */

    private function _setupRoutesFromConfigAndResolveRoute()
    {
        $config = $this->_config->getConfig();
        $routes = (isset($config->routes)) ? $config->routes : [];
        foreach ($routes as $id => $route) {
            $this->_router->when($route->path, $route->module, $route->controller, $route->action, $id);
        }

        //resolve route
        $this->_router->resolve();

        //lazy load module from resolved route.
        $moduleClass = $this->_router->getModule();
        if ($moduleClass) {
            $this->addModule($moduleClass);
        }
        //load all modules
        $this->_invokeAllModulesMethod('load');
    }

    private function _runResovledModule()
    {
        $moduleClass = $this->_router->getModule();
        $module = $this->getModule($moduleClass);
        if ($module) {
            $module->run();
        }
    }

    private function _runResolvedControllerAction()
    {
        $controllerClass = $this->_router->getController();
        $method = $this->_router->getRequestMethod();
        $action = $this->_router->getAction();
        if ($controllerClass && $action) {
            $controller = new $controllerClass();
            $controller->run();
            $controller->{$action}();
            $controller->postRun();
        }

        //module::postRun()
        $moduleClass = $this->_router->getModule();
        $module = $this->getModule($moduleClass);
        if ($module) {
            $module->postRun();
        }
    }

    /**
     * ================================================================================
     * Misc Methods
     * ================================================================================
     */

    private function _invokeAllPluginsMethod($method)
    {
        foreach ($this->_plugins as $plugin) {
            $plugin->{$method}();
        }
    }

    private function _invokeAllModulesMethod($method)
    {
        foreach ($this->_modules as $module) {
            $module->{$method}();
        }
    }

}
