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
        $this->model->title = 'FireStudio Admin:Users';
        $this->setPageTemplate(__DIR__ . '/../Template/admin/users.phtml');

        $page = $this->getGet('page');
        $currentPage = ($page) ? $page : 1;

        $fieldMapping = (object) [
            '__id' => 'ID',
            'email' => 'Email',
            'name' => 'Full Name',
            'roles' => 'Roles'
        ];

        $actionLinks = [
            (object) [
                'label' => 'View',
                'url' => 'application.admin.users.view'
            ],
            (object) [
                'label' => 'Edit',
                'url' => 'application.admin.users.edit'
            ],
            (object) [
                'label' => 'Delete',
                'url' => 'application.admin.users.delete'
            ]
        ];

        $adminDataPanelHelper = new AdminDataPanel(
            User::COLLECTION_FSUSERS,
            'User',
            'Users',
            $fieldMapping,
            $actionLinks
        );
        $this->model->adminDataPanel = $adminDataPanelHelper->getModel($currentPage, 10);
        echo $this->renderHtml();
    }

}
