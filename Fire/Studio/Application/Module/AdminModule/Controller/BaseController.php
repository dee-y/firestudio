<?php

namespace Fire\Studio\Application\Module\AdminModule\Controller;

use \Fire\Studio\Controller;
use \Fire\Studio\Application\Module\AdminModule;

abstract class BaseController extends Controller
{
    private $_notFound;

    public function run()
    {
        $this->_notFound = false;
        $this->model->title = 'FireStudio Admin';
        $this->setLayout(AdminModule::TEMPLATE_ADMIN_LAYOUT);
    }

    protected function _setupNotFound()
    {
        $this->_notFound = true;
        $this->setResponceCode(404);
        $this->setPageTemplate(__DIR__ . '/../Template/admin/not-found.phtml');
    }

    protected function _isPageFound()
    {
        return !$this->_notFound;
    }

}
