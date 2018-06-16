<?php

namespace SBM\Module;

use \Fire\Studio\Application\Module\AdminModule as FireStudioAdminModule;
use \Fire\Studio\Application\Module\AdminModule\MenuItem;
use \Fire\Studio\Module;
use \Fire\Studio;

class AdminModule extends Module
{
    const ADMIN_ALL_CLIENTS_URL_ID = 'sbm.admin.allClients';

    public function config()
    {
        $this->loadConfig(__DIR__ . '/AdminModule/Config/module.json');
    }

    public function init()
    {
        if (!isset($this->model->adminMenu)) {
            $this->model->adminMenu = [];
        }
        $this->model->adminMenu[] = new MenuItem('Clients', self::ADMIN_ALL_CLIENTS_URL_ID);
    }

}
