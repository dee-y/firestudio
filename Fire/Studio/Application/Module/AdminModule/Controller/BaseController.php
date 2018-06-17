<?php

namespace Fire\Studio\Application\Module\AdminModule\Controller;

use \Fire\Studio\Controller;
use \Fire\Studio\Application\Module\AdminModule;

abstract class BaseController extends Controller
{

    public function run()
    {
        $this->model->title = 'FireStudio Admin';
        $this->setLayout(AdminModule::TEMPLATE_ADMIN_LAYOUT);
    }

}
