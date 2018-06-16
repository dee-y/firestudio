<?php

namespace Fire\Studio\Application\Module\AdminModule\Controller;

use \Fire\Studio\Controller;
use \Fire\Studio\Application\Module\AdminModule;
use \Fire\Studio\Application\Module\AdminModule\Controller\AbstractAdminController;

class AdminController extends AbstractAdminController {

    public function dashboard()
    {
        $this->setPageTemplate(__DIR__ . '/../Template/admin/dashboard.phtml');
        $this->model->title = 'FireStudio Admin:Dashboard';
        echo $this->renderHtml();
    }

}
