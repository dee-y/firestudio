<?php

namespace Fire\Studio\Application\Module\AdminModule\Controller;

use \Fire\Studio\Controller;
use \Fire\Studio\Application\Module\AdminModule;
use \Fire\Studio\Application\Module\AdminModule\Controller\BaseController;
use \Fire\Studio\Application\Module\AdminModule\Controller\Helper\AdminDataPanel;

class AdminController extends BaseController {

    public function dashboard()
    {
        $this->setPageTemplate(__DIR__ . '/../Template/admin/dashboard.phtml');

        $this->model->title = 'FireStudio Admin:Dashboard';
        echo $this->renderHtml();
    }
    
}
