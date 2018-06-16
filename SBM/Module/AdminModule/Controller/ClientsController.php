<?php

namespace SBM\Module\AdminModule\Controller;

use \Fire\Studio\Controller;
use \Fire\Studio\Application\Module\AdminModule as FireStudioAdminModule;
use \Fire\Studio\Application\Module\AdminModule\Controller\AbstractAdminController;

class ClientsController extends AbstractAdminController
{
    use \Fire\Studio\Injector;

    public function init()
    {
        $this->setLayout(FireStudioAdminModule::TEMPLATE_ADMIN_LAYOUT);
    }

    public function newClient()
    {
        $this->setPageTemplate(__DIR__ . '/../Template/admin/new-client.phtml');

        $this->model->title = 'FireStudio Admin:{Client Name} Profile';
        echo $this->renderHtml();
    }

    public function clientProfile()
    {
        $this->setPageTemplate(__DIR__ . '/../Template/admin/client-profile.phtml');
        $this->model->tapToLink = '/admin/client/my-awesome-client/tap-to-link';

        $this->model->title = 'FireStudio Admin:{Client Name} Profile';
        echo $this->renderHtml();
    }

    public function pushNotifications()
    {
        $this->setPageTemplate(__DIR__ . '/../Template/admin/push-notifications.phtml');

        $this->model->title = 'FireStudio Admin:{Client Name} Profile';
        echo $this->renderHtml();
    }

    public function tapToLink()
    {
        $this->setPageTemplate(__DIR__ . '/../Template/admin/tap-to-link.phtml');

        $this->model->title = 'FireStudio Admin:{Client Name} Profile';
        echo $this->renderHtml();
    }

    public function customContent()
    {
        $this->setPageTemplate(__DIR__ . '/../Template/admin/custom-content.phtml');

        $this->model->title = 'FireStudio Admin:{Client Name} Profile';
        echo $this->renderHtml();
    }

    public function allClients()
    {
        $this->model->myAwesomeClient = '/admin/client/my-awesome-client';
        $this->model->newClientLink = '/admin/clients/new';

        $this->setPageTemplate(__DIR__ . '/../Template/admin/all-clients.phtml');

        $this->model->title = 'FireStudio Admin:All Clients';
        echo $this->renderHtml();
    }
}
