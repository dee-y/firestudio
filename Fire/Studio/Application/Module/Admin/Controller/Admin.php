<?php

namespace Fire\Studio\Application\Module\Admin\Controller;

use \Fire\Studio\Controller;
use \Fire\Studio\Application\Module\Admin as AdminModule;

class Admin extends Controller {

    public function init()
    {
        $this->model->title = 'FireStudio Admin';
    }

    public function dashboard()
    {
        $this->loadTemplate(
            AdminModule::PARTIAL_ADMIN_PAGE,
            __DIR__ . '/../Template/admin/dashboard.phtml',
            true
        );

        $this->model->title = 'FireStudio Admin:Dashboard';
        echo $this->render(AdminModule::TEMPLATE_ADMIN_LAYOUT);
    }

}
