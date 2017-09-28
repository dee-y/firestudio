<?php

namespace Fire;

use Fire\FireStudioException;
use Fire\Bug;
use Fire\Studio\Helper\Router;

class Studio
{

    use \Fire\Studio\Injector;

    const INJECTOR_CONFIG = 'fire.studio.config';
    const INJECTOR_DATABASE = 'fire.studio.db';
    const INJECTOR_DEBUG_PANEL = 'fire.studio.debug';
    const INJECTOR_ERRORS = 'fire.studio.constants.errors';
    const INJECTOR_ROUTER = 'fire.studio.router';

    protected $_config;
    protected $_db;
    protected $_debug;
    protected $_errors;
    protected $_router;

    protected $_resolvedRoute;

    public function __construct()
    {
        $this->_initInjector();
        $this->_initDebugPanel();
        $this->_initConstants();
        $this->_initConfig();
        $this->_initDbConnection();
        $this->_initRouter();
        $this->_checkInstall();
        $this->_initPageDebug();
        $this->_initPlugins();
    }

    public function run()
    {
        $this->_resolveRoute();
        $this->_initController();
        $this->_closeDbConnection();
    }

    public function admin()
    {
        $this->_closeDbConnection();
    }

    /**
     * ================================================================================
     * Startup Processes
     * ================================================================================
     */

     protected function _initDebugPanel()
     {
         $this->injector->set(self::INJECTOR_DEBUG_PANEL, new debugPanel());
         $this->_debug = $this->injector->get(self::INJECTOR_DEBUG_PANEL);
         $this->_debug->addPanel(__DIR__ . '/templates/debug/panels/debuggers.phtml');
         $this->_debug->addPanel(__DIR__ . '/templates/debug/panels/injector.phtml');
         $this->_debug->addPanel(__DIR__ . '/templates/debug/panels/config.phtml');
         $this->_debug->addPanel(__DIR__ . '/templates/debug/panels/constants.phtml');
         $this->_debug->addPanel(__DIR__ . '/templates/debug/panels/router.phtml');
     }

     protected function _initConstants()
     {
         $errors = include __DIR__ . '/const/errors.php';
         $this->injector->set(self::INJECTOR_ERRORS, $errors);
         $this->_errors = $this->injector->get(self::INJECTOR_ERRORS);
         $this->_debug->constErrors = $this->_errors;

         $sql = include __DIR__ . '/const/sql.php';
         $this->injector->set(self::INJECTOR_SQL, $sql);
         $this->_sql = $this->injector->get(self::INJECTOR_SQL);
         $this->_debug->constSql = $this->_sql;
    }

    protected function _initConfig()
    {
        $configFile = __DIR__ . '/../config.php';
        if (file_exists($configFile)) {
            $config = include $configFile;
            $this->injector->set(self::INJECTOR_CONFIG, $config);
            $this->_config = $this->injector->get(self::INJECTOR_CONFIG);
            $this->_debug->config = $this->_config;
        }
    }

    protected function _initDbConnection()
    {
        // try {
            $db = '';
            $this->injector->set(self::INJECTOR_DATABASE, $db);
            $this->_db = $this->injector->get(self::INJECTOR_DATABASE);
        // } catch(Exception $e) {
        //
        // }
    }

    protected function _initRouter()
    {
        $this->injector->set(self::INJECTOR_ROUTER, new router());
        $this->_router = $this->injector->get(self::INJECTOR_ROUTER);
        $this->_router->otherwise('firestudio\controllers\content', 'index');
    }

    protected function _checkInstall()
    {
        $hasConfig = !empty($this->_config);
        $hasDbConnection = !empty($this->_db);
        $hasDbTables = false;

        //if not installed properly
        if (!($hasConfig && $hasDbConnection && $hasDbTables)) {
            $adminInstallRoute = (isset($this->_config['routes']['admin.install']))
                ? $this->_config['routes']['admin.install']
                : '/admin/install/:step';
            $this->_router->when($adminInstallRoute, 'firestudio\controllers\installation', 'install');
            $this->_router->resolve();
            if ($this->_router->getMatchedRoute() !== $adminInstallRoute) {
                $route = str_replace(':step', '1', $adminInstallRoute);
                $this->_router->redirect($route, 302);
            }
        }
    }

    protected function _initPageDebug()
    {
        debugger(!empty($this->_config['debug']));
        if (!empty($this->_config['debug'])) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
    }

    protected function _initPlugins()
    {

    }

    /**
     * ================================================================================
     * Run Processes
     * ================================================================================
     */

    protected function _resolveRoute()
    {
        $this->_resolvedRoute = $this->_router->resolve();
        $this->_debug->routeConfig = $this->_router->getRouteConfig();
        $this->_debug->resolvedMatchedRoute = $this->_router->getMatchedRoute();
        $this->_debug->resolvedController = $this->_resolvedRoute->controller;
        $this->_debug->resolvedMethod = $this->_resolvedRoute->method;
        $this->_debug->resolvedRouteVars = $this->_router->getRouteVars();
    }

    protected function _initController()
    {

    }

    protected function _closeDbConnection()
    {
        $this->injector->set(self::INJECTOR_DATABASE, null);
    }


    /**
     * ================================================================================
     * Misc Processes
     * ================================================================================
     */

    protected function _runInstall()
    {
        //copy config file and replace params
        $db = $this->injector->get('db');
        //$db->exec('CREATE TABLE Dogs (Id INTEGER PRIMARY KEY, Breed TEXT, Name TEXT, Age INTEGER)');
    }

}
