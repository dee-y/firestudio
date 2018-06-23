<?php

namespace Fire\Studio\Application\Module\AdminModule\Controller;

use \Fire\Studio\Controller;
use \Fire\Studio\Application\Model\User;
use \Fire\Studio\Application\Module\AdminModule;
use \Fire\Studio\Application\Module\AdminModule\Controller\BaseController;
use \Fire\Studio\Application\Module\AdminModule\Controller\Helper\AdminDataPanel;

class UsersController extends BaseController {

    public function index()
    {
        $this->setPageTemplate(__DIR__ . '/../Template/admin/users.phtml');

        $this->model->title = 'FireStudio Admin:Users';
        $adminDataPanelHelper = new AdminDataPanel(User::COLLECTION_FSUSERS, '{}', 'User', 'Users');
        $this->model->adminDataPanel = $adminDataPanelHelper->getModel(0, 10);
        echo $this->renderHtml();
    }

}
