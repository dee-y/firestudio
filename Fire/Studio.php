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
use \PDO;

class Studio
{

    use \Fire\Studio\Injector;

    const INJECTOR_DEBUG_PANEL = 'fire.studio.debug';
    const INJECTOR_CONFIG = 'fire.studio.config';
    const INJECTOR_DATABASE = 'fire.studio.db';
    const INJECTOR_ROUTER = 'fire.studio.router';

    private $_config;
    private $_db;
    private $_debug;
    private $_router;

    public function __construct($appJsonConfig)
    {
        $this->_fireInjector();
        $this->_initDebugPanel();
        $this->_initConfig($appJsonConfig);
        $this->_initDb();
        $this->_initRouter();
        // $this->_checkInstall();
    }

    public function addConfig($pathToJsonConfig)
    {
        $jsonConfig = file_get_contents($pathToJsonConfig);
        $this->_config->addJsonConfig($jsonConfig);
    }

    public function addModule(Module $module)
    {

    }

    public function run()
    {
        $this->_initModules();
        $this->_initPageDebug();
        // $this->_resolveRoute();
        // $this->_initController();
        // $this->_closeDbConnection();
    }

    /**
     * ================================================================================
     * Startup Processes
     * ================================================================================
     */

    private function _initDebugPanel()
    {
         //setup injector
         $this->injector->set(self::INJECTOR_DEBUG_PANEL, Bug::get());
         $this->_debug = $this->injector->get(self::INJECTOR_DEBUG_PANEL);

         //enable debugging
         $this->_debug->enable();

         //add injector debug panel
         $this->_debug->addPanel(new FireBugPanelInjector());
    }

    private function _initConfig($appConfig)
    {
        //setup injector
        $this->injector->set(self::INJECTOR_CONFIG, new Config());
        $this->_config = $this->injector->get(self::INJECTOR_CONFIG);

        //setup application configurations
        $defaultAppConfig = __DIR__ . '/Application/Config/application.json';
        $this->addConfig($defaultAppConfig);
        $this->addConfig($appConfig);

        //add config debug panel
        $this->_debug->addPanel(new FireBugPanelConfig());
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

    private function _initRouter()
    {
        $this->injector->set(self::INJECTOR_ROUTER, new Router());
        $this->_router = $this->injector->get(self::INJECTOR_ROUTER);

        $this->_debug->addPanel(new FireBugPanelRouter());
    }

    // private function _checkInstall()
    // {
    //     $hasConfig = !empty($this->_config);
    //     $hasDbConnection = !empty($this->_db);
    //     $hasDbTables = false;
    //
    //     //if not installed properly
    //     if (!($hasConfig && $hasDbConnection && $hasDbTables)) {
    //         $adminInstallRoute = (isset($this->_config['routes']['admin.install']))
    //             ? $this->_config['routes']['admin.install']
    //             : '/admin/install/:step';
    //         $this->_router->when($adminInstallRoute, 'firestudio\controllers\installation', 'install');
    //         $this->_router->resolve();
    //         if ($this->_router->getMatchedRoute() !== $adminInstallRoute) {
    //             $route = str_replace(':step', '1', $adminInstallRoute);
    //             $this->_router->redirect($route, 302);
    //         }
    //     }
    // }
    //
    // private function _initPageDebug()
    // {
    //     debugger(!empty($this->_config['debug']));
    //     if (!empty($this->_config['debug'])) {
    //         ini_set('display_errors', 1);
    //         ini_set('display_startup_errors', 1);
    //         error_reporting(E_ALL);
    //     }
    // }
    //
    // private function _initPlugins()
    // {
    //
    // }

    /**
     * ================================================================================
     * Run Processes
     * ================================================================================
     */

    // private function _resolveRoute()
    // {
    //     $this->_resolvedRoute = $this->_router->resolve();
    //     $this->_debug->routeConfig = $this->_router->getRouteConfig();
    //     $this->_debug->resolvedMatchedRoute = $this->_router->getMatchedRoute();
    //     $this->_debug->resolvedController = $this->_resolvedRoute->controller;
    //     $this->_debug->resolvedMethod = $this->_resolvedRoute->method;
    //     $this->_debug->resolvedRouteVars = $this->_router->getRouteVars();
    // }

    // private function _initController()
    // {
    //
    // }
    //
    // private function _closeDbConnection()
    // {
    //     $this->injector->set(self::INJECTOR_DATABASE, null);
    // }


    /**
     * ================================================================================
     * Misc Processes
     * ================================================================================
     */

    // private function _runInstall()
    // {
    //     //copy config file and replace params
    //     $db = $this->injector->get('db');
    //     //$db->exec('CREATE TABLE Dogs (Id INTEGER PRIMARY KEY, Breed TEXT, Name TEXT, Age INTEGER)');
    // }

}
