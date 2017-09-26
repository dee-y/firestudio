<?php

namespace firestudio;

use PDO;
use firestudio\error;
use firestudio\debugPanel;
use firestudio\helpers\router;

class fire {

    use \firestudio\traits\injector;

    const INJECTOR_CONFIG = 'firestudio.config';
    const INJECTOR_DATABASE = 'firestudio.db';
    const INJECTOR_DEBUG_PANEL = 'firestudio.debugPanel';
    const INJECTOR_ERRORS = 'firestudio.constants.errors';
    const INJECTOR_ROUTER = 'firestudio.router';
    const INJECTOR_SQL = 'firestudio.constants.sql';

    protected $_config;
    protected $_db;
    protected $_debugPanel;
    protected $_errors;
    protected $_router;
    protected $_sql;

    protected $_resolvedRoute;

    public function __construct() {
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

    public function run() {
        $this->_resolveRoute();
        $this->_initController();
        $this->_closeDbConnection();
    }

    public function admin() {
        $this->_closeDbConnection();
    }

    /**
     * ================================================================================
     * Startup Processes
     * ================================================================================
     */

     protected function _initDebugPanel() {
         $this->injector->set(self::INJECTOR_DEBUG_PANEL, new debugPanel());
         $this->_debugPanel = $this->injector->get(self::INJECTOR_DEBUG_PANEL);
         $this->_debugPanel->addPanel(__DIR__ . '/templates/debug/panels/debuggers.phtml');
         $this->_debugPanel->addPanel(__DIR__ . '/templates/debug/panels/injector.phtml');
         $this->_debugPanel->addPanel(__DIR__ . '/templates/debug/panels/config.phtml');
         $this->_debugPanel->addPanel(__DIR__ . '/templates/debug/panels/constants.phtml');
         $this->_debugPanel->addPanel(__DIR__ . '/templates/debug/panels/router.phtml');
     }

     protected function _initConstants() {
         $errors = include __DIR__ . '/const/errors.php';
         $this->injector->set(self::INJECTOR_ERRORS, $errors);
         $this->_errors = $this->injector->get(self::INJECTOR_ERRORS);
         $this->_debugPanel->constErrors = $this->_errors;

         $sql = include __DIR__ . '/const/sql.php';
         $this->injector->set(self::INJECTOR_SQL, $sql);
         $this->_sql = $this->injector->get(self::INJECTOR_SQL);
         $this->_debugPanel->constSql = $this->_sql;
    }

    protected function _initConfig() {
        $configFile = __DIR__ . '/../config.php';
        if (file_exists($configFile)) {
            $config = include $configFile;
            $this->injector->set(self::INJECTOR_CONFIG, $config);
            $this->_config = $this->injector->get(self::INJECTOR_CONFIG);
            $this->_debugPanel->config = $this->_config;
        }
    }

    protected function _initDbConnection() {
        // try {
            $db = '';
            $this->injector->set(self::INJECTOR_DATABASE, $db);
            $this->_db = $this->injector->get(self::INJECTOR_DATABASE);
        // } catch(Exception $e) {
        //
        // }
    }

    protected function _initRouter() {
        $this->injector->set(self::INJECTOR_ROUTER, new router());
        $this->_router = $this->injector->get(self::INJECTOR_ROUTER);
        $this->_router->otherwise('firestudio\controllers\content', 'index');
    }

    protected function _checkInstall() {
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

    protected function _initPageDebug() {
        debugger(!empty($this->_config['debug']));
        if (!empty($this->_config['debug'])) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
    }

    protected function _initPlugins() {

    }

    /**
     * ================================================================================
     * Run Processes
     * ================================================================================
     */

    protected function _resolveRoute() {
        $this->_resolvedRoute = $this->_router->resolve();
        $this->_debugPanel->routeConfig = $this->_router->getRouteConfig();
        $this->_debugPanel->resolvedMatchedRoute = $this->_router->getMatchedRoute();
        $this->_debugPanel->resolvedController = $this->_resolvedRoute->controller;
        $this->_debugPanel->resolvedMethod = $this->_resolvedRoute->method;
        $this->_debugPanel->resolvedRouteVars = $this->_router->getRouteVars();
    }

    protected function _initController() {

    }

    protected function _closeDbConnection() {
        $this->injector->set(self::INJECTOR_DATABASE, null);
    }


    /**
     * ================================================================================
     * Misc Processes
     * ================================================================================
     */

    protected function _runInstall() {
        //copy config file and replace params
        $db = $this->injector->get('db');
        //$db->exec('CREATE TABLE Dogs (Id INTEGER PRIMARY KEY, Breed TEXT, Name TEXT, Age INTEGER)');
    }

}
